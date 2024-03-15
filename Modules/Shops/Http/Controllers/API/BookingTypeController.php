<?php


namespace Modules\Shops\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Traits\ApiResponse;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Modules\Shops\Entities\BookingType;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Shops\Entities\Booking;
use App\Models\InvoicePayment;

class BookingTypeController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        return CommonResource::collection(BookingType::latest()->paginate($request->perPage));
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
                'bookingtypetitle' =>  'required|string|max:100|unique:booking_type,bookingtypetitle,' . $request->cat_id,
            ]);
            try {
                $hotelcategory = BookingType::where('id', $request->cat_id)->first();
                $hotelcategory->update([
                    'bookingtypetitle' => $request->bookingtypetitle,
                ]);
                return $this->responseWithSuccess('Booking Type Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'bookingtypetitle' =>  'required|string|max:100|unique:booking_type,bookingtypetitle',
            ]);
            BookingType::create([
                'bookingtypetitle' => $request->bookingtypetitle,
            ]);
            return $this->responseWithSuccess('Booking Type added successfully!');
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
            $hotelCategory = BookingType::where('id', $id)->first();
            if (@$hotelCategory) {
                return new CommonResource($hotelCategory);
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
            $hotelcategory = BookingType::where('id', $id)->first();
            if (@$hotelcategory) {
                $hotelcategory->delete();
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
            return $this->responseWithSuccess('Booking Type deleted successfully');
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
        return CommonResource::collection(BookingType::latest()->get());
    }
    public function booking_sourcecount()
    {
        $hotelCategory_data = Booking::where('booking_source', '5')->count();
        $paymentscount = InvoicePayment::where('created_by',auth()->user()->id)->count();
        $room_status = Booking::where(['booking_source' => '5', 'booking_status_main' => '2'])->count();
        $upcoming = Booking::whereDate('check_in_date', '>', Carbon::now())
            ->whereIn('booking_status_main', [Booking::BOOKED, Booking::PENDING_PAYMENT])
            ->where('booking_source', '5')
            ->count();
        return response()->json([
            'status' => true,
            'bookings_count' => $hotelCategory_data,
            'hold_count' => $room_status,
            'upcoming_count' => $upcoming,
            'payments_count' => $paymentscount,
        ]);
    }


    // public function booking_hold_status()
    // {
    //     return response()->json([
    //         'status' => true,
    //         'data' => $room_status,
    //     ]);

    // }
}
