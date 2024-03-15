<?php

namespace Modules\Shops\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Transformers\MealPriceResource;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Transformers\RoomFacilitytdataResource;
use Modules\Shops\Transformers\RoomFacilityWithTaxPriceResource;
use Modules\Shops\Traits\ApiResponse;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Shops\Entities\Roomcategory;
use Modules\Shops\Entities\RoomFacilitytdata;
use Modules\Shops\Entities\Roomfacility;
use Modules\Shops\Entities\FacilityTax;
use Modules\Shops\Entities\HotelRoomCategory;
use DB;

class RoomCategoryController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return CommonResource::collection(Roomcategory::latest()->paginate($request->perPage)); 
    }


    public function get_category(Request $request){
        try{
            $rooms = Roomcategory::where('id', $request->id)->first();
            return response()->json([
                'status' => true,
                'data' => $rooms,
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => true,
                'data' => "not working",
            ]);
        }
    }
    
    public function hotelRoom(Request $request)
    {
        return CommonResource::collection(HotelRoomCategory::with('roomCategory')->where('hotel_id',$request->hotel_id)->latest()->paginate($request->perPage)); 
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (!empty($request->room_cat_id)) {
            $this->validate($request, [
                'room_category_name' =>  'required|string|max:100|unique:room_categories,room_category_name,' . $request->room_cat_id,
            ]);
            try {
                $hotelcategory = Roomcategory::where('id', $request->room_cat_id)->first();
                $hotelcategory->update([
                    'room_category_name' => $request->room_category_name,
                ]);
                return $this->responseWithSuccess('Room Category Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'room_category_name' =>  'required|string|max:100|unique:room_categories,room_category_name',
            ]);
            try {
                Roomcategory::create([
                    'room_category_name' => $request->room_category_name,
                ]);
                return $this->responseWithSuccess('Room Category added successfully!');
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
            $hotelcategory = Roomcategory::where('id', $id)->first();
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
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $hotelcategory = Roomcategory::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Room Category deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list()
    {
        return CommonResource::collection(Roomcategory::latest()->get());
    }
    /**
     * ********************************
     * ****get lsave facility price on the base of hotel list ****
     **********************************
     */
    public function saveFacility(Request $request)
    {
        DB::beginTransaction();
        try {
            $hotelFacilityRate =   RoomFacilitytdata::updateOrCreate([
                'facilityId' => $request->facility_id,
                'hotel_id' => $request->hotel_id
            ], [
                'price' => $request->price
            ]);

            $explode_tax = isset($request->taxRate) ? explode(',', $request->taxRate) : [];
            $hotelFacilityRate->taxName()->sync($explode_tax);

            DB::commit();
            return $this->responseWithSuccess('Facility Updated successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseWithError($e->getMessage());
        }
    }
    /**
     * ********************************
     * ****get lsit of facility all price with pagination on the basesd of hotel
     **********************************
     */
    public function listWithPrice(Request $request)
    {
        $request = request();
        $users = Roomfacility::with(['facilityRate' => function ($query) use ($request) {
            $query->where('hotel_id', $request->hotel_id);
        }, 'facilityRate.taxRate.taxName'])->paginate($request->perPage);

        if ($request->hotel_id !== null) {
            $count = RoomFacilitytdata::where('hotel_id', $request->hotel_id)->count();
            return response()->json([
                'data' => CommonResource::collection($users),
                'count' => $count,
            ]);
        } else {
            return CommonResource::collection($users);
        }
        return CommonResource::collection($users);
        // return CommonResource::collection(RoomFacilitytdata::where("hotel_id", $request->hotel_id)->with("facilityName")->paginate($request->perPage));
    }
    /**
     * ********************************
     * ****get lsit of facility on the basesd of hotel
     **********************************
     */
    public function listWithPriceHotel(Request $request)
    {
        $roomFacilitytdata = RoomFacilitytdata::where("hotel_id", $request->hotel_id)->with('facilityName')->get();

        return RoomFacilitytdataResource::collection($roomFacilitytdata);
    }
    /****
     ************************************
     * get signel facility price
     * ********************************
     */
    public function listWithPriceHotelsingel(Request $request)
    {
        if (empty($request->id)) return CommonResource::collection([]);
        $explode_id = explode(',', $request->id);
        $count_id = count($explode_id);
        $allRooms = [];
        for ($hs = 0; $hs < $count_id; $hs++) {
            $roomFacilitytdata = RoomFacilitytdata::where("id", $explode_id[$hs])->with('taxRate.taxname')->first();
            $allRooms[] = $roomFacilitytdata;
        }
        return CommonResource::collection($allRooms);
    }
    public function listWithPriceHotelsingelWith(Request $request)
    {
        if (empty($request->id)) return MealPriceResource::collection([]);
        $explode_id = explode(',', $request->id);
        $count_id = count($explode_id);
        $allRooms = [];
        for ($hs = 0; $hs < $count_id; $hs++) {
            $roomFacilitytdata = RoomFacilitytdata::where("id", $explode_id[$hs])->with([
                'taxRate.taxName' => function ($query) {
                    $query->select('id', 'rate', 'name');
                },
                'taxRate.taxName:id,rate,name',
            ])->first();
            $allRooms[] = $roomFacilitytdata;
        }
        return MealPriceResource::collection($allRooms);
    }

    public function getAdditionalServiceWithPrice(Request $request){
        $roomFacilitytdata = RoomFacilitytdata::where("hotel_id", $request->hotel_id)->with('facilityName','taxRate.taxName')->get();
        $formattedData = $roomFacilitytdata->map(function ($item) {
            return [
                'id' => $item->id,
                'room_facility_id' => $item->facilityId,
                "facility_name" => $item->facilityName->room_facility_title,
                "price" => $item->price,
                "taxRate" =>  $this->taxRate($item->taxRate),
                "sumOfTax" =>  $this->sumOfTax($item->taxRate),
            ];
        });

        return response()->json($formattedData);
        // return RoomFacilityWithTaxPriceResource::collection($roomFacilitytdata);
    }
    
    public function taxRate($taxRate)
    {
        return $taxRate->map(function ($item) {
            return [
                $item->taxName->name => $item->taxName->rate,
            ];
        })->all();
    }
    
    public function sumOfTax($taxRateArray)
    {
        return $taxRateArray->sum('taxName.rate');
    }

    /*Get Hotel Room Category With Price and Tax*/

    public function hotelRoomCategory(Request $request){
        
        $rooms = HotelRoomCategory::latest()->when(@$request->hotelId, function ($query) use ($request) {
            $query->where('hotel_id', $request->hotelId);
        })->with('roomCategory', 'Hotel', 'taxRate.taxName')->get();

        return CommonResource::collection($rooms);
    }

    /* Add Hotel Room Category*/

    public function addHotelRoomCategory(Request $request){
        
        if($request->input('room_category')){
            foreach($request->input('room_category') as $roomCategory){
                HotelRoomCategory::create([
                    'hotel_id' => $request->hotel_id,
                    'room_category' => $roomCategory['id'],
                    'rate' => 0,
                    'extra_bed_capacity' => 0,
                    'extra_bed_rate' => 0,
                    'extra_person_capacity' => 0,
                    'per_person' => 0,
                    'no_of_person' => 0,
                    'no_of_child' => 0,
                    'no_of_infant' => 0
                ]);
            }            
        }
    }

    /*Update Hotel Room Category*/

    public function editHotelRoomCategory(Request $request){
        
        DB::beginTransaction();
        try {
            $roomCategory = HotelRoomCategory::updateOrCreate(['id' => $request->id], [
                'room_category' => $request->room_category['id'],
                'extra_bed_capacity' => $request->extra_bed_capacity,
                'extra_person_capacity' => $request->extra_person_capacity,
                'rate' => $request->rate,
                'extra_bed_rate' => $request->extra_bed_rate,
                'per_person' => $request->per_person,
                'no_of_person' => $request->no_of_person,
                'no_of_child' => $request->no_of_child,
                'no_of_infant' => $request->no_of_infant
            ]);
            $explode_tax = isset($request->taxRate) ? explode(',', $request->taxRate) : [];
            $roomCategory->taxName()->sync($explode_tax);
            DB::commit();

            return $this->responseWithSuccess('Room Category Edit successfully!');
        } catch (Exception $e) {
            DB::rollback();

            return $this->responseWithError($e->getMessage());
        }
    }

    
}
