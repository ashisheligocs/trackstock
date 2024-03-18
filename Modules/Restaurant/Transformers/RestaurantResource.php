<?php

namespace Modules\Restaurant\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'restaurant_id' => $this->id,
            'hotel_id' => $this->shop_id,
            'taxes' => $this->taxes,
            'restaurant_name' => $this->Shop->shop_name,
            'restaurant_phone' => $this->Shop->shop_phone,
            'restaurant_email' => $this->Shop->shop_email,
        ];
    }
}
