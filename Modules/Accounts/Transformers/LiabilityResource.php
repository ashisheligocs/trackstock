<?php

namespace Modules\Accounts\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LiabilityResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $groupedData = $this->groupBy('ledgerCategory.name')->map(function ($items, $categoryName) {

            if($categoryName == "Current Liabilities"){
                $convertArray = $items;
                $key = array_search('GST-INPUT', array_column($convertArray->toArray(), 'code'));
                if($items[$key]){
                    if(@$items[$key]->getAccoutnbalance && $items[$key]->getAccoutnbalance->available_balance !== null){
                        $totalAmount = $items->sum('getAccoutnbalance.available_balance') - ($items[$key]->getAccoutnbalance->available_balance * 2);
                    } else {
                        $totalAmount = $items->sum('getAccoutnbalance.available_balance');
                    }
                } else{
                    $totalAmount = $items->sum('getAccoutnbalance.available_balance');
                }
            } else {
                $totalAmount = $items->sum('getAccoutnbalance.available_balance');
            }

            $filteredItems = $items->filter(function ($item) {
                return @$item->getAccoutnbalance->available_balance && $item->getAccoutnbalance->available_balance !== 0;
            });

            return [
                'group_name' => $categoryName,
                'total_amount' => $totalAmount,
                'items' => $filteredItems->map(function ($item) {
                    return [
                        'name' => $item->ledger_name,
                        'amount' => $item->getAccoutnbalance->available_balance,
                    ];
                }),
            ];
        })->values();

        return $groupedData;
    }
}
