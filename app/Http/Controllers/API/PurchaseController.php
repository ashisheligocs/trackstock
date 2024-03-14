<?php

namespace App\Http\Controllers\API;

use App\Models\GeneralSetting;
use Exception;
use App\Models\Product;
use App\Rules\MinTotal;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use Intervention\Image\Facades\Image as Image;
use App\Models\PurchaseProduct;
use App\Rules\PurchaseTotalPaid;
use App\Models\AccountTransaction;
use App\Http\Controllers\Controller;
use App\Notifications\PurchaseNotification;
use App\Http\Resources\PurchaseListResource;
use App\Http\Resources\PurchaseProductsResource;
use App\Notifications\PurchasePaymentNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Modules\Accounts\Entities\LedgerAccount;
use Modules\Accounts\Entities\PlutusEntries;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:purchase-list', ['only' => ['index', 'search']]);
        $this->middleware('can:purchase-create', ['only' => ['create']]);
        $this->middleware('can:purchase-view', ['only' => ['show']]);
        $this->middleware('can:purchase-edit', ['only' => ['update']]);
        $this->middleware('can:purchase-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $setting = GeneralSetting::where('key', 'selected_hotel')->first();
        if ($setting && $setting->value && $setting->value !== 'all') {
            $purchase = Purchase::whereHas('hotel', function ($q) use ($setting) {
                $q->where('id', $setting->value);
            })->with('supplier', 'purchasePayments', 'purchaseTaxes', 'hotel')->latest()->paginate($request->perPage);
        } else {
            $purchase = Purchase::with('supplier', 'purchasePayments', 'purchaseTaxes', 'hotel')->latest()->paginate($request->perPage);
        }
        return PurchaseListResource::collection($purchase);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // validate request
        // dd($request->input());
        $this->validate($request, [
            'supplier' => 'required',
            'selectedProducts' => 'required|array|min:1',
            'selectedProducts.*' => 'required|distinct',
            'discount' => 'nullable|numeric|min:1|max:'.$request->subTotal,
            'transportCost' => 'nullable|numeric|min:1',
            //'orderTax' => 'required|array',
            'netTotal' => 'required|numeric|min:1',
            'poReference' => 'nullable|string|max:255',
            'paymentTerms' => 'nullable|string|max:255',
            'account' => $request->addPayment == true ? 'required' : 'nullable',
            'availableBalance' => $request->addPayment == true ? 'required|numeric' : 'nullable',
            'totalPaid' => [$request->addPayment != true ? 'nullable' : 'required', new PurchaseTotalPaid($request->availableBalance)],
            'chequeNo' => 'nullable|string|max:255',
            'receiptNo' => 'nullable|string|max:255',
            'purchaseDate' => 'nullable|date_format:Y-m-d',
            'poDate' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
            'hotel_id' => 'required'
        ]);

        try {
            // generate code
            // $taxes = $request->orderTax;

            $code = 1;
            $prevCode = Purchase::latest()->first();
            if ($prevCode) {
                $code = $prevCode->id + 1;
            }

            if (@$request->hotel_id['id']) {
                $hotel = str_replace('hotel', '', $request->hotel_id['hotel_name']);
                $hotel = str_replace('Hotel', '', $hotel);
                $hotel = Str::slug($hotel);
                $code = "$hotel-$code";
            }

            // get logged in user id
            $userId = auth()->user()->id;


            $clientsDirectory = public_path('images/clients');
            if (!File::exists($clientsDirectory)) {
                File::makeDirectory($clientsDirectory, 0755, true);
            }
            // upload thumbnail and set the name
            $imageName = null;
            if ($request->images && !empty($request->images)) {
                dd( $request->images);
                $imageName = [];
                foreach ($request->images as $image) {
                    if ($image) {
                        $name = time().$this->generateRandomString(12).'.'.$image->getClientOriginalExtension();
                        Image::make($image)->save(public_path('images/Supplier_bills/') . $name);
                        $imageName[] = $name;
                    }
                }
            }
            if(!empty($request->file)){
                $fileDetails = $_FILES['file'];
                if ($fileDetails && !empty($fileDetails)) {
                    /*Handle Multiple File Upload*/

                    for ($i = 0; $i < count($fileDetails['name']); $i++) {
                            
                            $name    = $fileDetails['name'][$i];
                            $tmpName = $fileDetails['tmp_name'][$i];
                            $type    = $fileDetails['type'][$i];
                            $error   = $fileDetails['error'][$i];
                            $size    = $fileDetails['size'][$i];

                            if ($error === 0) {
                                $name = time() . $this->generateRandomString(12) . '.jpeg';
                                $destination = public_path('images/Supplier_bills/') . $name;
                                move_uploaded_file($tmpName, $destination);
                                $imageName[] = $name;
                            }
                        }
                        // Check if there were no errors during the upload
                }
            }

            // create purchase
            $purchase = Purchase::create([
                'purchase_no' => $code,
                'slug' => uniqid(),
                'image_path' => $imageName && !empty($imageName) ? $imageName[0] : '',
                'images' => $imageName,
                'supplier_id' => $request->supplier['id'],
                'discount' => $request->discount,
                'transport' => $request->transportCost,
                'sub_total' => $request->subTotal,
                'po_reference' => $request->poReference,
                'payment_terms' => $request->paymentTerms,
                'po_date' => $request->poDate,
                'purchase_date' => $request->purchaseDate,
                'note' => clean($request->note),
                'status' => $request->status,
                'created_by' => $userId,
                'hotel_id' => @$request->hotel_id['id'],
                'tax_amount' =>$request->totalProductTax,
            ]);

            // if (!empty($taxes)) {
            //     $taxes = Arr::pluck($taxes, 'id');
            //     $purchase->purchaseTaxes()->sync($taxes);
            // }
            /*----------add in calculated gst-----------*/
            $reason = '['.config('config.purchasePrefix').'-'.$purchase->purchase_no.'] Purchase Payment sent from ['.$request->account['accountNumber'].'] Entry done by '.auth()->user()->name.'';
            $plutusId = $this->createPlutusEntry($request->hotel_id['id'],$reason,now(),$request->subTotal);
            $this->storeTax($purchase,$plutusId,$request->totalTax);

            // store purchase products
            foreach ($request->selectedProducts as $key => $selectedProduct) {
                $product = Product::where('slug', $selectedProduct['slug'])->first();

                // calculate new purchase price
                $currentStockPrice = $product->inventory_count * $product->purchase_price;
                $newStockPrice = $selectedProduct['qty'] * $selectedProduct['unitPrice'];
                $totalStockPrice = $currentStockPrice + $newStockPrice;
                $totalQty = $product->inventory_count + $selectedProduct['qty'];
                $unitCost = $totalStockPrice / $totalQty;

                // update product stock purchase price
                $product->update([
                    'purchase_price' => $unitCost,
                    'inventory_count' => $product->inventory_count + $selectedProduct['qty'],
                ]);

                PurchaseProduct::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $selectedProduct['qty'],
                    'purchase_price' => $selectedProduct['unitPrice'],
                    'unit_cost' => $selectedProduct['unitPrice'],
                    'tax_amount' => $selectedProduct['totalTax'],
                ]);
            }
            // dd($request->account['id']);

            // store transaction
            if ($request->addPayment == true) {
                $reason = '['.config('config.purchasePrefix').'-'.$purchase->purchase_no.'] Purchase Payment sent from ['.$request->account['accountNumber'].']';

                // create transaction
                $transaction = AccountTransaction::create([
                    'account_id' => $request->account['id'],
                    'amount' => $request->totalPaid,
                    'reason' => $reason,
                    'type' => 0,
                    'transaction_date' => $request->purchaseDate,
                    'cheque_no' => $request->chequeNo,
                    'receipt_no' => $request->receiptNo,
                    'created_by' => $userId,
                    'status' => $request->status,
                    'hotel_id' => @$request->hotel_id['id'],
                    'purchase_id' => $purchase->id,
                    'plutus_entries_id' => $plutusId,
                ]);

                // store purchase payment record
                PurchasePayment::create([
                    'slug' => uniqid(),
                    'purchase_id' => $purchase->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $request->totalPaid,
                    'date' => $request->purchaseDate,
                    'note' => clean($request->note),
                    'created_by' => $userId,
                    'status' => $request->status,
                ]);

            }

            //Manage account payable
            $totalPayable = floatval($purchase->purchaseTotal()) +  - floatval($purchase->discount ?? 0)
                - floatval($request->totalPaid ?? 0) + floatval($purchase->transport ?? 0) - $request->totalTax;
            if ($totalPayable > 0) {
                $payableAccount = LedgerAccount::where('code', 'ACCOUNT-PAYABLE')->first();

                $payableAccount = $payableAccount->getAccoutnbalance;
                $payableAccount->balanceTransactions()->create([
                    'amount' => $totalPayable,
                    'reason' => '['.config('config.purchasePrefix').'-'.$purchase->purchase_no.'] Purchase Payment pending ]',
                    'type' => 1,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'purchase_id' => $purchase->id,
                    'hotel_id' => @$request->hotel_id['id'],
                    'plutus_entries_id' => $plutusId,
                ]);
            }

            $this->manageInventoryLedger($purchase, @$request->hotel_id['id'],$plutusId);

            // update purchase
            if ($purchase->totalDue() == 0) {
                $purchase->update([
                    'is_paid' => 1,
                ]);
            }

            //send notification
            if ($request->isSendEmail || $request->isSendSMS) {
                $this->notifySupplier($purchase->slug, $request);
            }

            return $this->responseWithSuccess('Purchase added successfully', [
                'slug' => $purchase->slug,
            ]);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
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
            'hotel_id' => $hotelId,
            'plutus_entries_id' => $plutusId,
        ]);
    }

    public function storeTax($purchase,$plutusId,$totalTax)
    {
        // dd($purchase);
        $vat = $purchase->purchaseTaxes;
        $gstAccount = null;
        // if ($vat && count($vat)) {
//            $tax = $vat->taxCalculations()->where('type', 'purchase')->where('reference', $purchase->id)->first();
//            if ($tax) $tax->delete();
//
//            $vat->taxCalculations()->create([
//                'input' => $purchase->calculated_tax,
//                'type' => 'Purchase',
//                'reference' => $purchase->id,
//            ]);

            if ($totalTax) {
            // if (!empty($purchase->calculated_tax)) {
                $inputGst = LedgerAccount::where('code', 'GST-INPUT')->first();

                if ($inputGst) {
                    $inputGst = $inputGst->getAccoutnbalance;
                    $gst = $inputGst->balanceTransactions()->where('purchase_id', $purchase->id)->first();
                    if ($gst) $gst->delete();
                    $gstAccount = $inputGst->balanceTransactions()->create([
                        'amount' => $totalTax,
                        'reason' => '['.config('config.purchasePrefix').'-'.$purchase->purchase_no.'] Purchase tax ]',
                        'type' => 0,
                        'transaction_date' => now(),
                        'cheque_no' => null,
                        'receipt_no' => null,
                        'created_by' => auth()->user()->id,
                        'status' => 1,
                        'purchase_id' => $purchase->id,
                        'hotel_id' => $purchase->hotel_id,
                        'plutus_entries_id' => $plutusId,
                    ]);
                }
            }
        // }

        return $gstAccount;
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
            // Fetch the purchase data with eager-loaded relationships
            $purchase = Purchase::with(
                'supplier',
                'purchaseProducts.purchase',
                'purchaseReturn',
                'purchasePayments.purchasePaymentTransaction.cashbookAccount',
                'purchaseProducts.product.productUnit',
                'purchaseProducts.product.productTaxRate',
                'purchaseProducts.product.proSubCategory.category',
                'user'
            )->where('slug', $slug)->first();
    
            // Fetch tax-related data (assuming 'tax' is a relationship or attribute in the Purchase model)
            $tax = Purchase::where('slug', $slug)->first();
    
            // Add tax-related data to the purchase data
            $purchase->tax = $tax;
    
            // Return the data using a resource
            return new PurchaseProductsResource($purchase);

        } catch (Exception $e) {
            // Handle exceptions and return an error response
            return $this->responseWithError($e->getMessage());
        }
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $slug)
    {
        //dd($request->input());
        $purchase = Purchase::where('slug', $slug)->with('purchaseProducts.product')->first();
        $totalPaid = $purchase->purchaseTotalPaid();
        $minAmount = ! isset($purchase->purchaseReturn) ? $totalPaid : $totalPaid - $purchase->purchaseReturn->returnTransaction->amount;
        
        // validate request
        $this->validate($request, [
            'supplier' => 'required',
            'selectedProducts' => 'required|array|min:1',
            'selectedProducts.*' => 'required|distinct',
            // 'discount' => 'nullable|numeric|min:0|max:'.$request->rowSubTotal,
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::when($request->rowSubTotal !== null, ['max:' . $request->rowSubTotal]),
                // 'max:' . $request->rowSubTotal
            ],
            'transportCost' => 'nullable|numeric|min:1',
            // 'orderTax' => 'required',
            'netTotal' => ['required', 'numeric', new MinTotal($minAmount, $request->netTotal)],
            'poReference' => 'nullable|string|max:255',
            'paymentTerms' => 'nullable|string|max:255',
            'purchaseDate' => 'nullable|date_format:Y-m-d',
            'poDate' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
            // 'hotel_id' => 'required'
        ]);


        try {
            // dd($request->orderTax[0]['id']); exit();
            // delete current products
            $purchase->purchaseProducts->each->delete();
            // store purchase products
            foreach ($request->selectedProducts as $key => $selectedProduct) {
                $product = Product::where('slug', $selectedProduct['slug'])->first();

                // calculate new purchase price
                $currentStockPrice = $product->inventory_count * $product->purchase_price;
                $newStockPrice = $selectedProduct['qty'] * $selectedProduct['unitPrice'];
                $totalStockPrice = $currentStockPrice + $newStockPrice;
                $totalQty = $product->inventory_count + $selectedProduct['qty'];
                $unitCost = $totalStockPrice / $totalQty;

                $newInventory = $product->inventory_count - $selectedProduct['oldQty'] + $selectedProduct['qty'];

                // update product purchase price
                $product->update([
                    'purchase_price' => $unitCost,
                    'inventory_count' => $newInventory,
                ]);

                // store products
                PurchaseProduct::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $selectedProduct['qty'],
                    'purchase_price' => $selectedProduct['unitPrice'],
                    'unit_cost' => $selectedProduct['unitPrice'],
                    'tax_amount' => $selectedProduct['totalTax'],
                ]);
            }

            // update purchase
            $purchase->update([
                'supplier_id' => $request->supplier['id'],
                'discount' => $request->discount,
                'transport' => $request->transportCost,
                'sub_total' => $request->subTotal,
                'po_reference' => $request->poReference,
                'payment_terms' => $request->paymentTerms,
                'po_date' => $request->poDate,
                'purchase_date' => $request->purchaseDate,
                'note' => clean($request->note),
                'status' => $request->status,
                'hotel_id' => @$request->hotel_id['id'],
                'tax_amount' =>$request->totalProductTax,
            ]);

            $purchase->refresh();

            // $taxes = $request->orderTax;
            // if (!empty($taxes)) {
            //     $taxes = Arr::pluck($taxes, 'id');
            //     $purchase->purchaseTaxes()->sync($taxes);
            // }
            $this->updateAccountTransaction($purchase);
            return $this->responseWithSuccess('Purchase updated successfully', [
                'slug' => $purchase->slug,
            ]);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function updateAccountTransaction($purchase){
        $getAccountTransaction = AccountTransaction::where('purchase_id',$purchase->id)->get();
        
        if(!empty($getAccountTransaction)){
            foreach($getAccountTransaction as $transaction){
                $payableAccount = LedgerAccount::where('code', 'ACCOUNT-PAYABLE')->first();
                $inventoryAccount = LedgerAccount::where('code', 'INVENTORY')->first();
                $inputGst = LedgerAccount::where('code', 'GST-INPUT')->first();

                if($transaction->account_id == $inputGst->id){
                    $transaction->amount = $purchase->tax_amount;
                }
                if($transaction->account_id == $payableAccount->id){
                    $transaction->amount = floatval($purchase->sub_total ?? 0) + floatval($purchase->transport ?? 0) - floatval($purchase->discount ?? 0) - floatval($purchase->tax_amount ?? 0);
                }
                if($transaction->account_id == $inventoryAccount->id){
                    $transaction->amount = floatval($purchase->sub_total ?? 0);
                }

                $transaction->save();
            }
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
            $purchase = Purchase::where('slug', $slug)->with('purchasePayments.purchasePaymentTransaction', 'purchaseProducts.product.productUnit', 'purchaseReturn.purchaseReturnProducts', 'purchaseReturn.returnTransaction')->first();

            // delete purchase return
            $purchaseReturn = $purchase->purchaseReturn;
            if (isset($purchaseReturn)) {
                if ($purchaseReturn->transaction_id != null) {
                    $purchaseReturn->returnTransaction->delete();
                }
                // update product inventory count
                foreach ($purchaseReturn->purchaseReturnProducts as $purchaseReturnProduct) {
                    $product = $purchaseReturnProduct->product;
                    $product->update([
                        'inventory_count' => $product->inventory_count + $purchaseReturnProduct->quantity,
                    ]);
                }
                // delete return products
                $purchaseReturn->purchaseReturnProducts->each->delete();
            }

            // delete purchase
            $purchase->delete();

            return $this->responseWithSuccess('Purchase deleted successfully!');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->term;
        $query = Purchase::with('supplier', 'purchasePayments', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('purchase_date', [$request->startDate, $request->endDate]);
        }

        $query = $query->where(function ($query) use ($term) {
            $query->where('purchase_no', 'LIKE', '%'.$term.'%')
                ->orWhere('sub_total', 'LIKE', '%'.$term.'%')
                ->orWhere('transport', 'LIKE', '%'.$term.'%')
                ->orWhere('discount', 'LIKE', '%'.$term.'%')
                ->orWhere('po_reference', 'LIKE', '%'.$term.'%')
                ->orWhere('payment_terms', 'LIKE', '%'.$term.'%')
                ->orWhereHas('supplier', function ($newQuery) use ($term) {
                    $newQuery->where('name', 'LIKE', '%'.$term.'%')
                        ->orWhere('company_name', 'LIKE', '%'.$term.'%')
                        ->orWhere('phone', 'LIKE', '%'.$term.'%');
                });
        });

        return PurchaseListResource::collection($query->latest()->paginate($request->perPage));
    }

    // notify supplier
    public function notifySupplier($slug, Request $request){
        $purchase = Purchase::with('supplier', 'purchaseProducts.purchase', 'purchaseReturn', 'purchasePayments.purchasePaymentTransaction.cashbookAccount', 'purchaseProducts.product.productUnit', 'purchaseProducts.product.productTax', 'purchaseProducts.product.proSubCategory.category', 'user')->where('slug', $slug)->first();
        // send notification
        $purchase->supplier->notify(new PurchaseNotification($purchase, [
            'isSendEmail' => filter_var($request->isSendEmail, FILTER_VALIDATE_BOOLEAN),
            'isSendSMS' =>  filter_var($request->isSendSMS, FILTER_VALIDATE_BOOLEAN)
        ]));
        return 'Successfully Notified';
    }

    // store purchase payment
    public function storePurchasePayment(Request $request){

        $maxAmount = $request->selectedPurchase['due'] <= $request->account['availableBalance'] ?  $request->selectedPurchase['due']  :  $request->account['availableBalance'];
        // validate request
        $this->validate($request, [
            'selectedPurchase' => 'required|array|min:1',
            'paidAmount' => 'required|numeric|min:1|max:'.$maxAmount,
            'account' => 'required',
            'chequeNo' => 'nullable|string|max:255',
            'receiptNo' => 'nullable|string|max:255',
            'paymentDate' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
        ]);

        $purchase = Purchase::where('slug', $request->selectedPurchase['slug'])->first();
        $userId = auth()->user()->id;
        // store transaction
        $transactionID = null;
        $reason = '['.config('config.purchasePrefix').'-'.$purchase->purchase_no.'] Purchase Payment sent from ['.$request->account['accountNumber'].'] Entry done by '.auth()->user()->name.'';
        // create transaction
        $plutusId = $this->createPlutusEntry($purchase->id,$reason,now(),$request->paidAmount);

        $transaction = AccountTransaction::create([
            'account_id' => $request->account['id'],
            'amount' => $request->paidAmount,
            'reason' => $reason,
            'type' => 0,
            'cheque_no' => $request->chequeNo,
            'receipt_no' => $request->receiptNo,
            'transaction_date' => $request->paymentDate,
            'created_by' => $userId,
            'status' => $request->status,
            'hotel_id' => $purchase->hotel_id,
            'purchase_id' => $purchase->id,
            'plutus_entries_id' => $plutusId,
        ]);
        $transactionID = $transaction->id;

        //Manage account payable
        $totalPayable = $request->paidAmount;
        if ($totalPayable > 0) {
            $payableAccount = LedgerAccount::where('code', 'ACCOUNT-PAYABLE')->first();
            $payableAccount = $payableAccount->getAccoutnbalance;
            $payableAccount->balanceTransactions()->create([
                'amount' => $totalPayable,
                'reason' => '['.config('config.purchasePrefix').'-'.$purchase->purchase_no.'] Purchase Payment]',
                'type' => 0,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'purchase_id' => $purchase->id,
                'hotel_id' => $purchase->hotel_id,
                'plutus_entries_id' => $plutusId,
            ]);
        }

        // store purchase payment
        PurchasePayment::create([
            'slug' => uniqid(),
            'purchase_id' => $purchase->id,
            'amount' => $request->paidAmount,
            'transaction_id' => $transactionID,
            'date' => $request->paymentDate,
            'note' => clean($request->note),
            'created_by' => $userId,
            'status' => $request->status,
        ]);

        // update purchase
        $purchase->update([
            'is_paid' => $purchase->totalDue() <= 0 ? 1 : 0,
        ]);

        if ($request->isSendEmail || $request->isSendEmail) {
            $purchase['amount_paid'] = $request->paidAmount;
            $purchase->supplier->notify(new PurchasePaymentNotification($purchase, [
                'isSendEmail' => filter_var($request->isSendEmail, FILTER_VALIDATE_BOOLEAN),
                'isSendSMS' =>  filter_var($request->isSendSMS, FILTER_VALIDATE_BOOLEAN)
            ]));
        }

        return $this->responseWithSuccess('Supplier payment added successfully!');
    }

    /*Manage Plutus Entry*/
    protected function createPlutusEntry($hotelId, $note, $date, $amount)
    {
        $createPlutus = PlutusEntries::create([
            'hotel_id' => $hotelId,
            'note' => $note,
            'date' => $date,
            'amount' => $amount,
        ]);
        
        return $createPlutus->id;
    }
}
