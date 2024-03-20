<?php

namespace App\Http\Resources;

use App\Models\Purchase;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\PurchaseProduct;
use Modules\Restaurant\Entities\RestroItem;

class ProductListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $getTotalPurchaseQtyPerShop =  PurchaseProduct::where('product_id', $this->id)->whereHas('purchase', function ($newQuery) use ($request) {
            $newQuery->where('shop_id', request()->hotel_id);
        })->sum('quantity');
        
        $getTotalSellQtyPerShop = RestroItem::where('restaurant_item_id', $this->id)->whereHas('restaurantorder', function ($newQuery) use ($request) {
            $newQuery->where('shop_id', request()->hotel_id);
        })->sum('qty');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->name . ' [' . $this->code . ']',
            'slug' => $this->slug,
            'subCategory' => new ProductSubCategoryResource($this->whenLoaded('proSubCategory')),
            'category' => new ProductCategoryResource($this->proSubCategory->category),
            'code' => $this->code,
            'itemModel' => $this->model,
            'itemUnit' => new UnitResource($this->productUnit),
            'inventoryCount' => $this->inventory_count,
            'alertQty' => $this->alert_qty,
            'regularPrice' => $this->regular_price,
            'sellingPrice' => $this->sellingPrice(),
            'discount' => $this->discount,
            'discountAmount' => $this->discountAmount(),
            'taxAmount' => $this->taxAmount(),
            'note' => $this->note,
            'status' => (int) $this->status,
            'image' => $this->image_path ? asset('/images/products/' . $this->image_path) : '',
            // "quantity" => $this->produtQuantity(),
            'available_qty' => $getTotalPurchaseQtyPerShop - $getTotalSellQtyPerShop,
            'total_qty' => $getTotalPurchaseQtyPerShop,
            // 'taxRate' => !empty($this->productTaxRate) ? $this->productTaxRate[0]->rate : '',
        ];
    }
}