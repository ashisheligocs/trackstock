<?php

namespace App\Http\Resources;

use App\Models\InvoiceService;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceReturnServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $invoiceService = InvoiceService::where('invoice_id', $this->invoiceReturn->invoice->id)->where('service_id', $this->service_id)->first();
        $invoiceQty = $invoiceService->quantity;
        return [
            'id' => $this->id,
            'returnQty' => $this->quantity,
            'invoiceQty' => $invoiceQty,
            'salePrice' => $this->sale_price,
            'purchasePrice' => $this->purchase_price,
            'serviceID' => $this->service_id,
            'serviceName' => $invoiceService->service_name,
            // 'productSlug' => $this->product->slug,
            // 'productCode' => $this->product->code,
            // 'productName' => $this->product->name,
            // 'productModel' => $this->product->model,
            // 'inventoryCount' => $this->product->inventory_count,
            // 'avgPurchasePrice' => $this->product->purchase_price,
            // 'productUnit' => $this->product->productUnit->code,
            // 'taxType' => $this->product->tax_type,
            // 'taxRate' => @$this->product->productTax->rate,
            // 'product_tax' => $this->product->productTaxRate
        ];
    }
}
