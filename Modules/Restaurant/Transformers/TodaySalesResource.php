<?php

namespace Modules\Restaurant\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TodaySalesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'product_id' => $this->restaurantItem->id,
            'name' => $this->restaurantItem->name,
            'shop' => $this->restaurantorder->shop_id,
            'orderId' => $this->restaurantorder->order_id_uniq,
            'price' => $this->restaurantItem->selling_price,
            'quantity' => $this->qty,
            'total_price' => $this->qty * $this->restaurantItem->selling_price,
            'order_date' => $this->restaurantorder->order_date
        ];
    }
}
