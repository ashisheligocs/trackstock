<?php

namespace Modules\Restaurant\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Hotel\Transformers\CommonResource;
use Modules\Hotel\Traits\ApiResponse;
use Exception;
use Modules\Restaurant\Entities\Varient;

class VarientController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return CommonResource::collection(Varient::latest()->paginate($request->perPage));
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (!empty($request->cat_id)) {
            $this->validate($request, [
                'varient_name' =>  'required|string|max:100|unique:varients,varient_name,' . $request->cat_id,
            ]);
            try {
                $hotelcategory = Varient::where('id', $request->cat_id)->first();
                $hotelcategory->update([
                    'varient_name' => $request->varient_name,
                ]);
                return $this->responseWithSuccess('Varients Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'varient_name' =>  'required|string|max:100|unique:varients,varient_name',
            ]);
            try {
                Varient::create([
                    'varient_name' => $request->varient_name
                ]);
                return $this->responseWithSuccess('Varients added successfully!');
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
            $hotelcategory = Varient::where('id', $id)->first();
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
            $hotelcategory = Varient::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Varients deleted successfully');
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
        return CommonResource::collection(Varient::get());
    }
}
