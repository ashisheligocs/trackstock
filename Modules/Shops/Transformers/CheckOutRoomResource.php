<?php

namespace Modules\Shops\Transformers;

use App\Models\VatRate;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckOutRoomResource extends JsonResource
{
    protected $roomTaxRate = 12;

    public function toArray($request)
    {
        $days = Carbon::parse($this->booking->check_out_date)->diffInDays(Carbon::parse($this->booking->check_in_date));
        $room_rate = floatval($this->modified_room_rate ?? $this->room->room_rate) - ((float) $this->discount / $days);
        $this->roomTaxRate = $room_rate <= 7500 ? 12 : 18;

        return [
            "room_id"           => $this->room_id,
            "room_no"           => $this->room->room_name ?? '',
            "room_category"     => $this->room->Roomcategory->room_category_name ?? '',
            "room_category_id"  => $this->room->Roomcategory->id ?? '',
            "check_in"          => Carbon::parse($this->booking->check_in_date)->format('d M y h:i A'),
            "check_out"         => Carbon::parse($this->booking->check_out_date)->format('d M y h:i A'),
            "adult_count"       => $this->adult + $this->extra_person,
            "infant_count"      => $this->infant,
            "children_count"    => $this->children,
            "booking_status"    => $this->booking_status,
            "room_rent_details" =>
                [
                    "check_in_date"         => Carbon::parse($this->booking->check_in_date)->format('d M y h:i A'),
                    "check_out_date"        => Carbon::parse($this->booking->check_out_date)->format('d M y h:i A'),
                    "days"                  => Carbon::parse($this->booking->check_out_date)->diffInDays(Carbon::parse($this->booking->check_in_date)),
                    "rent_per_day"          => $this->modified_room_rate ?? $this->room->room_rate,
                    "rent_discount"         => $this->discount,
                    "discount_per_day"      => 0,
                    'commission_amount'     => @$this->booking->commission_amount ?? 0,
                    "amount_after_discount" => $this->totalAmountWithGST,
                    "tax_details"           => $this->taxRate(),
                    "final_amount"          => $this->totalAmountWithGST,
                ],
            'complementary'     => $this->getComplementaryData(),
            'mealPlanPrice'     => $this->mealPlanPrice(),
            'mealPersons'       => @$this->meal_plan_persons ?? 0,
            'mealTax'           => $this->meal_plan_tax ?? 0,
            'bedPrice'          => $this->extra_bed_price, //$this->bedPrice(),
            'bedTax'            => $this->extra_bed_tax ?? 0,
            'perPersonPrice'    => $this->extra_person_price,  //$this->perPersonPrice(),
            'extraPersonTax'    => @$this->extra_person_tax ?? 0,
            'discount'          => @$this->discount ?? 0,
            'commission_amount' => @$this->booking->commission_amount ?? 0,
            'facilityTax'       => $this->facility_tax,
        ];
    }

    public function mealPlanPrice()
    {
        $price = @$this->mealPlanDetails->price ?? 0;
        $tax = $this->mealPlanDetails->totalTaxRate();

        return $this->booking->tax_included ? $this->inclusiveTaxAmount($price, $tax) : $price;
    }

    public function bedPrice()
    {
        $price = $this->booking->tax_included
            ? $this->inclusiveTaxAmount(@$this->room->extra_bed_rate ?? 0, $this->roomTaxRate)
            : @$this->room->extra_bed_rate ?? 0;

        return $price * (@$this->extra_bed ?? 0);
    }

    public function perPersonPrice()
    {
        $price = $this->booking->tax_included
            ? $this->inclusiveTaxAmount(@$this->room->per_person ?? 0, $this->roomTaxRate)
            : @$this->room->per_person ?? 0;

        return $price * ($this->extra_person ?? 0);
    }


    public function inclusiveTaxAmount($price, $tax)
    {
        $price = floatval($price ?? 0);
        $tax = floatval($tax ?? 0);

        return $price * (100 / (100 + $tax));
    }

    public function getComplementaryData()
    {
        $complementary = [];
        if ($this->complementrays) {
            foreach ($this->complementrays as $comp) {
                // dd($comp);
                $filteredFacilityRates = $comp->paidFacility->facilityRate->filter(function ($facilityRate) {
                    return $facilityRate->hotel_id == $this->booking->hotel->id;
                });
                // $filteredFacilityRates = $filteredFacilityRates->values();
                // dd($filteredFacilityRates);
                // // $tax = @$comp->paidFacility->facilityRate[0] ? (int) @$comp->paidFacility->facilityRate[0]->totalTaxRate() : 0;
                // // $price = @$comp->paidFacility->facilityRate[0] ? (int) @$comp->paidFacility->facilityRate[0]->price : 0;
                
                // $tax = @$filteredFacilityRates[0] ? (int) @$filteredFacilityRates[0]->totalTaxRate() : 0;
                
                // $price = @$filteredFacilityRates[0] ? (int) @$filteredFacilityRates[0]->price : 0;

                // $price = $this->booking->tax_included
                //     ? $this->inclusiveTaxAmount($price, $tax)
                //     : $price;

                // $tax = @$comp->paidFacility->facilityRate[0] ? (int) @$comp->paidFacility->facilityRate[0]->totalTaxRate() : 0;
                // $price = @$comp->paidFacility->facilityRate[0] ? (int) @$comp->paidFacility->facilityRate[0]->price : 0;
                // $price = ((int) @$comp->modified_rate * (int)  @$comp->quantity) + (((int) @$comp->modified_rate * (int)  @$comp->quantity) * $tax / 100); 
                $price = (int) @$comp->modified_rate * (int)  @$comp->quantity; 
                $complementary[] = [
                    'id'    => $comp->paidFacility->id,
                    'title' => $comp->paidFacility->room_facility_title,
                    'price' => $price,
                ];
            }
        }

        return $complementary;

    }

    public function getRoomDetails($rooms)
    {
        $roomsDetails = [];
        $roomsD = [];
        if (@$rooms) {
            foreach ($rooms as $room) {
                $roomsDetails["room_id"] = $room->room_id;
                $roomsDetails["room_no"] = $room->room->room_name;
                $roomsDetails["booking_no"] = $this->booking_number;
                $roomsDetails["booking_check_in_date"] = Carbon::parse($this->check_in_date)->format('d M y');
                $roomsDetails["booking_check_out_date"] = Carbon::parse($this->check_out_date)->format('d M y');
                $roomsD[] = $roomsDetails;
            }

            return $roomsD;
        }
    }

    public function roomsNo($bookingDetails)
    {
        $roomNo = [];
        if (!empty($bookingDetails)) {
            foreach ($bookingDetails as $bookingDetail) {
                $roomNo[] = $bookingDetail->room_no;
            }
        }

        return implode(', ', $roomNo);
    }


    public function taxRate()
    {
        $days = Carbon::parse($this->booking->check_out_date)->diffInDays(Carbon::parse($this->booking->check_in_date));
        $room_rate = floatval($this->modified_room_rate ?? $this->room->room_rate) - (float) $this->booking->commission_amount - ((float) $this->discount / $days);

        if ($room_rate <= 7500) {
            $taxRateArray = VatRate::where('rate', 6)->get();
        } else {
            $taxRateArray = VatRate::where('rate', 9)->get();
        }
        // Initialize a variable to store the sum
        $taxr = [];
        $taxs = [];
        // Loop through the taxRate array and calculate the sum
        foreach ($taxRateArray as $tax) {
            $gstRate = $tax->rate;
            $gstAmount = ($room_rate * $gstRate) / 100;
            $taxr[] = ['type' => $tax->name, 'amount' => $gstAmount];
            $taxs[] = $taxr;
        }

        return $taxr;
    }


    public function sumOfTax()
    {
        // Initialize a variable to store the sum
        $days = Carbon::parse($this->booking->check_out_date)->diffInDays(Carbon::parse($this->booking->check_in_date));
        $room_rate = floatval($this->modified_room_rate ?? $this->room->room_rate) - ((float) $this->discount / $days);

        return gstSlab($room_rate);
    }


    public function totalAmountWithGST()
    {
        $days = Carbon::parse($this->booking->check_out_date)->diffInDays(Carbon::parse($this->booking->check_in_date));
        
        $gstRate = $this->sumOfTax(); // Assume 18% GST rate, you can change it to your required GST rate
        $room_rate = floatval($this->modified_room_rate ?? $this->room->room_rate) - ((float) $this->discount / $days);
        $gstAmount = ($room_rate * $gstRate) / 100; // Calculate GST amount
        // Add the GST amount to the base room rate to get the total amount including GST
        return $room_rate + $gstAmount;
    }
}
