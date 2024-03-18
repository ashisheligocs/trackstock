<?php

namespace Modules\Restaurant\Http\Controllers\api;

use App\Http\Controllers\Controller as Controller;
use App\Http\Resources\CommonResource;
use App\Models\AccountTransaction;
use App\Models\GeneralSetting;
use DB;
use Exception;
use Illuminate\Http\Request;
use Modules\Restaurant\Entities\OptionalItem;
use Modules\Restaurant\Entities\OptionalItemPrice;
use Modules\Restaurant\Entities\Restaurant;
use Modules\Restaurant\Entities\RestaurantItem;
use Modules\Restaurant\Transformers\RestaurantResource;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $setting = GeneralSetting::where('key', 'selected_hotel')->first();

        if ($setting && $setting->value && $setting->value !== 'all') {
            $restaurants = Restaurant::whereHas('shop', function ($q) use ($setting) {
                $q->where('id', $setting->value);
            })->with([
                'shop' => function ($query) {
                    $query->select('shop_name', 'shop_phone', 'shop_email', 'id');
                },
            ])->latest()->paginate($request->perPage);
        } else {
            $restaurants = Restaurant::with([
                'shop' => function ($query) {
                    $query->select('shop_name', 'shop_phone', 'shop_email', 'id');
                },
            ])->latest()->paginate($request->perPage);
        }

        return RestaurantResource::collection($restaurants);
    }

    public function price(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required',
            'item_id'       => 'required',
            'varient_id'    => 'required',
            'price'         => 'required',
            'tax_rate'      => 'nullable',
        ]);
        try {
            $restaurantItem = RestaurantItem::updateOrCreate([
                'restaurant_id' => $validated['restaurant_id'],
                'item_id'       => $validated['item_id'],
                'varient_id'    => $validated['varient_id'],
            ], [
                'price' => $validated['price'],
            ]);

            if (!empty($validated['tax_rate']) && !empty($restaurantItem)) {
                $restaurantItem->taxName()->sync($validated['tax_rate']);
            }

            return $this->responseWithSuccess('Item Rate Updated successfully !');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function updateTax(Request $request, Restaurant $restaurant)
    {
        $taxes = $request->taxes ?? [];
        $restaurant->taxes()->sync($taxes);

        return $this->responseWithSuccess('Item Taxes Updated successfully !');
    }

    public function Taxes(Request $request, Restaurant $restaurant)
    {
        return CommonResource::make($restaurant->taxes);
    }

    public function show($id)
    {
        return view('restaurant::show');
    }

    public function edit($id)
    {
        return view('restaurant::edit');
    }

    public function storeVariantsOptions(Request $request, Restaurant $restaurant)
    {
        DB::beginTransaction();
        try {
            $optionItems = @$request->optionItems;
            $variants = @$request->variants;

            $optIds = [];
            if (count($optionItems) > 0 && @$optionItems[0]['name'] && @$optionItems[0]['price']) {
                foreach ($optionItems as $item) {
                    if (@$item['name']) {
                        $option = OptionalItem::updateOrcreate(
                            ['name' => $item['name']],
                            ['name' => $item['name']]);
                    }

                    if (isset($option) && @$item['price']) {
                        $opt = OptionalItemPrice::updateOrcreate([
                                'restaurant_id'    => $restaurant->id,
                                'optional_item_id' => $option->id,
                                'item_id'          => $request->item_id,
                            ]
                            , [
                                'restaurant_id'    => $restaurant->id,
                                'optional_item_id' => $option->id,
                                'item_id'          => $request->item_id,
                                'price'            => $item['price'],
                                'active'            => 1,
                            ]);
                        $optIds[] = $opt->id;
                    }
                }
            }

            if (OptionalItemPrice::where('item_id', $request->item_id)->where('restaurant_id', $restaurant->id)->whereNotIn('id', $optIds)->exists()) {
                OptionalItemPrice::where('item_id', $request->item_id)
                    ->where('restaurant_id', $restaurant->id)
                    ->whereNotIn('id', $optIds)
                    ->update(['active' => 0]);
            }

            $variantItemsIds = [];
            if (count($variants) > 0 && @$variants[0]['id'] && @$variants[0]['price']) {
                foreach ($variants as $variant) {
                    if (@$variant['id']) {
                        $var = RestaurantItem::updateOrCreate([
                            'restaurant_id' => $restaurant->id,
                            'item_id'       => $request->item_id,
                            'varient_id'    => $variant['id'],
                        ], [
                            'restaurant_id' => $restaurant->id,
                            'item_id'       => $request->item_id,
                            'varient_id'    => $variant['id'],
                            'price'         => $variant['price'],
                            'active'            => 1,
                        ]);

                        $variantItemsIds[] = $var->id;
                    }
                }
            }
            if (RestaurantItem::where('item_id', $request->item_id)->where('restaurant_id', $restaurant->id)->whereNotIn('id', $variantItemsIds)->exists()) {
                RestaurantItem::where('item_id', $request->item_id)
                    ->where('restaurant_id', $restaurant->id)
                    ->whereNotIn('id', $variantItemsIds)
                    ->update(['active' => 0]);
            }
            DB::commit();

            return $this->responseWithSuccess('Restaurant updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function updateItemStatus(Request $request, Restaurant $restaurant){

        DB::beginTransaction();
        try {
            RestaurantItem::where('restaurant_id', $restaurant->id)
                      ->where('item_id', $request->item_id)
                      ->update(['active' => $request->active]);

            DB::commit();
            return $this->responseWithSuccess('Restaurant Item Status updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
