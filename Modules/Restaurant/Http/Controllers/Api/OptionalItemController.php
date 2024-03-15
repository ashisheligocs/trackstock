<?php

namespace Modules\Restaurant\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shops\Transformers\CommonResource;
use Modules\Restaurant\Entities\OptionalItem;
use Modules\Restaurant\Entities\OptionalItemPrice;

class OptionalItemController extends Controller
{

    public function index()
    {
        $items = OptionalItem::all();

        return CommonResource::collection($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
           'name' => 'required',
           'restaurant_id' => 'required',
           'item_id' => 'required',
           'price' => 'required'
        ]);

        $optionalItem = OptionalItem::updateOrCreate([
            'name' => $validated['name'],
        ]);

        OptionalItemPrice::updateOrCreate([
            'restaurant_id' => $validated['restaurant_id'],
            'item_id' => $validated['item_id'],
            'optional_item_id' => $optionalItem->id,
        ],[
            'price' => $validated['price']
        ]);

        return $this->responseWithSuccess('Optional Item added successfully!');
    }
}
