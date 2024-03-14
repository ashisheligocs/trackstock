<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceReturnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'returnNo' => $this->return_no,
            'reason' => $this->reason,
            'slug' => $this->slug,
            'totalReturn' => $this->total_return,
            'invoice' => new InvoiceListResource($this->invoice),
            'client' => new ClientListResource($this->invoice->client),
            'invoiceReturnProducts' => ($this->invoiceReturnProducts[0]->product_id != null) ? InvoiceReturnProductResource::collection($this->invoiceReturnProducts) : [],
            'invoiceReturnService' => ($this->invoiceReturnProducts[0]->service_id != null) ? InvoiceReturnServiceResource::collection($this->invoiceReturnProducts) : [],
            'accountPayable' => isset($this->returnTransaction) ? $this->returnTransaction : null,
            'account' => isset($this->returnTransaction) ? new AccountResource($this->returnTransaction->cashbookAccount) : null,
            'returnDate' => $this->date,
            'note' => $this->note,
            'status' => (int) $this->status,
            'createdBy' => $this->user->name,
            'invoiceReturnServiceTax' => $this->invoice->invoiceServiceTax
        ];
    }
}
