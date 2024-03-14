<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client_name' => $this->client->name,
            'client_id' => $this->client_id,
            'coupon' => $this->coupon_code,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'expiry' => $this->expiry,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'hotels' => $this->getHotelName('name'),
            'hotel_id' => $this->getHotelName('ids')
        ];
    }

    public function getHotelName($type)
    {
        $couponHasHotel = $this->couponHotel;
        $hotelName = [];
        $hotelId = [];
        if (!empty($couponHasHotel)) {
            foreach ($couponHasHotel as $couponHasHotel) {
                $hotelId[] = $couponHasHotel->hotel_id;
                $hotelName[] = $couponHasHotel->hotel->hotel_name;
            }
        }
        if($type == 'name'){
            return implode(', ', $hotelName);
        } else {
            return $hotelId;
        }
    }
}
