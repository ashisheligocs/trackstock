<?php

namespace Modules\Shops\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelPriceResource  extends JsonResource
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
            'id' => $this->id,
            "room_rate" => $this->room_rate,
            "extra_bed_rate" => $this->extra_bed_rate,
            "per_person" => $this->per_person,
            "taxRate" =>  $this->gettaxRate($this->taxRate),
            "sumOfTax" =>  $this->sumOfTax($this->taxRate),
        ];
    }

    public function gettaxRate($taxRate)
    {
        $mergedArray = collect($taxRate)->map(function ($item) {
            return [
                $item->taxName->name => $item->taxName->rate,
            ];
        })->all();

        return $mergedArray;
    }

    public function sumOfTax($taxRateArray)
    {
        // Initialize a variable to store the sum
        $sum = 0;
        // Loop through the taxRate array and calculate the sum
        foreach ($taxRateArray as $tax) {
            $taxAmount = $tax->taxName->rate;
            // Add the tax amount to the sum
            $sum += $taxAmount;
        }
        return $sum;
    }
}
