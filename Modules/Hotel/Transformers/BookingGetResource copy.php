<?php

namespace Modules\Hotel\Transformers;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingGetResource extends JsonResource
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
            "id" => $this->id,
            "booking_number" => $this->booking_number,
            "customer_name" => $this->booking->customer->name,
            "customer_phone" => $this->booking->customer->phone,
            "check_in_date" =>   Carbon::parse($this->check_in_date )->format('d M y'),
            "check_out_date" =>  Carbon::parse($this->check_out_date)->format('d M y'),
            "total_price" => $this->booking->total_price,
            "dueAmount" => $this->Booking->dueAmount(),
            "adult" => $this->adult,
            "infant" => $this->infant,
            "children" => $this->children,
            "paid_amount" => $this->booking->paid_amount,
            "discount" => $this->booking->discount_amount,
            "status" => $this->getStatusName(),
        ];
        // return parent::toArray($request);
    }
}
