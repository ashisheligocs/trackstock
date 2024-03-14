<?php

namespace Modules\Hotel\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Transformers\CommonResource;
use Modules\Hotel\Traits\ApiResponse;
use Exception;
use Modules\Hotel\Entities\Roomfacility;

class RoomFacilityController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $search = $request->search ?? '';

        $facilities = Roomfacility::latest()->when($search, function ($q) use ($search) {
            $q->where('room_facility_title', 'like', "%$search%");
        })->paginate($request->perPage);

        return CommonResource::collection($facilities);
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
                'room_facility_title' =>  'required|max:100|unique:room_facility,room_facility_title,' . $request->id,
                'facility_description' =>  'required',
                'facility_img' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            try {
                $roomFacility = Roomfacility::where('id', $request->id)->first();
                $roomFacility->update([
                    'room_facility_title' => $request->room_facility_title,
                    'facility_description' => $request->facility_description,
                    // 'price' => $request->price,
                ]);
                if (request()->hasFile('facility_img') && request('facility_img') != '') {
                    $this->deleteImage($roomFacility->facility_img);
                    $file = $request->file('facility_img');
                    $directory = 'modules/hotelfacilityIimg'; // Specify the directory to store the images
                    $imagePath = $this->uploadImage($file, $directory);
                    $roomFacility->update([
                        'facility_img' => $imagePath,
                    ]);
                }
                return $this->responseWithSuccess('Room Facility Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'room_facility_title' =>  'required|max:100|unique:room_facility,room_facility_title',
                'facility_description' =>  'required',
                'facility_img' => 'mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            try {
                // $file = $request->file('facility_img');
                // $directory = 'modules/hotelfacilityIimg'; // Specify the directory to store the images
                // $imagePath = $this->uploadImage($file, $directory);
                Roomfacility::create([
                    'room_facility_title' => $request->room_facility_title,
                    'facility_description' => $request->facility_description,
                ]);
                return $this->responseWithSuccess('Room Facility added successfully!');
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
            $roomFacility = Roomfacility::where('id', $id)->first();
            if (@$roomFacility) {
                return new CommonResource($roomFacility);
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
            $hotelcategory = Roomfacility::where('id', $id)->first();
            $hotelcategory->delete();
            return $this->responseWithSuccess('Room Facility deleted successfully');
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
        return CommonResource::collection(Roomfacility::latest()->get());
    }
}
