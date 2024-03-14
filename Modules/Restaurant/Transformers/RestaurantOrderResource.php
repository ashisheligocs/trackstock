<?php

namespace Modules\Restaurant\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Restaurant\Http\RestaurantHelper;

class RestaurantOrderResource extends JsonResource
{
    use RestaurantHelper;
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->order_date,
            'hotel' => $this->hotel,
            'orderId' => $this->order_id_uniq,
            'type' => $this->type ?? 'Customer order',
            'totalAmount' => $this->total_amount,
            'discount' => $this->discount ?? 0,
            'tax' => $this->tax ?? 0,
            'room' => $this->room ?? null,
            'customer' => $this->client ?? null,
            'bookingId' => $this->booking_id,
            'items' => $this->getRestaurantOrderDetails($this),
            'order_status_name' => $this->getOrderStatusName(),
            'payment_status_name' => $this->getPaymentStatusName(),
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
        ];
    }
}
