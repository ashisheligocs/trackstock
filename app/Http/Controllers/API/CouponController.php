<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponsHotel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\ClientListResource;
use App\Http\Resources\CouponListResource;
use App\Models\Client;

class CouponController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:coupon-list', ['only' => ['index', 'search']]);
        $this->middleware('can:coupon-create', ['only' => ['create']]);
        $this->middleware('can:coupon-view', ['only' => ['show']]);
        $this->middleware('can:coupon-edit', ['only' => ['update']]);
        $this->middleware('can:coupon-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return CouponListResource::collection(Coupon::with('client', 'couponHotel.hotel')->latest()->paginate($request->perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // dd($request->input());
        // validate request
        $this->validate($request, [
            'discount_type' => 'required',
            'discount_value' => 'required',
            'client_id' => 'required',
            'coupon_code' => 'required|max:255',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'expiry' => 'nullable|string|max:255',
        ]);
        try {
            // store coupon
            $coupon = Coupon::create([
                'coupon_code' => $request->coupon_code,
                'discount_type' => $request->discount_type['id'],
                'discount_value' => $request->discount_value,
                'start_time' => $request->start_date,
                'end_time' => $request->end_date,
                'expiry' => $request->expiry,
                'client_id' => $request->client_id['id']
            ]);

            if ($coupon->id) {
                if (!empty($request->hotel_id)) {
                    foreach ($request->hotel_id as $hotels) {
                        CouponsHotel::create([
                            'hotel_id' => $hotels['id'],
                            'coupon_id' => $coupon->id,
                        ]);
                    }
                }
            }

            return $this->responseWithSuccess('Coupon added successfully!');
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

    public function getAgentCoupons(Request $request)
    {   
        $user_id = $request->id;
        return CouponListResource::collection(Coupon::with('client.user','client', 'couponHotel.hotel')
            ->whereHas('client.user', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->latest()->get()); 
    }
    
    public function show($slug)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::where('id', $id)->first();
        
        // validate request
        $this->validate($request, [
            'discount_type' => 'required',
            'discount_value' => 'required',
            'client_id' => 'required',
            'coupon_code' => 'required|max:255',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'expiry' => 'nullable|string|max:255',
        ]);

        try {
            // update account
            $coupon->update([
                'coupon_code' => $request->coupon_code,
                'discount_type' => (@$request->discount_type[0]) ? @$request->discount_type[0]['id'] : $request->discount_type['id'],
                'discount_value' => $request->discount_value,
                'start_time' => $request->start_date,
                'end_time' => $request->end_date,
                'expiry' => $request->expiry,
                'client_id' => (@$request->client_id[0]) ? $request->client_id[0]['id'] : $request->client_id['id'],
            ]);


            if (!empty($request->hotel_id)) {
                $deleteOldHotel = CouponsHotel::where('coupon_id', $id)->delete();
                foreach ($request->hotel_id as $hotels) {
                    CouponsHotel::create([
                        'hotel_id' => $hotels['id'],
                        'coupon_id' => $id,
                    ]);
                }
            }

            return $this->responseWithSuccess('Coupon updated successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $coupon = Coupon::where('id', $id)->first();
            $coupon->update([
                'status' => $request->status,
            ]);
            return $this->responseWithSuccess('Coupon status update successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {

            $coupon = Coupon::where('id', $id)->first();

            /*Check Later if Coupon already in Use then can not delete*/
            // if (isset($coupon->id)) {
            //     return $this->responseWithError('Sorry you can\'t remove this coupon!');
            // }

            $coupon->delete();
            return $this->responseWithSuccess('Coupon deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->term;

        $query = Coupon::with('client', 'couponHotel.hotel');

        $query->where(function ($query) use ($term) {
            $query->where('coupon_code', 'like', '%' . $term . '%')
                ->orWhereHas('client', function ($clientQuery) use ($term) {
                    $clientQuery->where('name', 'like', '%' . $term . '%');
                })
                ->orWhereHas('couponHotel.hotel', function ($hotelQuery) use ($term) {
                    $hotelQuery->where('hotel_name', 'like', '%' . $term . '%');
                });
        });

        return CouponListResource::collection($query->latest()->paginate($request->perPage));
    }

    public function getAgents(Request $request)
    {
        return ClientListResource::collection(Client::where('type', '2')->orderBy('id', 'DESC')->get());
    }
}
