<?php

namespace Modules\Restaurant\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Restaurant\Http\RestaurantHelper;
use Modules\Restaurant\Entities\RestroItem;
use App\Models\Product;
class RestaurantOrderShowResource extends JsonResource
{
    use RestaurantHelper;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $allItems = RestroItem::where('order_id',$this->id)->get(); 
        if(!empty($allItems)){ 
            foreach($allItems as $item){
                $item->items = Product::where('id',$item->restaurant_item_id)->first();
            } 
        }
        return [
            'id' => $this->id,
            'date' => $this->order_date,
            'hotel' => $this->hotel,
            'orderId' => $this->order_id_uniq,
            'type' => $this->type ?? 'Customer order',
            // 'items' => $this->getRestaurantOrderDetails($this),
            'items' => $allItems,
            'room' => $this->room ?? null,
            'customer' => $this->client ?? null,
            'bookingId' => $this->booking_id,
            'roomCustomer' => $this->roomCustomer(),
            'transactionAmount' => @$this->transactions->where('asset', 1)->first()->amount,
            'bookingPaid' => $this->bookingPaid(),
            'checkOutDate' => $this->booking ? @$this->booking->check_out_date : '',
            'createdBy' => $this->createdBy(),
            'order_status_name' => $this->getOrderStatusName(),
            'payment_status_name' => $this->getPaymentStatusName(),
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
        ];
    }

    public function roomCustomer()
    {
        if ($this->booking) {
            return $this->booking->customer;
        }
        return null;
    }

    public function bookingPaid()
    {
        if ($this->booking) {
            return $this->booking->paid_amount <= 1;
        }

        return false;
    }

    public function createdBy()
    {
        if ($this->booking) {
            return $this->booking->booked_from == 1 ? 'Admin' : 'User';
        } else if ($this->transaction) {
            return @$this->transactions()->with('user')->first()->user->name;
        }

        return 'Admin';
    }
}
