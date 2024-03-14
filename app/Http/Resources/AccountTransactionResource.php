<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $balance = 0;
        if($this->type == 1){
            $balance = $balance + $this->amount;
        }
        else{
           $balance = $balance - $this->amount;
        }

        return [
            'id' => $this->id,
            'account' => new AccountResource($this->cashbookAccount),
            'reason' => $this->reason,
            'slug' => $this->slug,
            'amount' => round($this->amount, 2),
            'balance' => round($balance, 2),
            'type' => (int) $this->type,
            'accountType'=> (int) $this->account_type,
            'transactionDate' => date_format(date_create($this->transaction_date), 'Y-m-d'),
            'note' => $this->note,
            'status' => (int) $this->status,
            'user' => $this->user,
            'prevTotal' => $request->total,
            'isAsset' => $this->asset,
            'hotel' => $this->hotel,
            'isExpense' => $this->expense,
            // 'customer_name'=> @$this->booking ? (@$this->booking->Source ? $this->booking->Source->name : (@$this->booking->Customer ? $this->booking->Customer->name : '')) : '',
            // 'customer_slug'=> @$this->booking ? (@$this->booking->Source ? $this->booking->Source->slug : (@$this->booking->Customer ? $this->booking->Customer->slug : '')) : '',
            'customer_name'=> $this->customer ? $this->customer->name : '',
            'customer_slug'=> $this->customer ? $this->customer->slug : '',
            // 'final_balance'=>@$this->balance ? $this->balance : 0,
            'discount_amount' => @$this->booking ? $this->booking->discount_amount : 0,
        ];
    }
}