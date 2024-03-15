<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shops\Transformers\CheckOutRoomResource;
use Modules\Restaurant\Http\RestaurantHelper;

class InvoiceResource extends JsonResource
{
    use RestaurantHelper;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       
        $total_service_tax = 0.0;
        if(!empty($this->invoiceServiceTax)){
            foreach($this->invoiceServiceTax as $invoiceservice){
                $total_service_tax += ($invoiceservice->amount);
            }
        }
        return [
            'id' => $this->id,
            'invoiceNo' => $this->invoice_no,
            'label' => config('config.invoicePrefix').'-'.$this->invoice_no,
            'slug' => $this->slug,
            'reference' => $this->reference,
            'client' => new ClientListResource($this->client),
            'invoiceServices' => InvoiceServiceResource::collection($this->invoiceService),
            'invoiceServiceTax' => $this->invoiceServiceTax,
            'invoicePayments' => InvoicePaymentListResource::collection($this->invoicePayments),
            'invoiceProducts' => InvoiceProductResource::collection($this->invoiceProducts),
            'discountType' => $this->discount_type,
            'discount' => $this->discount,
            'discountPercentage' => $this->discountPercentage(),
            'transport' => $this->transport,
            'taxRate' => $this->invoiceTax,
            'tax' => $this->taxAmount(),
            'total_service_tax' => $total_service_tax,
            'subTotal' => $this->sub_total,
            'invoiceTotal' => $this->invoiceTotal(),
            'totalPaid' => $this->invoiceTotalPaid(),
            'due' => $this->totalDue(),
            'totalInvoiceReturn' => isset($this->invoiceReturn) ? $this->invoiceReturn->total_return : 0,
            'accountPayable' => isset($this->invoiceReturn->returnTransaction) ? $this->invoiceReturn->returnTransaction->amount : null,
            'poReference' => $this->po_reference,
            'paymentTerms' => $this->payment_terms,
            'deliveryPlace' => $this->delivery_place,
            'invoiceDate' => $this->invoice_date,
            'note' => $this->note,
            'status' => (int) $this->status,
            'createdBy' => $this->user->name,
            'booking' => $this->booking,
            'hotel' => $this->hotel,
            'bookingDetails' => CheckOutRoomResource::collection($this->bookingDetails),
            'bookingDiscount' => $this->bookingDiscount(),
            'restaurantOrderDetails' => null,
            'bookingRestOrders' => $this->getBookingOrderDetails($this->booking)
        ];
    }
}