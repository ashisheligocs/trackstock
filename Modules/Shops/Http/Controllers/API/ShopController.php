<?php

namespace Modules\Shops\Http\Controllers\API;

use App\Models\Scopes\SelectedHotel;
use App\Models\VatRate;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Entities\Hotel;
use Modules\Shops\Entities\Room;
use Modules\Shops\Entities\FacilityName;
use Modules\Shops\Entities\Hotelfacilityhotel;
use Modules\Shops\Transformers\HotelResource;
use Intervention\Image\Facades\Image as Image;
use Modules\Shops\Transformers\HotelPriceResource;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Traits\ApiResponse;
use Modules\Restaurant\Entities\ItemTax;
use Modules\Restaurant\Entities\Restaurant;
use Modules\Restaurant\Entities\RestaurantItem;
use Modules\Restaurant\Entities\Item;
use Modules\Shops\Entities\BookingDetails;
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

        $checkInDate = (@$request->check_in_date) ? $request->check_in_date : '';
      
        $hotel = Hotel::latest()->with('category', 'hotelroomcategory', 'hotelroomcategory.roomCategory', 'hotelroomcategory.rooms', 'facilities.room_facilitytdatas')->when($search, function ($q) use ($search) {
            $q->where('hotel_name', 'like', "%$search%")->orWhere('hotel_email', 'like', "%$search%");
        })->paginate($request->perPage);
        
        return HotelResource::collection($hotel);
    }


    public function lists(Request $request)
    {
        return CommonResource::collection(Hotel::withCount('roomFacilityData')
            ->with(['hotelroomcategory' => function ($query) {
                $query->select('hotel_id', \DB::raw('min(rate) as min_room_rate'))
                    ->groupBy('hotel_id');
            }])
            ->latest()
            ->get());
    }


    public function listWithoutScope(Request $request)
    {
        $hotels = count(auth()->user()->hotels) > 0
            ? Hotel::withoutGlobalScope(SelectedHotel::class)->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            })->latest()->get()
            : Hotel::withoutGlobalScope(SelectedHotel::class)->latest()->get();
        return CommonResource::collection($hotels);
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (!empty($request->hotel_id)) {
            $this->validate($request, [
                "hotel_name" => "required",
                "hotel_address" => "required",
                "hotelcategory_id" => "required",
                "hotel_prefix" => "required|min:2|max:5|unique:hotels,hotel_prefix," . $request->hotel_id,
                "hotel_phone" => "required|numeric|digits:10|min:1|unique:hotels,hotel_phone," . $request->hotel_id,
                "hotel_email" => 'required|email|unique:hotels,hotel_email,' . $request->hotel_id,
                "total_no_of_rooms" => "nullable",
                "no_of_floor" => "nullable",
                "contact_phone" => 'nullable|numeric|digits:10|min:1',
            ]);
            try {

                $imageName = null;
                if ($request->images && !empty($request->images)) {
                    $imageName = [];
                    foreach ($request->images as $image) {
                        if ($image) {
                            $name = time() . $this->generateRandomString(12) . '.' . $image->getClientOriginalExtension();
                            Image::make($image)->save(public_path('images/hotel/') . $name);
                            $imageName[] = $name;
                        }
                    }
                }

                if ($request->pastImages && !empty($request->pastImages)) {
                    $imageName = array_merge($imageName ?? [], $request->pastImages);
                }

                $hotel = Hotel::where('id', $request->hotel_id)->first();
                $hotel->update([
                    "hotel_name" => $request->hotel_name,
                    "hotel_address" => $request->hotel_address,
                    "hotelcategory_id" => $request->hotelcategory_id,
                    "hotel_phone" => $request->hotel_phone,
                    "hotel_phone1" => $request->hotel_phone1,
                    "hotel_email" => $request->hotel_email,
                    "hotel_prefix" => $request->hotel_prefix,
                    "total_no_of_rooms" => @$request->total_no_of_rooms,
                    "no_of_floor" => @$request->no_of_floor,
                    "contact_phone" => @$request->contact_phone,
                    "contact_name" => @$request->contact_name,
                    "state" => @$request->state,
                    "city" => @$request->city,
                    'images' =>  !empty($imageName) ? json_encode($imageName) : '',
                    'image_path' => $imageName && !empty($imageName) ? $imageName[0] : null,

                ]);
                return $this->responseWithSuccess('Hotel Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                "hotel_name" => "required",
                "hotel_address" => "required",
                "hotelcategory_id" => "required",
                "hotel_phone" => "required|numeric|digits:10|min:1|unique:hotels,hotel_phone",
                "hotel_email" => 'required|email|unique:hotels,hotel_email',
                "hotel_prefix" => "required|min:2|max:5|unique:hotels,hotel_prefix",
                "total_no_of_rooms" => "nullable",
                "no_of_floor" => "nullable",
                "contact_phone" => 'nullable|numeric|digits:10|min:1',
                //                "contact_name" => "required",
                // "hotel_facility_ids" => "required",
            ]);

            try {
                $imageName = null;
                if ($request->images && !empty($request->images)) {
                    $imageName = [];
                    foreach ($request->images as $image) {
                        if ($image) {
                            $name = time() . $this->generateRandomString(12) . '.' . $image->getClientOriginalExtension();
                            Image::make($image)->save(public_path('images/hotel/') . $name);
                            $imageName[] = $name;
                        }
                    }
                }
                $hotel = Hotel::create([
                    "hotel_name" => $request->hotel_name,
                    "hotel_address" => $request->hotel_address,
                    "hotelcategory_id" => $request->hotelcategory_id,
                    "hotel_phone" => $request->hotel_phone,
                    "hotel_phone1" => $request->hotel_phone1,
                    "hotel_email" => $request->hotel_email,
                    "hotel_prefix" => $request->hotel_prefix,
                    "total_no_of_rooms" => @$request->total_no_of_rooms,
                    "no_of_floor" => @$request->no_of_floor,
                    "contact_phone" => @$request->contact_phone,
                    "contact_name" => @$request->contact_name,
                    'images' =>  !empty($imageName) ? json_encode($imageName) : '',
                    'image_path' => $imageName && !empty($imageName) ? $imageName[0] : null,
                    "state" => @$request->state ? $request->state['value'] : '',
                    "city" => @$request->city,
                ]);
                // print_r($hotel->id);
                $lastInsertid = $hotel->id;

                $restaurant = Restaurant::create([
                    "hotel_id" => $lastInsertid,
                ]);

                $taxes = VatRate::whereIn('slug', ['cgst-2-5', 'sgst-2-5'])->get();
                foreach ($taxes as $tax) {
                    ItemTax::create([
                        'restaurant_id' => $restaurant->id,
                        'tax_id' => $tax->id
                    ]);
                }

                //set default item and price for restaurant

                $getItems = Item::get();
                if (!empty($getItems)) {
                    $i = 0;
                    foreach ($getItems as $getItem) {
                        RestaurantItem::create([
                            'restaurant_id' => $restaurant->id,
                            'item_id' => $getItem->id,
                            'varient_id' => 1,
                            'price' => 50 + $i,
                            'active' => 1
                        ]);

                        if ($i > 300) {
                            $i = 0;
                        } else {
                            $i = $i + 10;
                        }
                    }
                }

                // $hotelFacility = new Room();
                // $hotelFacility->hotel_id = $lastInsertid;
                // $hotelFacility->save();

                return $this->responseWithSuccess('Hotel added successfully!');
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
            $hotel = Hotel::where('id', $id)->with('facilities', 'category')->first();

            if (@$hotel) {
                return new HotelResource($hotel);
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
            $hotelcategory = Hotel::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Hotel Category deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function getFloor($id)
    {
        try {
            $hotel = Hotel::where('id', $id)->select('no_of_floor')->first();
            if (@$hotel) {
                return new CommonResource($hotel);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /*** get total no of rooms */
    public function getNoOfRooms($id)
    {
        try {
            $hotel = Hotel::where('id', $id)->select('total_no_of_rooms')->first();
            if (@$hotel) {
                return new CommonResource($hotel);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /***
     * * get hotel category wise
     * * */
    public function getHotelCategoryWise(Request $request)
    {
        try {
            $checkInDate = @$request->check_in_date;
            // $rooms = Room::available($checkInDate)->where('room_categorary', $request->cat_id)->where('hotel_id', $request->hotel_id)->select('room_name', 'id')->get();
            // $rooms = Room::available($checkInDate)->with('hotelRoomCategory', 'hotelRoomCategory.taxRate.taxName')->where('room_categorary', $request->cat_id)->where('hotel_id', $request->hotel_id)->get();
            // $finalRooms = [];
            // foreach ($rooms as $finalRoom) {
            //     $check = BookingDetails::where('room_id', $finalRoom->id)->whereIn('booking_status', [2, 3, 5])->count();
            //     if ($check == 0) {
            //         $finalRooms[] = $finalRoom;
            //     }
            // }
            $totalRoom = Room::with('hotelRoomCategory', 'hotelRoomCategory.taxRate.taxName')->where('room_categorary', $request->cat_id)->where('hotel_id',$request->hotel_id)->get();
                
            $rooms = Room::occupied($checkInDate)->with('hotelRoomCategory', 'hotelRoomCategory.taxRate.taxName')->where('room_categorary',$request->cat_id)->where('hotel_id',$request->hotel_id)->get();
            $finalRooms = $this->getAvailbleRoom($totalRoom,$rooms);

            if (@$rooms) {
                return new CommonResource($finalRooms);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function getAvailbleRoom($totalRooms,$occupiedRooms){
        $finalArray = [];
        
        if(count($occupiedRooms) === 0){
          $finalArray = $totalRooms; 
        } else {
            foreach ($totalRooms as $totalRoom) {
                $isAvailable = true;
                foreach ($occupiedRooms as $occupiedRoom) {
                    if ($totalRoom->id == $occupiedRoom->id) {
                        $isAvailable = false;
                        break; 
                    }
                }
                if ($isAvailable) {
                    $finalArray[] = $totalRoom;
                }
            }
        }
        return $finalArray;
    }
    
    /***
     * * get hotel category wise
     * * */
    public function getRoomsDetails(Request $request)
    {
        try {
            // $hotel = Room::where('id', $request->id)->with('taxRate.taxName')->first();
            $hotel = Room::where('id', $request->id)->with('hotelRoomCategory', 'hotelRoomCategory.taxRate.taxName')->first();
            if (@$hotel) {
                return new CommonResource($hotel);
            } else {
                return $this->responseWithError('Hotel do not have any room category !');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }



    public function getRoomsDetailsprice(Request $request)
    {
        try {

            $hotel = Room::where('id', $request->id)->with([
                'taxRate.taxName' => function ($query) {
                    $query->select('id', 'rate', 'name');
                },
                'taxRate.taxName:id,rate,name',
            ])->first();

            if (@$hotel) {
                return new HotelPriceResource($hotel);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    public function getRoomsprice_api(Request $request)
    {
        $hotel = Room::where('room_categorary', $request->id)->get();
        return response()->json([
            'status' => true,
            'data' => $hotel,
        ]);
    }
}
