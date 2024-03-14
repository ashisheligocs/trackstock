<?php

namespace Modules\Hotel\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CommonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        return parent::toArray($request);
        // return [
        //     'allData' => parent::toArray($request),
        //     // 'discount_type' => $this->discountType(),
        // ];
    }
}
