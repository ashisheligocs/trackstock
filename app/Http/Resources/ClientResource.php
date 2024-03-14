<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'name' => $this->name,
            'clientID' => $this->client_id,
            'slug' => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,
            'gst'=>$this->gst,
            'companyName' => $this->company_name,
            'address' => $this->address,
            'status' => (int) $this->status,
            'type' => (int)$this->type,
            'image' => $this->image_path ? asset('/images/clients/'.$this->image_path) : '',
            'images' => $this->images,
            'clientTotalPaid' => $this->clientTotalPaid(),
            'clientInvoiceTotal' => $this->clientInvoiceTotal(),
            'clientDue' => $this->clientDue(),
            'nonInvoiceDue' => $this->nonInvoiceTotalDue(),
            'nonInvoicePaid' => $this->nonInvoicePaid(),
            'nonInvoiceCurrentDue' => $this->nonInvoiceCurrentDue(),
            'nationality' => $this->nationality,
            'identity' => $this->identity,
            'hotel' => @$this->hotel,
            'hotel_id' => $this->hotel_id
        ];
    }
}