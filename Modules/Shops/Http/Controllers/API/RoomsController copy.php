<?php

namespace Modules\Shops\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Shops\Traits\ApiResponse;
use Exception;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Entities\Roomfacility;
use Modules\Shops\Entities\Roomcategory;
use Modules\Shops\Entities\Room;
use Modules\Shops\Entities\HotelMealPlan;
use Modules\Shops\Entities\MealPlan;
use Modules\Shops\Entities\MealTax;
use Modules\Shops\Entities\RoomFacilitytdata;


class RoomsController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return CommonResource::collection(Room::latest()->with('Roomcategory', 'Hotel', 'Bed')->paginate($request->perPage));
        // return CommonResource::collection(Room::latest()->with('Roomcategory', 'Hotels', 'Bed', 'Roomfacilities')->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (!empty($request->room_id)) {
            $this->validate($request, [
                "room_name" => "required",
                "room_categorary" => "required",
                "hotel_id" => "required",
                "bed_type_id" => "required",
                "no_of_person" => "required",
                // "facilityId" => 'required',
                "room_rate" => 'required',
            ]);
            try {
                $hotel = Room::where('id', $request->room_id)->first();
                $hotel->update([
                    "room_name" => $request->room_name,
                    "room_categorary" => $request->room_categorary,
                    "hotel_id" => $request->hotel_id,
                    "bed_type_id" => $request->bed_type_id,
                    "roomdescription" => $request->roomdescription,
                    "no_of_person" => $request->no_of_person,
                    "extra_bed_capacity" => $request->extra_bed_capacity,
                    "no_of_child" => $request->no_of_child,
                    "room_rate" => $request->room_rate,
                    "extra_bed_rate" => $request->extra_bed_rate,
                    "per_person" => $request->per_person,
                    "floor_id" => $request->floor_id,
                    "no_of_infant" => $request->no_of_infant,
                ]);
                return $this->responseWithSuccess('Room Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                "room_name" => "required",
                "room_categorary" => "required",
                "hotel_id" => "required",
                "bed_type_id" => "required",
                "no_of_person" => "required",
                // "facilityId" => 'required',
                "room_rate" => 'required'
            ]);
            try {
                $roomid = Room::create([
                    "room_name" => $request->room_name,
                    "room_categorary" => $request->room_categorary,
                    "hotel_id" => $request->hotel_id,
                    "bed_type_id" => $request->bed_type_id,
                    "roomdescription" => $request->roomdescription,
                    "no_of_person" => $request->no_of_person,
                    "extra_bed_capacity" => $request->extra_bed_capacity,
                    "no_of_child" => $request->no_of_child,
                    "room_rate" => $request->room_rate,
                    "extra_bed_rate" => $request->extra_bed_rate,
                    "per_person" => $request->per_person,
                    "floor_id" => $request->floor_id,
                    "no_of_infant" => $request->no_of_infant,
                ]);

                return $this->responseWithSuccess('Room added successfully!');
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
        return view('hotel::show');
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $hotelcategory = Room::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Room Facility deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /*******meal function for add edit update  */
    public function addHotelMeals(Request $request)
    {

        try {
            $hotelMealPlanRate =   HotelMealPlan::updateOrCreate([
                'hotel_id' => $request->hotel_id,
                'meal_id' => $request->meal_id
            ], [
                'price' => $request->price
                // 'taxRate' => $request->taxRate
            ]);
            if (isset($request->taxRate) && !empty($request->taxRate) && !empty($hotelMealPlanRate)) {
                $explode_tax = explode(',', $request->taxRate);
                $count_tax = count($explode_tax);
                for ($hs = 0; $hs < $count_tax; $hs++) {
                    $function = new MealTax;
                    $function->tax_id = $explode_tax[$hs];
                    $function->meal_price_id = $hotelMealPlanRate->id;
                    $function->save();
                }
            }
            // MealTax
            return $this->responseWithSuccess('Meal Rate Updated successfully !');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    public function getHotelMeals(Request $request)
    {
        $mealPlan = HotelMealPlan::where("hotel_id", $request->hotel_id)->with('mealname', 'taxRate', 'taxRate.taxName')->paginate($request->perPage);
        return CommonResource::collection($mealPlan);
        // $request = request();
        // $users = MealPlan::with(['rateHotel' => function ($query) use ($request) {
        //     $query->where('hotel_id', $request->hotel_id);
        // }])->paginate($request->perPage);
        // $users = MealPlan::with(['rateHotel' => function ($query) use ($request) {
        //     $query->where('hotel_id', $request->hotel_id);
        // }, 'rateHotel.taxRate.taxName'])->paginate($request->perPage);
    }
    public function getAvailableHotelMeals(Request $request)
    {
        return CommonResource::collection(HotelMealPlan::where("hotel_id", $request->hotel_id)->where('status', 0)->paginate($request->perPage));
    }
    public function updateStatus(Request $request)
    {
        try {
            $function = HotelMealPlan::where('hotel_id', $request->hotel_id)->where('meal_id', $request->meal_id)->first();
            $function->status = $request->status;;
            // $function->status = !$function->status;
            $function->save();
            return $this->responseWithSuccess('Status Updated successfully !');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function viewHotelMeals(Request $request)
    {
        try {
            $mealPlan = HotelMealPlan::with('taxRate', 'taxRate.taxName')->where('meal_id', $request->meal_id)->where('hotel_id', $request->hotel_id)->first();
            if (@$mealPlan) {
                return new CommonResource($mealPlan);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
}
