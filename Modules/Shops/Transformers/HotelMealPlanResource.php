<?php

namespace Modules\Shops\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelMealPlanResource extends JsonResource
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
            "mealname" => $this->mealname->plan_name,
            'shortName' => $this->mealname->short_name,
        ];
    }
}
