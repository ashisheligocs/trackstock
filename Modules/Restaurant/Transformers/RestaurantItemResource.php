<?php

namespace Modules\Restaurant\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantItemResource extends JsonResource
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
            'name' => $this->item_name,
            'image' => $this->item_image,
            'description' => $this->description,
            'status' => $this->status,
            'variants' => $this->variants()['variants'],
            'optionalItems' => $this->optionalItems(),
            'taxRate' => $this->variants()['tax'],
            'price' => $this->variants()['price'],
            'restaurant_item_price' => $this->restaurantItemPrice,
            'multi_price' => $this->getMultiPrice(),
            'multi_variant' => $this->getMultiVariant(),
            'active' => $this->itemStatus(),
        ];
    }

    public function itemStatus(){
        if (!empty($this->restaurantItemPrice)) {
            foreach ($this->restaurantItemPrice as $restaurantItem) {
                return $restaurantItem->active;
            }
        }
    }
    public function getMultiPrice(){
        $price = [];
        if (!empty($this->restaurantItemPrice)) {
            foreach ($this->restaurantItemPrice as $restaurantItem) {
                $price[] = $restaurantItem->price;
            }
        }
        $commaSeparatedString = implode(', ', $price);  
        return $commaSeparatedString;
    }

    public function getMultiVariant(){
        $variant = [];
        if (!empty($this->restaurantItemPrice)) {
            foreach ($this->restaurantItemPrice as $restaurantItem) {
                $variant[] = $restaurantItem->variant->varient_name;
            }
        }
        $commaSeparatedString = implode(', ', $variant); 
        return $commaSeparatedString;
    }

    public function variants()
    {
        $taxRate = 0;
        $price = 0;
        $variants = [];
        if ($this->restaurantItemPrice && !empty($this->restaurantItemPrice)) {
            foreach ($this->restaurantItemPrice as $index => $itemPrice) {
                if ($itemPrice->active) {
                    $variants[] = [
                        'id' => $itemPrice->id,
                        'name' => $itemPrice->variant->varient_name,
                        'price' => $itemPrice->price
                    ];
                    if ($itemPrice->price && $itemPrice->price < $price) $price = $itemPrice->price;
                    if ($index === 0) {
                        $taxRate = $itemPrice->taxRate();
                        $price = $itemPrice->price;
                    }
                }
            }
        }

        return ['variants' => $variants, 'tax' => $taxRate, 'price' => $price];
    }

    public function optionalItems()
    {
        $optionalItems = [];
        if ($this->optionalItemsPrices && !empty($this->optionalItemsPrices)) {
            foreach ($this->optionalItemsPrices as $itemPrice) {
                if ($itemPrice->active) {
                    $optionalItems[] = [
                        'id' => $itemPrice->id,
                        'name' => $itemPrice->optionalItem->name,
                        'price' => $itemPrice->price
                    ];
                }
            }
        }

        return $optionalItems;
    }

}
