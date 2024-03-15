<?php

namespace Modules\Restaurant\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Traits\ApiResponse;
use Exception;
use Modules\Restaurant\Entities\Item;
use Modules\Restaurant\Entities\Itemprice;
use Modules\Restaurant\Entities\Restaurant;
use Modules\Restaurant\Transformers\RestaurantItemResource;

class ItemController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $term = $request->term ?? '';
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $itemsdetail = Item::with('foodCategory', 'itemPrice', 'itemPrice.varient')->latest();
        if (!empty($term)) {
            $itemsdetail->where(function ($query) use ($term) {
                $query->where('item_name', 'like', "%$term%")
                    ->orWhere('category_id', 'like', "%$term%")
                    ->orWhere('item_image', 'like', "%$term%")
                    ->orWhere('description', 'like', "%$term%")
                    ->orWhere('notes', 'like', "%$term%")
                    ->orWhere('status', 'like', "%$term%");
            });
        }
        $hotelCategoryItems = $itemsdetail->paginate($perPage, ['*'], 'page', $page);
        return CommonResource::collection($hotelCategoryItems);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (!empty($request->cat_id)) {
            $this->validate($request, [
                'item_name' =>  'required|string|max:100|unique:items,item_name,' . $request->cat_id,
                'category_id' =>  'required',
                'description' =>  'nullable',
                'notes' => 'nullable',
                'status' => 'required'
            ]);
            $imagePath = '';
            if (request()->hasFile('item_image') && request('item_image') != '') {
                $file = $request->file('item_image');
                $directory = 'storage/modules/itemImage'; // Specify the directory to store the images
                $imagePath = $this->uploadImage($file, $directory);
            }

            try {
                $hotelcategory = Item::where('id', $request->cat_id)->first();
                $hotelcategory->update([
                    'category_id' => $request->category_id,
                    'item_name' => $request->item_name,
                    'description' => $request->description,
                    'notes' => $request->notes,
                    'status' => $request->status,
                    'item_image' => !empty($imagePath) ? $imagePath : @$request->url
                ]);
                return $this->responseWithSuccess('Item Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'category_id' =>  'required|',
                'item_name' =>  'required|string|max:100|unique:items,item_name',
                'description' =>  'nullable',
                'notes' => 'nullable',
                'status' => 'required'
            ]);
            try {
                $imagePath = '';
                if (request()->hasFile('item_image') && request('item_image') != '') {
                    $file = $request->file('item_image');
                    $directory = 'storage/modules/itemImage'; // Specify the directory to store the images
                    $imagePath = $this->uploadImage($file, $directory);
                }
                Item::create([
                    'category_id' => $request->category_id,
                    'item_name' => $request->item_name,
                    'description' => $request->description,
                    'notes' => $request->notes,
                    'status' => $request->status,
                    'item_image' => $imagePath
                ]);
                return $this->responseWithSuccess('Item added successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        try {
            $hotelcategory = Item::where('id', $id)->with('foodCategory', 'itemPrice', 'itemPrice.varient')->first();
            if (@$hotelcategory) {
                return new CommonResource($hotelcategory);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $hotelcategory = Item::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Item deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function list(Request $request)
    {
       

        $restId = $request->restaurant;
        // 
        $items = Item::with(['restaurantItemPrice.variant','restaurantItemPrice' => function ($query) use ($restId) {
            $query->where('restaurant_id', $restId);
        }, 'optionalItemsPrices' => function ($query) use ($restId) {
            $query->where('restaurant_id', $restId);
        }])->get();
        // dd($items);
        // return CommonResource::collection($items);
        // $items = Item::with(['restaurantItemPrice.variant','restaurantItemPrice'])->get();    
        return RestaurantItemResource::collection($items);
    }

    public function priceUpdated(Request $request)
    {
        try {
            $countFood  = count($request->price);
            $priceValue  = $request->price;
            for ($i = 0; $i <= $countFood - 1; $i++) {
                Itemprice::updateOrCreate([
                    'item_id' => $priceValue[$i]['item_id'],
                    'varient_id' => $priceValue[$i]['varient_id']
                ], [
                    'price' => $priceValue[$i]['price']
                ]);
            }
            return $this->responseWithSuccess('Price  Updated successfully !');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    public function listWithPrice(Request $request)
    {
        $id = $request->item_id;
        return CommonResource::collection(Itemprice::where('item_id', $id)->get());
    }
    public function listForPos(Request $request)
    {
        $restaurantId = 1;
        $hotelId = @$request->hotel_id ?? 1;
        $categoryId = @$request->category ?? '';
        $restaurant = Restaurant::where('hotel_id', $hotelId)->first();
        $query = @$request->search ?? '';
        if ($restaurant) $restaurantId = $restaurant->id;

        $items = Item::whereHas('restaurantItemPrice', function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId)->where('active', 1);
        })->with('restaurantItemPrice', function ($q) use ($restaurantId) {
            $q->with('variant')->where('restaurant_id', $restaurantId);
        })->with('optionalItemsPrices', function ($q) use ($restaurantId) {
            $q->with('optionalItem')->where('restaurant_id', $restaurantId);
        })->when($categoryId, function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        })->when($query, function ($q) use ($query) {
            $q->where('item_name', 'like', "%$query%");
        })->paginate(25);

        return RestaurantItemResource::collection($items);
    }
}
