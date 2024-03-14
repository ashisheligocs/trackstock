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
            'hotel_id' => $this->hotel_id,
            'taxes' => $this->taxes,
            'restaurant_name' => $this->hotel->hotel_name,
            'restaurant_phone' => $this->hotel->hotel_phone,
            'restaurant_email' => $this->hotel->hotel_email,
        ];
    }
}
