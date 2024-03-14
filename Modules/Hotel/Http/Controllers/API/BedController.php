<?php

namespace Modules\Hotel\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Modules\Hotel\Transformers\CommonResource;
use Modules\Hotel\Traits\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Validator;
use Modules\Hotel\Entities\Bed;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class BedController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return CommonResource::collection(Bed::latest()->paginate($request->perPage));
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
                'bedtypetitle' =>  'required|string|max:100|unique:beds,bedtypetitle,' . $request->cat_id,
            ]);
            try {
                $hotelcategory = Bed::where('id', $request->cat_id)->first();
                $hotelcategory->update([
                    'bedtypetitle' => $request->bedtypetitle,
                ]);
                return $this->responseWithSuccess('Bed Category Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'bedtypetitle' =>  'required|string|max:100|unique:beds,bedtypetitle',
            ]);
            try {
                Bed::create([
                    'bedtypetitle' => $request->bedtypetitle,
                ]);
                return $this->responseWithSuccess('Bed Category added successfully!');
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
            $hotelcategory = Bed::where('id', $id)->first();
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
            $hotelcategory = Bed::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Bed Category deleted successfully');
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
        return CommonResource::collection(Bed::latest()->get());
    }
}
