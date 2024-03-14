<?php

namespace Modules\Hotel\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Hotel\Entities\Hotelfacility;
use Modules\Hotel\Transformers\CommonResource;
use Illuminate\Support\Facades\Validator;
use Modules\Hotel\Traits\ApiResponse;
use Exception;

class HotelFacilityController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {

        $facilities = Hotelfacility::latest()->paginate($request->perPage);

        return CommonResource::collection($facilities);
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
                    'facility_title' =>  'required|string|max:100|unique:hotels_facility,facility_title,' . $request->id,
                ]);
                if ($validator->fails()) {
                    return  $this->responseWithError($validator->errors());
                }
                try {
                    $hotelcategory = Hotelfacility::where('id', $request->id)->first();
                    $hotelcategory->update([
                        'facility_title' => $request->facility_title,
                    ]);
                    return $this->responseWithSuccess('Hotel Facility Edit successfully!');
                } catch (Exception $e) {
                    return $this->responseWithError($e->getMessage());
                }
            } else {
                $validator = \Validator::make($request->all(), [
                    'facility_title' =>  'required|string|max:100|unique:hotels_facility,facility_title',
                ]);
                if ($validator->fails()) {
                    return  $this->responseWithError($validator->errors());
                }
                Hotelfacility::create([
                    'facility_title' => $request->facility_title,
                ]);
                return $this->responseWithSuccess('Hotel Facility added successfully!');
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
            $hotelFacility = Hotelfacility::where('id', $id)->first();
            if (@$hotelFacility) {
                return new CommonResource($hotelFacility);
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
            $hotelfacility = Hotelfacility::where('id', $id)->first();
            if (@$hotelfacility) {
                $hotelfacility->delete();
                return $this->responseWithSuccess('Hotel Facility deleted successfully');
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
        return CommonResource::collection(Hotelfacility::latest()->get());
    }
}
