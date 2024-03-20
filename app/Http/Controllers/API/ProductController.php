<?php

namespace App\Http\Controllers\API;

use Exception;
use ZipArchive;
use App\Models\Unit;
use Modules\Shops\Entities\Shop;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\VatRate;
use Illuminate\Http\Request;
use App\Models\PurchaseProduct;
use App\Models\GeneralSetting;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelReader;
use App\Http\Resources\ProductSelectResource;
use App\Http\Resources\ProductListingResource;
use App\Models\ProductTax;
use Modules\Accounts\Entities\PlutusEntries;
use Modules\Accounts\Entities\LedgerAccount;
use Intervention\Image\Facades\Image as Image;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use DateTime;
class ProductController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:product-list', ['only' => ['index', 'search']]);
        $this->middleware('can:product-create', ['only' => ['create']]);
        $this->middleware('can:product-view', ['only' => ['show']]);
        $this->middleware('can:product-edit', ['only' => ['update']]);
        $this->middleware('can:product-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->input());
        // $d = Product::with('proSubCategory.category', 'productUnit', 'productTax','productTaxRate',
        // 'productBrand')->latest()->paginate($request->perPage); 
        $search = $request->search ?? '';

        return ProductListingResource::collection(
            Product::with('proSubCategory.category', 'productUnit', 'productTax','productTaxRate',
        'productBrand')->when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%$search%");
            $q->orWhere('slug', 'LIKE', '%'.$search.'%');
            $q->orWhere('model', 'LIKE', '%'.$search.'%');
            $q->orWhere('code', 'LIKE', '%'.$search.'%');
            $q->orWhere('selling_price', 'LIKE', '%'.$search.'%');
            $q->orWhere('purchase_price', 'LIKE', '%'.$search.'%');
            $q->orWhereHas('proSubCategory', function ($newQuery) use ($search) {
                $newQuery->where('name', 'LIKE', '%'.$search.'%')
                    ->orWhereHas('category', function ($newQuery) use ($search) {
                        $newQuery->where('name', 'LIKE', '%'.$search.'%');
                    });
            });
        })
        ->latest()->paginate($request->perPage));    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->input());
        // validate request
        $this->validate($request, [
            'itemName' => 'required|string|max:255|unique:products,name',
            'itemCode' => 'required|numeric|max:99999999999|unique:products,code',
            'itemModel' => 'nullable|string|min:2|max:255',
            'barcodeSymbology' => 'required|string|max:20',
            'subCategory' => 'required',
            'brand' => 'nullable',
            'itemUnit' => 'required',
            'productTax' => 'required',
            'taxType' => 'required',
            'regularPrice' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'note' => 'nullable|string|max:255',
            'alertQuantity' => 'nullable|numeric|min:1',
        ]);
        try {
            // generate code
            $code = 1;
            if ($request->itemCode) {
                //$code = ltrim($request->itemCode, '0');
                $code = $request->itemCode;
            } else {
                $product = Product::latest()->first();
                if ($product) {
                    $code = $product->code + 1;
                }
            }

            // upload thumbnail and set the name
            $imageName = '';
            if ($request->image) {
                $imageName = time().'.'.explode('/',
                        explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
                
                if (!file_exists(public_path('images/products/'))) {
                    mkdir(public_path('images/products/'), 666, true);
                }        
                Image::make($request->image)->save(public_path('images/products/').$imageName);
            }

            $brand = $tax = $discount = null;
            if (isset($request->brand)) {
                $brand = $request->brand['id'];
            }
            // if (isset($request->productTax)) {
            //     $tax = $request->productTax['id'];
            // }
            if ($request->discount) {
                $discount = $request->discount;
            }

            // create product
            $product = Product::create([
                'name' => $request->itemName,
                'code' => $code,
                'model' => $request->itemModel,
                'barcode_symbology' => $request->barcodeSymbology,
                'sub_cat_id' => $request->subCategory['id'],
                'brand_id' => $brand,
                'unit_id' => $request->itemUnit['id'],
                'tax_id' => null,
                'tax_type' => @$request->taxType,
                'regular_price' => isset($request->regularPrice) ? $request->regularPrice : 0 ,
                'discount' => $discount,
                'note' => clean($request->note),
                'alert_qty' => $request->alertQuantity,
                'status' => $request->status,
                'image_path' => $imageName,
            ]);

            if(!empty($request->productTax)){
                foreach ($request->productTax as $key => $taxer) {   
                  
                    ProductTax::create([ 
                        'product_id' => $product->id,
                        'tax_id' => $taxer['id'],
                        'name' => $taxer['name'],
                        'rate' => $taxer['rate'],
                        'code' => $taxer['code'],
                        'amount' => ($taxer['rate'] / 100) * $request->regularPrice,
                    ]);
                }
            }

            return $this->responseWithSuccess('Product added successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        try {
            // dd($slug);
            $product = Product::where('slug', $slug)->with('proSubCategory.category')->first();

            return new ProductResource($product);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        
        $product = Product::where('slug', $slug)->first();
        // validate request
        $this->validate($request, [
            'itemName' => 'required|string|max:255|unique:products,name,'.$product->id,
            'itemCode' => 'required|numeric|max:99999|unique:products,code,'.$product->id,
            'itemModel' => 'nullable|string|min:2|max:255',
            'barcodeSymbology' => 'required|string|max:20',
            'subCategory' => 'required',
            'brand' => 'nullable',
            'itemUnit' => 'required',
            'productTax' => 'required',
            'taxType' => 'required',
            'regularPrice' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'note' => 'nullable|string|max:255',
            'alertQuantity' => 'nullable|numeric|min:1|max:1000',
        ]);
        try {
            // upload thumbnail and set the name
            $imageName = $product->image_path;
            if ($request->image) {
                if ($imageName) {
                    @unlink(public_path('images/products/'.$imageName));
                }
                $imageName = time().'.'.explode('/',
                        explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
                
                if (!file_exists(public_path('images/products/'))) {
                    mkdir(public_path('images/products/'), 666, true);
                }
                        
                Image::make($request->image)->save(public_path('images/products/').$imageName);
            }

            $brand = $product->brand_id;
            if (isset($request->brand)) {
                $brand = $request->brand['id'];
            }
            // $tax = $product->tax_id;
            // if (isset($request->productTax)) {
            //     $tax = $request->productTax['id'];
            // }
            $discount = $product->discount;
            if ($request->discount) {
                $discount = $request->discount;
            }

            // udpate product
            $product->update([
                'name' => $request->itemName,
                'code' => $request->itemCode,
                'model' => $request->itemModel,
                'barcode_symbology' => $request->barcodeSymbology,
                'sub_cat_id' => $request->subCategory['id'],
                'brand_id' => $brand,
                'unit_id' => $request->itemUnit['id'],
                'tax_id' => null,
                'tax_type' => @$request->taxType,
                'regular_price' => isset($request->regularPrice) ? $request->regularPrice : 0,
                'discount' => $discount,
                'note' => clean($request->note),
                'alert_qty' => $request->alertQuantity,
                'status' => $request->status,
                'image_path' => $imageName,
            ]);

            if(!empty($request->productTax)){
                ProductTax::where('product_id',$product->id)->delete();
                foreach ($request->productTax as $key => $taxer) {   
                  
                    ProductTax::create([ 
                        'product_id' => $product->id,
                        'tax_id' => (@$taxer['tax_id']) ? $taxer['tax_id'] : $taxer['id'],
                        'name' => $taxer['name'],
                        'rate' => $taxer['rate'],
                        'code' => $taxer['code'],
                        'amount' => ($taxer['rate'] / 100) * $request->regularPrice,
                    ]);
                }
            }
            return $this->responseWithSuccess('Product updated successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        try {
            $product = Product::where('slug', $slug)->first();
            //delete image from storage
            if ($product->image_path) {
                @unlink(public_path('images/products/'.$product->image_path));
            }
            $product->delete();

            return $this->responseWithSuccess('Product deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->term;

        $query = Product::with('proSubCategory.category')->where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('slug', 'LIKE', '%'.$term.'%')
            ->orWhere('model', 'LIKE', '%'.$term.'%')
            ->orWhere('code', 'LIKE', '%'.$term.'%')
            ->orWhere('regular_price', 'LIKE', '%'.$term.'%')
            ->orWhere('purchase_price', 'LIKE', '%'.$term.'%')
            ->orWhereHas('proSubCategory', function ($newQuery) use ($term) {
                $newQuery->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhereHas('category', function ($newQuery) use ($term) {
                        $newQuery->where('name', 'LIKE', '%'.$term.'%');
                    });
            });

        return ProductListingResource::collection($query->orderBy('code', 'ASC')->paginate($request->perPage));
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return AnonymousResourceCollection
     */
    public function searchFromPos(Request $request)
    {
        $term = $request->term;
        $query = Product::with('proSubCategory.category');
        if (isset($request->catSlug) && isset($request->subCatSlug)) {
            $subCategory = ProductSubCategory::where('slug', $request->subCatSlug)->first();
            $query = $query->where('sub_cat_id', $subCategory->id);
        } elseif (isset($request->catSlug) && !isset($request->subCatSlug)) {
            $category = ProductCategory::where('slug', $request->catSlug)->firstOrFail();
            $subCategories = ProductSubCategory::where('cat_id', $category->id)->pluck('id');
            $query = $query->whereIn('sub_cat_id', $subCategories);
        }
        $query = $query->where(function ($query) use ($term) {
            $query->where('name', 'LIKE', '%'.$term.'%')
                ->orWhere('slug', 'LIKE', '%'.$term.'%')
                ->orWhere('model', 'LIKE', '%'.$term.'%')
                ->orWhere('code', 'LIKE', '%'.$term.'%');
        });
        return ProductSelectResource::collection($query->orderBy('code', 'ASC')->limit(24)->get());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allProducts()
    {
        $products = Product::with('purchaseProducts', 'adjustmentProducts', 'invoiceProducts', 'invoiceReturnProducts',
            'productTaxRate')->where('status', 1)->latest()->get();

        return ProductSelectResource::collection($products);
    }
    
    /**
     * @return AnonymousResourceCollection
     */
    public function allProductsPaginated()
    {
        $products = Product::with('purchaseProducts', 'adjustmentProducts', 'invoiceProducts', 'invoiceReturnProducts',
            'productTax')->where('status', 1)->latest()->paginate(24);

        return ProductSelectResource::collection($products);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allProductsForSelect()
    {
        $products = Product::with('purchaseProducts', 'adjustmentProducts', 'invoiceProducts', 'invoiceReturnProducts',
            'productTax')->where('status', 1)->latest()->get();

        return ProductSelectResource::collection($products);
    }

    // generate item code
    public function generateItemCode()
    {
        // generate code
        $code = 1;
        $product = Product::latest()->first();
        if ($product) {
            $code = $product->code + 1;
        }
        $prefix = '';
        $setting = GeneralSetting::get()->where('key', 'product_prefix')->first();
        $prefix = $setting ? $setting->value : '';

        return [
            'prefix' => $prefix,
            'code' => str_pad($code, 6, '0', STR_PAD_LEFT),
        ];
    }

    // return products by sub category
    public function productsBySubCategory($catSlug, $subCatSlug)
    {
        if ($catSlug == 'all' && $subCatSlug == 'all') {
            $products = Product::latest()->get();
        } elseif ($catSlug != 'all' && $subCatSlug == 'all') {
            $category = ProductCategory::where('slug', $catSlug)->first();
            $products = Product::with('proSubCategory.category')->whereHas('proSubCategory',
                function ($newQuery) use ($category) {
                    $newQuery->whereHas('category', function ($newQuery) use ($category) {
                        $newQuery->where('id', $category->id);
                    });
                })->get();
        } else {
            $subCat = ProductSubCategory::where('slug', $subCatSlug)->first();
            $products = Product::where('sub_cat_id', $subCat->id)->latest()->get();
        }

        return ProductResource::collection($products);
    }

    // return all products by sub category
    public function allProductsBySubCategory($catSlug, $subCatSlug)
    {
        if ($catSlug == 'all' && $subCatSlug == 'all') {
            $products = Product::latest()->get();
        } elseif ($catSlug != 'all' && $subCatSlug == 'all') {
            $category = ProductCategory::where('slug', $catSlug)->first();
            $products = Product::with('proSubCategory.category')->whereHas('proSubCategory',
                function ($newQuery) use ($category) {
                    $newQuery->whereHas('category', function ($newQuery) use ($category) {
                        $newQuery->where('id', $category->id);
                    });
                })->get();
        } else {
            $subCat = ProductSubCategory::where('slug', $subCatSlug)->first();
            $products = Product::where('sub_cat_id', $subCat->id)->latest()->paginate(5);
        }

        return ProductSelectResource::collection($products);
    }

    // csv import
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:csv,txt', 'file'],
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data = SimpleExcelReader::create($file, 'csv')->getRows();
            
            $rules = [
                'purchase_date' => ['nullable'],
                'item_name' => ['required','string','max:255'],
                'model' => ['nullable','string','min:2','max:255'],
                // 'barcode_type' => ['required','string','max:20'],
                'group' => ['required'],
                'group' => ['nullable'],  
                'selling_price' => ['required','numeric','min:0'],
                'quantity' => ['nullable'],
                'alert_qty' => ['nullable','numeric','min:1'],
                'batch' => ['nullable'],
                'shop' => ['nullable'],
                'shop_address' => ['nullable'],
            ];
            
            foreach ($data as $key => $item) { 
                $formattedDate = $item['purchase_date'] ?? '';
                $item['barcode_type'] = 'CODE39';
                $date = !empty($item['purchase_date']) ? DateTime::createFromFormat('d/m/Y', $item['purchase_date']) : '';
                if ($date !== false) {
                    $formattedDate = $date->format('Y-m-d');
                } 

                $shop = Shop::where('shop_name',$item['shop'])->first();
                if(empty($shop)){
                    $shop = Shop::create([
                        "shop_name" => $item['shop'] ?? '',
                        "shop_address" => $item['shop_address'] ?? '', 
                    ]);
                }

                if(strToLower($item['group']) == 'wine'){
                    $item['sub_cat_id'] = 1;
                    $item['brand_id'] = 1;
                }else if(strToLower($item['group']) == 'vodka'){
                    $item['sub_cat_id'] = 2; 
                    $item['brand_id'] = 2; 
                }else if(strToLower($item['group']) == 'rum'){
                    $item['sub_cat_id'] = 3; 
                    $item['brand_id'] = 3; 
                }else if(strToLower($item['group']) == 'whiskey'){
                    $item['sub_cat_id'] = 4; 
                    $item['brand_id'] = 4; 
                }else if(strToLower($item['group']) == 'beer'){
                    $item['sub_cat_id'] = 5; 
                    $item['brand_id'] = 5; 
                }else if(strToLower($item['group']) == 'gin'){
                    $item['sub_cat_id'] = 6; 
                    $item['brand_id'] = 6; 
                }else if(strToLower($item['group']) == 'breezer'){
                    $item['sub_cat_id'] = 7; 
                    $item['brand_id'] = 7; 
                }else if(strToLower($item['group']) == 'tequila'){
                    $item['sub_cat_id'] = 8; 
                    $item['brand_id'] = 8; 
                }else if(strToLower($item['group']) == 'brandy'){
                    $item['sub_cat_id'] = 9; 
                    $item['brand_id'] = 9; 
                }
                
                $item['unit_id'] = 1;  
                $item['alert_qty'] = $item['alert_qty'];  
                $item['status'] = 1;  
                $item['tax_type'] = 'Exclusive';   
                $item['tax_id'] = 1;   
                $validator = Validator::make($item, $rules);
                if ($validator->passes()) {
                    $exist = Product::where('model',$item['model'])->first();
                    if(!empty($exist)){
                        Product::where('model',$item['model'])->update([
                            'selling_price' => $item['selling_price'] ?? 0
                        ]);
                        $pro = Product::where('model',$item['model'])->first();
                    }else{
                        $codeNo = 1;
                        $lastProduct = Product::latest('id')->first();
                        if ($lastProduct) {
                            $codeNo = (int) $lastProduct->code + 1;
                        }
                        $pro = Product::create([
                            'name' => @$item['item_name'],
                            'code' => @$codeNo,
                            'model' => @$item['model'],
                            'barcode_symbology' => @$item['barcode_type'],
                            'sub_cat_id' => @$item['sub_cat_id'],
                            'brand_id' => @$item['brand_id'],
                            'unit_id' => @$item['unit_id'], 
                            'tax_type' => @$item['taxType'],
                            'selling_price' => isset($item['selling_price']) ? $item['selling_price'] : 0,  
                            'alert_qty' => $item['alert_qty'] ?? 10,
                            'quantity' => $item['quantity'],
                            'status' => $item['status'],
                            'purchase_date' => $formattedDate, 
                        ]);
                    }


                    $code = 1;
                    $prevCode = Purchase::latest()->first();
                    if ($prevCode) {
                        $code = $prevCode->id + 1;
                    }
                    $userId = auth()->user()->id;
                    if($item['quantity'] > 0){ 
                        $purchase = Purchase::where('batch_id',$item['batch'])->first();
                        // if(empty($purchase)){ 
                            $purchase = Purchase::create([
                                'purchase_no' => $code,
                                'slug' => uniqid(), 
                                'supplier_id' => 1, 
                                'sub_total' => $item['selling_price'],   
                                'purchase_date' => $formattedDate, 
                                'po_date' => $formattedDate, 
                                'status' => 1,
                                'created_by' => $userId,
                                'shop_id' => $shop->id, 
                                'batch_id' =>$item['batch'],
                                'quantity'=> $item['quantity'] ?? 0 
                            ]);
                            $reason = '['.config('config.purchasePrefix').'-'.$purchase->purchase_no.'] , Entry done by '.auth()->user()->name.'';
                            $plutusId = $this->createPlutusEntry($shop->id,$reason,now(),$item['selling_price']);
                            $this->manageInventoryLedger($purchase, @$shop->id,$plutusId);
                        // } 
                        PurchaseProduct::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $pro->id,
                            'quantity' =>  $item['quantity'] ?? 0 ,
                            'purchase_price' => $item['selling_price'],
                            'unit_cost' => $item['selling_price'],
                            'tax_amount' => 0,
                        ]);
                    }
                    if(!empty($pro)){  
                        ProductTax::create([ 
                            'product_id' => $pro->id,
                            'tax_id' => 1,
                            'name' => 'SGST 0%',
                            'rate' => 0,
                            'code' => 'SGST@0',
                            'amount' => 0.00,
                        ]);
                        ProductTax::create([ 
                            'product_id' => $pro->id,
                            'tax_id' => 2,
                            'name' => 'CGST 0%',
                            'rate' => 0,
                            'code' => 'CGST@0',
                            'amount' => 0.00,
                        ]); 
                    }
                } else {
                    return response()->json([
                        'message' => $validator->errors()->first(),
                        'row_number' => $key + 1
                    ], 422);
                }
                // if($key == 100) break;
            }
            return response()->json([
                'message' => 'Supplier imported successfully'
            ]);
        }
    }

    public function incrementCode(): array
    {
        $codeNo = 1;
        $lastProduct = Product::latest('id')->first();
        if ($lastProduct) {
            $codeNo = (int) $lastProduct->code + 1;
        }
        return [
            'code' => $codeNo
        ];
    }

    protected function createPlutusEntry($hotelId, $note, $date, $amount)
    {
        $createPlutus = PlutusEntries::create([
            'shop_id' => $hotelId,
            'note' => $note,
            'date' => $date,
            'amount' => $amount,
        ]);

        return $createPlutus->id;
    }

    public function manageInventoryLedger($purchase, $hotelId,$plutusId)
    {
        //Manage Inventory
        $payableAccount = LedgerAccount::where('code', 'INVENTORY')->first();
        $payableAccount = $payableAccount->getAccoutnbalance;
        $payableAccount->balanceTransactions()->create([
            'amount' => floatval($purchase->purchaseTotal()) - floatval($purchase->discount)
                - floatval($purchase->calculated_tax ?? 0) + floatval($purchase->transport ?? 0),
            'reason' => '['.config('config.purchasePrefix').'-'.$purchase->purchase_no.'] Purchased ]',
            'type' => 1,
            'transaction_date' => now(),
            'cheque_no' => null,
            'receipt_no' => null,
            'created_by' => auth()->user()->id,
            'status' => 1,
            'purchase_id' => $purchase->id,
            'shop_id' => $hotelId,
            'plutus_entries_id' => $plutusId,
        ]);
    }


    // cxv import with template with sheet brand_id, sub_cat_id, unit_id, tax_id
    public function importTemplate(){
        // generate csv template
        $this->subCategoryImportTemplate();
        $this->brandImportTemplate();
        $this->unitImportTemplate();
        $this->taxImportTemplate();

        // zip sub-categories.csv and brands.csv and units.csv and taxes.csv
        $zip = new ZipArchive;
        $zip->open('products.zip', ZipArchive::CREATE);
        $zip->addFile(public_path('demo-csv-file/sub-categories.csv'), 'sub-categories.csv');
        $zip->addFile(public_path('demo-csv-file/brands.csv'), 'brands.csv');
        $zip->addFile(public_path('demo-csv-file/units.csv'), 'units.csv');
        $zip->addFile(public_path('demo-csv-file/taxes.csv'), 'taxes.csv');
        $zip->addFile(public_path('demo-csv-file/products.csv'), 'products.csv');
        $zip->close();

        // download zip file
        return response()->download('products.zip');

    }
    public function subCategoryImportTemplate(){
        $handle = fopen(public_path('demo-csv-file/sub-categories.csv'), 'w');
        fputcsv($handle, ['sub_cat_id','sub_category_name']);
        ProductSubCategory::chunk(2000, function ($subCategories) use ($handle) {
            foreach ($subCategories->toArray() as $subCategory) {
                fputcsv($handle, [$subCategory['id'], $subCategory['name']]);
            }
        });
        fclose($handle);

        return response()->download(public_path('demo-csv-file/sub-categories.csv'));
    }
    public function brandImportTemplate(){
        $handle = fopen(public_path('demo-csv-file/brands.csv'), 'w');
        fputcsv($handle, ['brand_id','brand_name']);
        Brand::chunk(2000, function ($brands) use ($handle) {
            foreach ($brands->toArray() as $brand) {
                fputcsv($handle, [$brand['id'], $brand['name']]);
            }
        });
        fclose($handle);

    }
    public function unitImportTemplate(){
        $handle = fopen(public_path('demo-csv-file/units.csv'), 'w');
        fputcsv($handle, ['unit_id','unit_name']);
        Unit::chunk(2000, function ($units) use ($handle) {
            foreach ($units->toArray() as $unit) {
                fputcsv($handle, [$unit['id'], $unit['name']]);
            }
        });
        fclose($handle);

    }
    public function taxImportTemplate(){
        $handle = fopen(public_path('demo-csv-file/taxes.csv'), 'w');
        fputcsv($handle, ['tax_id','tax_name']);
        VatRate::chunk(2000, function ($taxes) use ($handle) {
            foreach ($taxes->toArray() as $tax) {
                fputcsv($handle, [$tax['id'], $tax['name']]);
            }
        });
        fclose($handle);
    }

}