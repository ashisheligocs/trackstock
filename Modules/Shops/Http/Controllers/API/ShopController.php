<?php

namespace Modules\Shops\Http\Controllers\API;

use App\Models\Scopes\SelectedHotel;
use App\Models\VatRate;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Entities\Hotel;
use Modules\Shops\Entities\Shop;
use Modules\Shops\Transformers\HotelResource;
use Intervention\Image\Facades\Image as Image;
use Modules\Shops\Transformers\HotelPriceResource;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Traits\ApiResponse;
use Modules\Restaurant\Entities\ItemTax;
use Modules\Restaurant\Entities\Restaurant;
use Modules\Restaurant\Entities\RestaurantItem;
use Modules\Restaurant\Entities\Item;
use Exception;

class ShopController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // dd($request->input());

        $search = $request->search ?? '';
        if($request->has('search')){
            $shop = Shop::when($search, function ($q) use ($search) {
                $q->where('shop_name', 'like', "%$search%")->orWhere('shop_name', 'like', "%$search%");
            })->latest()->paginate($request->perPage);

        } else {
            $shop = count(auth()->user()->shops) > 0
            ? Shop::withoutGlobalScope(SelectedHotel::class)->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            })->latest()->get()
            : Shop::withoutGlobalScope(SelectedHotel::class)->when($search, function ($q) use ($search) {
                $q->where('shop_name', 'like', "%$search%")->orWhere('shop_name', 'like', "%$search%");
            })->latest()->paginate($request->perPage);
        }
        return CommonResource::collection($shop);
    }


    public function lists(Request $request)
    {
        return CommonResource::collection(Shop::latest()->get());
    }


    public function listWithoutScope(Request $request)
    {
        $hotels = count(auth()->user()->shops) > 0
            ? Shop::withoutGlobalScope(SelectedHotel::class)->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            })->latest()->get()
            : Shop::withoutGlobalScope(SelectedHotel::class)->latest()->get();
        return CommonResource::collection($hotels);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (!empty($request->shop_id)) {
            $this->validate($request, [
                "shop_name" => "required",
                "shop_address" => "required",
                "shop_prefix" => "nullable",
                "shop_phone" => "nullable",
                "shop_email" => "nullable",
                "contact_phone" => "nullable",
            ]);
            try {

                if (!file_exists(public_path('images/shop/'))) {
                    mkdir(public_path('images/shop/'), 666, true);
                }

                $imageName = null;
                if ($request->images && !empty($request->images)) {
                    $imageName = [];
                    foreach ($request->images as $image) {
                        if ($image) {
                            $name = time() . $this->generateRandomString(12) . '.' . $image->getClientOriginalExtension();
                            Image::make($image)->save(public_path('images/shop/') . $name);
                            $imageName[] = $name;
                        }
                    }
                }

                if ($request->pastImages && !empty($request->pastImages)) {
                    $imageName = array_merge($imageName ?? [], $request->pastImages);
                }

                $shop = Shop::where('id', $request->shop_id)->first();
                $shop->update([
                    "shop_name" => $request->shop_name,
                    "shop_address" => $request->shop_address,
                    "shop_phone" => $request->shop_phone,
                    "shop_phone1" => $request->shop_phone1,
                    "shop_email" => $request->shop_email,
                    "shop_prefix" => $request->shop_prefix,
                    "contact_phone" => @$request->contact_phone,
                    "contact_name" => @$request->contact_name,
                    "state" => @$request->state,
                    "city" => @$request->city,
                    'images' =>  !empty($imageName) ? json_encode($imageName) : '',
                    'image_path' => $imageName && !empty($imageName) ? $imageName[0] : null,

                ]);
                return $this->responseWithSuccess('Shop Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                "shop_name" => "required",
                "shop_address" => "required",
                "shop_prefix" => "nullable",
                "shop_phone" => "nullable",
                "shop_email" => "nullable",
                "contact_phone" => "nullable",
            ]);

            try {

                if (!file_exists(public_path('images/shop/'))) {
                    mkdir(public_path('images/shop/'), 666, true);
                }

                $imageName = null;
                if ($request->images && !empty($request->images)) {
                    $imageName = [];
                    foreach ($request->images as $image) {
                        if ($image) {
                            $name = time() . $this->generateRandomString(12) . '.' . $image->getClientOriginalExtension();
                            Image::make($image)->save(public_path('images/shop/') . $name);
                            $imageName[] = $name;
                        }
                    }
                }
                $shop = Shop::create([
                    "shop_name" => $request->shop_name,
                    "shop_address" => $request->shop_address,
                    "shop_phone" => $request->shop_phone,
                    "shop_phone1" => $request->shop_phone1,
                    "shop_email" => $request->shop_email,
                    "shop_prefix" => $request->shop_prefix,
                    "contact_phone" => @$request->contact_phone,
                    "contact_name" => @$request->contact_name,
                    'images' =>  !empty($imageName) ? json_encode($imageName) : '',
                    'image_path' => $imageName && !empty($imageName) ? $imageName[0] : null,
                    "state" => @$request->state ? $request->state['value'] : '',
                    "city" => @$request->city,
                ]);

                return $this->responseWithSuccess('Shop added successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage() . '--' . $e->getLine());
            }
        }
    }
    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        try {
            $hotel = Shop::where('id', $id)->first();

            if (@$hotel) {
                return new CommonResource($hotel);
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
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $shop = Shop::where('id', $id)->first();
            $shop->delete();
            return $this->responseWithSuccess('Shop Category deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
}
