<?php

namespace Modules\Shops\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Entities\MealPlan;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Traits\ApiResponse;
use Exception;

class MealController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return CommonResource::collection(MealPlan::orderBy('id', 'ASC')->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (!empty($request->id)) {
            $this->validate($request, [
                'plan_name' =>  'required|string|max:100|unique:meal_plans,plan_name,' . $request->id,
                'short_name' =>  'sometimes|string|max:100|unique:meal_plans,short_name,' . $request->id,
            ]);
            try {
                $hotelcategory = MealPlan::where('id', $request->id)->first();
                $hotelcategory->update([
                    'plan_name' => $request->plan_name,
                    'short_name' => $request->short_name,
                ]);
                return $this->responseWithSuccess('Meal Plan Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'plan_name' =>  'required|string|max:100|unique:meal_plans,plan_name',
                'short_name' =>  'sometimes|string|max:100|unique:meal_plans,short_name',
            ]);
            MealPlan::create([
                'plan_name' => $request->plan_name,
                'short_name' => $request->short_name,
            ]);
            return $this->responseWithSuccess('Meal Plan added successfully!');
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
            $mealPlan = MealPlan::where('id', $id)->first();
            if (@$mealPlan) {
                return new CommonResource($mealPlan);
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
            $hotelfacility = MealPlan::where('id', $id)->first();
            if (@$hotelfacility) {
                $hotelfacility->delete();
                return $this->responseWithSuccess('Meal Plan deleted successfully');
            } else {
                return $this->responseWithError('Sorry you can\'t remove this Facility!');
            }
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
        return CommonResource::collection(MealPlan::get());
    }
}
