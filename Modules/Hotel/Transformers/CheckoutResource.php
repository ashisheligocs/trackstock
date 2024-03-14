<?php

namespace Modules\Hotel\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Restaurant\Http\RestaurantHelper;

class CheckoutResource extends JsonResource
{
    use RestaurantHelper;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $customer = $this->Customer;
        if (!$customer) {
            $customer = $this->Source;
        }
        return  [
            "customer_details" => [
                'name' => $customer->name,
                "room_no" => $this->roomsNo(),
                'booking_no' => $this->booking_number,
                'email' => $customer->email,
                'address' => $customer->address,
                'mobile_no' => $customer->phone,
            ],
            "customer" => $this->getRoomDetails(),
            'extra_charge' => $this->extra_charge,
            'extra_charge_comment' => $this->extra_charge_comment,
            'advance_amount' => $this->advance_amount,
            'discount' => $this->discount_amount,
            'final_gst' => $this->final_gst_rates,
            'advance_remarks' => $this->advance_remarks,
            'creditAmount' => @$this->credit_amount,
            'special_discount_type' => @$this->special_discount_type,
            'special_discount' => @$this->special_discount_amount,
            'special_discount_rate' => @$this->special_discount_rate,
            'commission_amount' => @$this->commission_amount ?? 0,
            'remaining_amount' => @$this->remaining_amount ?? 0,
            'pending_amount' => @$this->paid_amount,
            'booking_source' => $this->booking_source,
            'invoicePersons' => $this->invoicePersons(),
            'restaurantOrderDetails' => $this->getBookingOrderDetails($this),
            'tax_included'      => $this->tax_included,
            'hotel_id' => $this->hotel_id
        ];
    }

    public function invoicePersons()
    {
        $persons = [];
        if ($this->Source) {
            $persons[] = [
                'id' => $this->Source->id,
                'name' => $this->Source->name,
                'phone' => $this->Source->phone,
                'agency' => 1
            ];
        }
        if ($this->Customer) {
            $persons[] = [
                'id' => $this->Customer->id,
                'name' => $this->Customer->name,
                'phone' => $this->Customer->phone,
                'agency' => 0
            ];
        }

        return $persons;
    }

    public function getRoomDetails()
    {
        $rooms = $this->BookingDetails;
        $roomsD = [];
        if (@$rooms) {
            foreach ($rooms as $room) {
                $roomsDetails = [];
                $roomsDetails["room_id"] = $room->room_id;
                $roomsDetails["id"] = $room->id;
                $roomsDetails["room_no"] = $room->room->room_name ?? '';
                $roomsDetails["booking_no"] = $this->booking_number;
                $roomsDetails["check_in_date"] = Carbon::parse($this->check_in_date)->format('d M y h:i:s A');
                $roomsDetails["check_out_date"] = Carbon::parse($this->check_out_date)->format('d M y h:i:s A');
                $roomsDetails['booking_status'] = $room->booking_status;
                $roomsD[] = $roomsDetails;
            }
        }
        return $roomsD;
    }

    public function roomsNo()
    {
        $bookingDetails = $this->BookingDetails;
        $roomNo = [];
        if (!empty($bookingDetails)) {
            foreach ($bookingDetails as $bookingDetail) {
                $roomNo[] = $bookingDetail->room_no;
            }
        }

        return implode(', ', $roomNo);
    }
}
