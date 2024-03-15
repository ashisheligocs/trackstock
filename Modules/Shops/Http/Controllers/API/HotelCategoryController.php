<?php

namespace Modules\Shops\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Shops\Transformers\CommonResource;
use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Entities\Hotelcategory;
use Exception;
use Modules\Shops\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
class HotelCategoryController extends Controller
{
    // public function __construct()
    // {
    //     // $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return CommonResource::collection(Hotelcategory::with('hotel')->orderBy('id', 'ASC')->paginate($request->perPage));
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            if (!empty($request->id)) {
                $validator = \Validator::make($request->all(), [
                    'category_name' =>  'required|string|max:100|unique:hotelcategories,category_name,' . $request->id,
                ]);
                if ($validator->fails()) {
                    return  $this->responseWithError($validator->errors());
                }
                try {
                    $hotelcategory = Hotelcategory::where('id', $request->id)->first();
                    $hotelcategory->update([
                        'category_name' => $request->category_name,
                    ]);
                    return $this->responseWithSuccess('Hotel Category Edit successfully!');
                } catch (Exception $e) {
                    return $this->responseWithError($e->getMessage());
                }
            } else {
                $validator = \Validator::make($request->all(), [
                    'category_name' =>  'required|string|max:100|unique:hotelcategories,category_name',
                ]);
                if ($validator->fails()) {
                    return  $this->responseWithError($validator->errors());
                }
                Hotelcategory::create([
                    'category_name' => $request->category_name,
                ]);
                return $this->responseWithSuccess('Hotel Category added successfully!');
            }
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
    public function show($id)
    {
        try {
            $hotelcategory = Hotelcategory::where('id', $id)->first();
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
            $hotelcategory = Hotelcategory::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Hotel Category deleted successfully');
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
        return CommonResource::collection(Hotelcategory::latest()->get());
    }
}
