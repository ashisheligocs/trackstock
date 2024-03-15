<?php

namespace Modules\Shops\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomFacilitytdataResource extends JsonResource
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
            'room_facility_id' => $this->facilityId,
            "facility_name" => $this->facilityName->room_facility_title,
            "price" => $this->price,
        ];
    }
}
