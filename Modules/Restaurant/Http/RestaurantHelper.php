<?php

namespace Modules\Restaurant\Http;


use Illuminate\Support\Arr;
use Modules\Restaurant\Entities\OptionalItem;

trait RestaurantHelper
{
    public function getBookingOrderDetails($booking)
    {
        try {
            $orders = @$booking->restaurantOrders;

            if (!$orders || count($orders) <= 0) return null;

            $itemArray = [];
            $variantPrice = 0;
            $tax = 0;
            $discount = 0;
            $total = 0;
            $addonTotal = 0;
            $orderIdArray = [];
            if (count($orders)) {
                
                foreach ($orders as $order) {
                    $orderIdArray[] = $order->id;
                    $items = $order->items;
                    
                    if (count($items) > 0) {
                        $priceForAddons = 0;
                        foreach ($items as $item) {
                            
                            $name = @$item->restaurantItem->item->item_name ?? '';
                            $price = floatval(@$item->restaurantItem->price ?? 0);
                            $quantity = intval(@$item->qty ?? 0);
                            $varPrice = $price*$quantity;
                            $priceForAddons += $varPrice;
                            $variantPrice += $varPrice;

                            $itemArray[] = [
                                'name' => $name,
                                'price' => $price,
                                'quantity' => $quantity,
                                'total' => $varPrice
                            ];
                        }

                        $tax += floatval($order->tax ?? 0);
                        $discount += floatval($order->discount ?? 0);
                        $netAmount = floatval($order->total_amount ?? 0);
                        $addonTotal += $netAmount - ($priceForAddons ?? 0)  + floatval($order->discount ?? 0);
                        $total += $netAmount ;
                    }
                }
            }

            return [
                'total' => $total,
                'tax' => $tax,
                'discount' => $discount,
                'addon' => $addonTotal - $tax,
                'items' => $itemArray,
                'subtotal' => $total - $tax + $discount,
                'error' => false,
                'order_id' => $orderIdArray,
            ];
        } catch (\Exception $exception) {
            info($exception->getMessage());
            return [
                'total' => 0,
                'tax' => 0,
                'discount' => 0,
                'addon' => 0,
                'items' => [],
                'error' => true
            ];
        }
    }

    public function getRestaurantOrderDetails($order)
    {
        try {
            if (!$order) return null;

            $itemArray = [];
            $variantPrice = 0;
            $tax = 0;
            $discount = 0;
            $total = 0;
            $addonTotal = 0;
            $addOnItems = [];
            if ($order) {
                    $items = $order->items;
                    if (count($items) > 0) {
                        
                        foreach ($items as $item) {
                            
                            $name = @$item->restaurantItem->item->item_name ?? '';
                            $price = floatval(@$item->restaurantItem->price ?? 0);
                            $quantity = intval(@$item->qty ?? 0);
                            $varPrice = $price*$quantity;
                            $variantPrice += $varPrice;
                            
                            if(!empty($item->optional_item_ids)){
                                foreach($item->optional_item_ids as $addItems){
                                    $itemName = OptionalItem::where('id',$addItems)->with('prices')->first();
                                    
                                    $addOnItems[] = [
                                        'id'=>$addItems,
                                        'item_name' => $itemName->name,
                                        'item_price' => $itemName->prices[0]->price,
                                    ];
                                }
                                
                            } else {
                                $addOnItems = [];
                            }
                           
                            $itemArray[] = [
                                'name' => $name,
                                'addOn' => $addOnItems,
                                'price' => $price,
                                'quantity' => $quantity,
                                'total' => $varPrice,
                                'variant_name' => $item->restaurantItem->variant->varient_name,
                                'item_id' => $item->restaurantItem->id,
                                'variant' => [
                                    'id' => $item->restaurantItem->id,
                                    'name' => $item->restaurantItem->variant->varient_name,
                                    'price' => $item->restaurantItem->price
                                ]
                            ];
                        }

                        $tax = floatval($order->tax ?? 0);
                        $discount = floatval($order->discount ?? 0);
                        $netAmount = floatval($order->total_amount ?? 0);
                        $addonTotal = floatval($netAmount) - (($variantPrice) + floatval($order->discount ?? 0) + floatval($order->tax ?? 0));
                        $total = $netAmount;
                    }
            }

            return [
                'total' => $total,
                'tax' => $tax,
                'discount' => $discount,
                'addon' => $addonTotal,
                'items' => $itemArray,
                'subtotal' => $total - $tax + $discount,
                'error' => false
            ];
        } catch (\Exception $exception) {
            info($exception->getMessage());
            return [
                'total' => 0,
                'tax' => 0,
                'discount' => 0,
                'addon' => 0,
                'items' => [],
                'error' => true
            ];
        }

    }
}