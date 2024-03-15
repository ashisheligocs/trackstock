<?php

namespace Modules\Shops\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Entities\Floor;
use Modules\Shops\Transformers\CommonResource;
use Illuminate\Support\Facades\Validator;
use Modules\Shops\Traits\ApiResponse;
use Exception;

class FloorController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return CommonResource::collection(Floor::orderBy('id', 'ASC')->paginate($request->perPage));
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
                'floorsName' =>  'required|string|max:100|unique:floors,floorsName,' . $request->id,
            ]);
            try {
                $hotelcategory = Floor::where('id', $request->id)->first();
                $hotelcategory->update([
                    'floorsName' => $request->floorsName,
                ]);
                return $this->responseWithSuccess('Floor Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'floorsName' =>  'required|string|max:100|unique:floors,floorsName',
            ]);
            Floor::create([
                'floorsName' => $request->floorsName,
            ]);
            return $this->responseWithSuccess('Floor added successfully!');
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
            $floor = Floor::where('id', $id)->first();
            if (@$floor) {
                return new CommonResource($floor);
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
            $hotelcategory = Floor::where('id', $id)->first();
            if (@$hotelcategory) {
                $hotelcategory->delete();
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
            return $this->responseWithSuccess('Floor deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function list()
    {
        return CommonResource::collection(Floor::get());
    }
}
