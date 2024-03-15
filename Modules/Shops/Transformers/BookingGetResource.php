<?php

namespace Modules\Shops\Transformers;

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
        $bookingDetails = $this->BookingDetails->first();
        return  [
            "id" => $this->id,
            "booking_number" => $this->booking_number,
            "customer_name" => @$this->customer->name,
            "source_name" => @$this->source->name,
            "source_phone" => @$this->source->phone,
            "customer_phone" => @$this->customer->phone,
            "check_in_date" =>   Carbon::parse($this->check_in_date)->format('d M y'),
            "check_out_date" =>  Carbon::parse($this->check_out_date)->format('d M y'),
            "total_price" => $this->total_price,
            "roomsNo" => $this->roomsNo($this->BookingDetails),
            "mealname" => $bookingDetails ? $bookingDetails->mealPlanDetails->mealname->plan_name : '',
            "adult" => $bookingDetails ? $bookingDetails->adult : 0,
            "infant" => $bookingDetails ? $bookingDetails->infant : 0,
            "children" => $bookingDetails ? $bookingDetails->children : 0,
            "dueAmount" => $this->dueAmount(),
            "BookingDetails" => $this->BookingDetails,
            "isChecking" => $this->isChecking(),
            "paid_amount" => $this->paid_amount,
            "status" => ($this->booking_status_main == 1) ? $this->getMainBookingStatusName() : $this->getStatusName(),
            "main_status" => $this->booking_status_main,
            "discount" => $this->discount_amount,
            "extraCharge" => intval($this->extra_charge),
            "advance_amount" => floatval($this->advance_amount),
            "purpose" => $this->purpose,
            "taxIncluded" => $this->tax_included,
            "hotel_id" => $this->hotel_id,
            "gst"=> $this->final_gst_rates
        ];
    }

    public function roomsNo($bookingDetails)
    {
        $roomNo = [];
        if (!empty($bookingDetails)) {
            foreach ($bookingDetails as $bookingDetail) {
                $roomNo[] = $bookingDetail->room->room_name ?? '';
            }
        }
        $commaSeparatedString = implode(', ', $roomNo);
        return $commaSeparatedString;
    }
}
