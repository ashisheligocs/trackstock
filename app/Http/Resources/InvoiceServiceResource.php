<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class InvoiceServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $returnQty = DB::table('invoice_return_products')
            ->join('invoice_returns', 'invoice_returns.id', '=', 'invoice_return_products.return_id')
            ->join('invoices', 'invoices.id', '=', 'invoice_returns.invoice_id')
            ->where('invoice_return_products.service_id', '=', $this->service_id)
            ->where('invoice_returns.invoice_id', '=',$this->invoice_id)
            ->sum('invoice_return_products.quantity');

        return [
            "id" => $this->id,
            "invoice_id" => $this->invoice_id,
            "service_id" => $this->service_id,
            "service_name" => $this->service_name,
            "quantity" => $this->quantity,
            "price" => $this->price,
            "myTax" => $this->myTax,
            'returnQty' => $returnQty > 0 ? $returnQty : 0,
        ];
    }
}
