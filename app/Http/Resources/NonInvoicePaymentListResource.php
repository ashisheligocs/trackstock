<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NonInvoicePaymentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $transaction = @$this->paymentTransaction;

//        dump($transaction);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'clientName' => $this->client->name,
            'account' => $transaction ? new AccountResource($transaction->cashbookAccount) : '',
            'transaction' => $transaction ?? null,
            'amount' => $this->amount,
            'type' => (int) $this->type,
            'date' => $this->date,
            'note' => $this->note,
            'status' => (int) $this->status,
            'hotel' => $this->hotel
        ];
    }
}