<?php

namespace Modules\Accounts\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LedgerNameAndGroupResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->ledger->ledger_name . ' - ' . $item->ledgerCategory->name
            ];
        });

    }
}
