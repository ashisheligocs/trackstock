<?php

namespace Modules\Shops\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;
use App\Models\VatRate;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Shops\Traits\ApiResponse;
use Exception;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Transformers\MealPriceResource;
use Modules\Shops\Transformers\HotelMealPlanResource;
use Modules\Shops\Entities\Roomfacility;
use Modules\Shops\Entities\Roomcategory;
use Modules\Shops\Entities\Room;
use Modules\Shops\Entities\HotelMealPlan;
use Modules\Shops\Entities\MealPlan;
use Modules\Shops\Entities\MealTax;
use Modules\Shops\Entities\RoomTax;
use Modules\Shops\Entities\RoomFacilitytdata;
use Modules\Shops\Http\Requests\RoomRequest;
use DB;
use Modules\Shops\Entities\HotelRoomCategory;
use Modules\Shops\Transformers\RoomResource;

class RoomsController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $rooms = Room::latest()->when(@$request->hotelId, function ($query) use ($request) {
            $query->where('hotel_id', $request->hotelId);
        })->with('Roomcategory', 'Hotel', 'Bed', 'floor')->paginate($request->perPage);

        return CommonResource::collection($rooms);
    }

    public function occupiedRooms(Request $request)
    {
        $rooms = Room::occupied()->latest()->when(@$request->hotel_id, function ($query) use ($request) {
            $query->where('hotel_id', $request->hotel_id);
        })->get();

        return CommonResource::collection($rooms);
    }

    public function checkInRooms(Request $request)
    {
        $rooms = Room::roomcheckin()->latest()->when(@$request->hotel_id, function ($query) use ($request) {
            $query->where('hotel_id', $request->hotel_id);
        })->get();

        return CommonResource::collection($rooms);
    }

    

    public function listWithStatus(Request $request)
    {
        $category = $request->category ?? '';
        $floor = $request->floor ?? '';
        $search = $request->search ?? '';
        $hotel = $request->hotel ?? '';

        $rooms = Room::query()
            ->when($hotel, function ($query) use ($hotel) {
                $query->where('hotel_id', $hotel);
            })
            ->when($category, function ($q) use ($category,$hotel) {
                $q->whereHas('Roomcategory', function ($q) use ($category) {
                    $q->where('id', $category);
                });
                $q->whereHas('hotelRoomCategory', function ($q) use ($category,$hotel) {
                    $q->where('hotel_id', $hotel);
                    $q->where('hotel_room_category_id', $category);
                });
                
            })
            ->when($floor, function ($q) use ($floor) {
                $q->whereHas('floor', function ($q) use ($floor) {
                    $q->where('id', $floor);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where('room_name', 'like', "%$search%")
                    ->orWhereHas('floor', function ($q) use ($search) {
                        $q->where('floorsName', 'like', "%$search%");
                    })
                    ->orWhereHas('roomCategory', function ($q) use ($search) {
                        $q->where('room_category_name', 'like', "%$search%");
                    });
            })
            ->orderBy('room_name', 'asc')
            ->get();
          
        return RoomResource::collection($rooms);
    }


    // get table for data.........
    // public function countWithStatus(Request $request)
    // {
    //     $category = $request->category ?? '';
    //     $floor = $request->floor ?? '';
    //     $search = $request->search ?? '';
    //     $hotel = $request->hotel ?? '';

    //     $roomCount = Room::query()
    //         ->when($hotel, function ($query) use ($hotel) {
    //             $query->where('hotel_id', $hotel);
    //         })
    //         ->when($category, function ($q) use ($category) {
    //             $q->whereHas('Roomcategory', function ($q) use ($category) {
    //                 $q->where('id', $category);
    //             });
    //         })
    //         ->when($floor, function ($q) use ($floor) {
    //             $q->whereHas('floor', function ($q) use ($floor) {
    //                 $q->where('id', $floor);
    //             });
    //         })
    //         ->when($search, function ($q) use ($search) {
    //             $q->where('room_name', 'like', "%$search%")
    //                 ->orWhereHas('floor', function ($q) use ($search) {
    //                     $q->where('floorsName', 'like', "%$search%");
    //                 })
    //                 ->orWhereHas('roomCategory', function ($q) use ($search) {
    //                     $q->where('room_category_name', 'like', "%$search%");
    //                 });
    //         })
    //         ->count();

    //     return response()->json(['room_count' => $roomCount]);
    // }



    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoomRequest $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->all());
            $category = HotelRoomCategory::where('id',$request->room_categorary)->first();
            if(!empty($category)){
                $roomCategory = $category->room_category;
            } else {
                $roomCategory = Null;
            }
            
            
            Room::updateOrCreate(['id' => $request->room_id], [
                'room_name' => $request->room_name,
                'room_categorary' => $roomCategory,
                'hotel_room_category_id' => $request->room_categorary,
                'hotel_id' => $request->hotel_id,
                'bed_type_id' => $request->bed_type_id,
                'roomdescription' => $request->roomdescription,
                'floor_id' => $request->floor_id,
            ]);
            // $explode_tax = isset($request->taxRate) ? explode(',', $request->taxRate) : [];
            // $room->taxName()->sync($explode_tax);
            DB::commit();

            return $this->responseWithSuccess($request->room_id ? 'Room Edit successfully!' : 'Room added successfully!');
        } catch (Exception $e) {
            DB::rollback();

            return $this->responseWithError($e->getMessage());
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
            $hotel = Room::where('id', $id)->with('Roomcategory', 'Hotels', 'Bed', 'taxRate.taxName')->first();
            if (@$hotel) {
                return new CommonResource($hotel);
            }

            return $this->responseWithError('Sorry you request can\'t Process!');
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
            Room::findOrFail($id)->delete();

            return $this->responseWithSuccess('Room Facility deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /*******meal function for add edit update  */
    public function addHotelMeals(Request $request)
    {

        DB::beginTransaction();
        try {
            $hotelMealPlanRate =   HotelMealPlan::updateOrCreate([
                'hotel_id' => $request->hotel_id,
                'meal_id' => $request->meal_id
            ], ['price' => $request->price]);
            $taxes = VatRate::whereIn('slug', ['sgst-2-5', 'cgst-2-5'])->pluck('id')->toArray();

            $hotelMealPlanRate->taxName()->sync($taxes);
            // MealTax
            DB::commit();
            return $this->responseWithSuccess('Meal Rate Updated successfully !');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseWithError($e->getMessage());
        }
    }
    public function getHotelMeals(Request $request)
    {
        $request = request();
        $users = MealPlan::with(['rateHotel' => function ($query) use ($request) {
            $query->where('hotel_id', $request->hotel_id);
        }, 'rateHotel.taxRate.taxName'])->paginate($request->perPage);
        return CommonResource::collection($users);
    }
    public function getHotelMealslist(Request $request)
    {
        $mealPlan = HotelMealPlan::where("hotel_id", $request->hotel_id)->with('mealname', 'taxRate', 'taxRate.taxName')->get();
        return CommonResource::collection($mealPlan);
    }
    public function getHotelMealslistOnlyMealName(Request $request)
    {
        $mealPlan = HotelMealPlan::where("hotel_id", $request->hotel_id)->with('mealname', 'taxRate', 'taxRate.taxName')->get();
        return HotelMealPlanResource::collection($mealPlan);
    }
    public function getAvailableHotelMeals(Request $request)
    {
        return CommonResource::collection(HotelMealPlan::where("hotel_id", $request->hotel_id)->where('status', 0)->paginate($request->perPage));
    }
    public function updateStatus(Request $request)
    {
        try {
            $function = HotelMealPlan::where('hotel_id', $request->hotel_id)->where('meal_id', $request->meal_id)->first();
            $function->status = $request->status;
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

     public function getRoomsDetailsAppprices(Request $request)
     {
         try {
             // $hotel = Room::where('id', $request->id)->with('taxRate.taxName')->first();
             $hotel = Room::where('id', $request->id)->with('hotelRoomCategory','hotelRoomCategory.taxRate.taxName')->first();
             $mealPlan_price = HotelMealPlan::where('meal_id', $request->meal_id)
             ->where('hotel_id', $request->hotel_id)
             ->select('id', 'price')
             ->with([
                 'taxRate.taxName' => function ($query) {
                     $query->select('id', 'rate', 'name');
                 },
             ])->first();
             if(@$hotel){ 
                 $hotel->mealPlan_price = $mealPlan_price;
            }
                return response()->json([
                    'status' => true,
                    'data' => $hotel
                ]); 
         } catch (Exception $e) {
             return $this->responseWithError($e->getMessage());
         }
     }


    public function viewHotelMeals(Request $request)
    {
        try {
            $mealPlan = HotelMealPlan::with('taxRate', 'taxRate.taxName')->where('id', $request->meal_id)->where('hotel_id', $request->hotel_id)->first();
            if (!@$mealPlan && $request->meal_id == 1) return [];
            if (@$mealPlan) {
                return new CommonResource($mealPlan);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function viewHotelMealsPrice(Request $request)
    {
        try {
            $mealPlan = HotelMealPlan::where('id', $request->meal_id)->where('hotel_id', $request->hotel_id)->with([
                'taxRate.taxName' => function ($query) {
                    $query->select('id', 'rate', 'name');
                },
                'taxRate.taxName:id,rate,name',
            ])->first();
            if (!@$mealPlan && $request->meal_id == 1) return [];
            if (@$mealPlan) {
                return new MealPriceResource($mealPlan);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function meal_mealprice(Request $request)
    {
        try {
            $mealPlan_price = HotelMealPlan::where('meal_id', $request->meal_id)
                ->where('hotel_id', $request->hotel_id)
                ->with([
                    'taxRate.taxName' => function ($query) {
                        $query->select('id', 'rate', 'name');
                    },
                ])->first();
    
            return response()->json([
                'status' => true,
                'data' => $mealPlan_price,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
}
