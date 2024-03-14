<?php

namespace Modules\Hotel\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoomFacilityWithTaxPriceResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */

     
    public function toArray($request)
    {
        
        return [
            'id' => $this->id,
            'room_facility_id' => $this->facilityId,
            "facility_name" => $this->facilityName->room_facility_title,
            "price" => $this->price,
            "taxRate" =>  $this->taxRate($this->taxRate),
            "sumOfTax" =>  $this->sumOfTax($this->taxRate),
        ];
    }

    public function taxRate($taxRate)
    {
        return $taxRate->map(function ($item) {
            return [
                $item->taxName->name => $item->taxName->rate,
            ];
        })->all();
    }
    
    public function sumOfTax($taxRateArray)
    {
        return $taxRateArray->sum('taxName.rate');
    }
}
