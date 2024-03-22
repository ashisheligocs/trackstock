<?php

namespace Modules\Restaurant\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;
use App\Models\AccountTransaction;
use App\Models\NonInvoicePayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Modules\Accounts\Entities\LedgerAccount;
use Modules\Shops\Entities\Booking;
use Modules\Shops\Entities\BookingDetails;
use Modules\Shops\Transformers\CommonResource;
use Modules\Restaurant\Entities\RestroInvoice;
use Modules\Restaurant\Entities\RestroItem;
use Modules\Restaurant\Entities\Restroorder;
use Modules\Restaurant\Transformers\RestaurantOrderResource;
use Modules\Restaurant\Transformers\RestaurantOrderShowResource;
use Modules\Restaurant\Transformers\TodaySalesResource;
use Modules\Accounts\Entities\PlutusEntries;
use Modules\Shops\Entities\Shop;
use App\Models\Product;

class OrderController extends Controller
{
    public function index(Request $request)
    {
       
        $startDate = $request->startDate ? Carbon::parse($request->startDate) : null;
        $endDate = $request->endDate ? Carbon::parse($request->endDate)->addDay() : null;
        return RestaurantOrderResource::collection(Restroorder::with('items',
            'items.restaurantItem')->where('shop_id',$request->shop_id)->when($startDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('order_date', [$startDate, $endDate]);
        })->latest()->paginate(15));


        // return (ItemRestroOrder::select('id', 'order_id_uniq', 'invoice_id', 'customer_id', 'order_date', 'order_status', 'payment_status', 'total_amount', 'discount', 'tax', 'shop_id', 'created_at', 'updated_at')
        //             ->get());


    }

    public function show($id)
    {
        try {
            $orderDetails = Restroorder::where('id', $id)->first();

            if (@$orderDetails) {
                return new RestaurantOrderShowResource($orderDetails);
            } else {
                return $this->responseWithError('Sorry your request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $this->storeOrder($request->all());

            return $this->responseWithSuccess('Order added successfully!');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function update(Request $request,$id){
        dd($request->input());
        dd($id);
    }

    public function updateOrder($data,$id)
    {
        // dd($data);
        $order = Restroorder::find($id);
        if($order){

            $order->update([
                'total_amount'  => @$data['netTotal'] ?? 0,
                'discount'      => @$data['discountAmount'] ?? 0,
                'tax'           => @$data['orderTax'] ?? @$data['tax'] ?? 0,
            ]);

            $orderItems = $data['selectedProducts'];

            if ($orderItems && !empty($orderItems)) {
                /*delete Old Records*/
                $deleteOld = RestroItem::where('order_id',$id)->delete();

                foreach ($orderItems as $item) {
                    $optionalItems = @$item['addon'] ? Arr::pluck($item['addon'], 'id') : [];
                    RestroItem::create([
                        'order_id'           => $id,
                        'restaurant_item_id' => $item['variant']['id'],
                        'optional_item_ids'  => $optionalItems,
                        'qty'                => $item['quantity'],
                    ]);
                }
            }
        }
        return $order;
    }

    public function storeOrder($data)
    {
        $hotelId = $data['hotel_id'];
        $prevOrder = Restroorder::where('shop_id', $hotelId)->latest()->first();
        if (!empty($prevOrder)) {
            $previousBookingId = "RO-0000".$prevOrder->id;
        } else {
            $previousBookingId = "RO-00000";
        }

        // $room = @$data['room'] ?? null;
        // $booking = null;
        // if ($room) {
            //     $bookingDetail = BookingDetails::where('room_id', $room['id'])->whereNotIn('booking_status',
            //         [1, 4])->whereHas('booking', function ($bookingQuery) {
                //             $bookingQuery->whereDate('check_out_date', '>=', Carbon::now())->where('booking_status_main', Booking::CHECKIN);
                //     })->first();
                //     if ($bookingDetail) {
        //         $booking = $bookingDetail->booking;
        //         $booking->paid_amount = floatval($booking->paid_amount) + floatval(@$data['netTotal'] ?? 0);
        //         $booking->total_price = floatval($booking->total_price) + floatval(@$data['netTotal'] ?? 0);
        //         $booking->save();
        //     }
        // }

        // dd(Carbon::now());
        $order = Restroorder::create([
            'order_id_uniq' => $this->generateBookingId($previousBookingId,$hotelId),
            'order_date'    => Carbon::now(),
            'order_status'  => 0,
            'total_amount'  => @$data['netTotal'] ?? 0,
            'discount'      => @$data['discountAmount'] ?? 0,
            'tax'           => @$data['orderTax'] ?? @$data['tax'] ?? 0,
            // 'room_id'       => $room ? @$room['id'] : null,
            // 'booking_id'    => $booking ? @$booking->id : null,
            'shop_id'      => $hotelId,
            'customer_id'   => @$data['client'],
        ]);

        $orderItems = $data['selectedProducts'];

        
        foreach ($orderItems as $item) {
            if($item['id'] != 0){
                    $id = $item['id'];
                if (isset($newArray[$id])) {
                    $newArray[$id]['quantity'] += $item['quantity'];
                } else {
                    $newArray[$id] = $item;
                }
            }
        }
        
        $newArray = array_values($newArray);
        
        if ($orderItems && !empty($orderItems)) {
            foreach ($orderItems as $item) {
                // $optionalItems = @$item['addon'] ? Arr::pluck($item['addon'], 'id') : [];
                if($item->id != 0){
                    RestroItem::create([
                        'order_id'           => $order->id,
                        'restaurant_item_id' => $item['id'],
                        'qty'                => $item['quantity'],
                    ]);
                }
                
            }
        }

        return $order;
    }

    public function payInvoice(Request $request)
    {
       
        $input = $request->all();
        $hotelId = $input['hotel_id'];
        $orderId = $input['invoice_slug'];
        $userId = auth()->user()->id;

        /*Create Plutus Entry ABhi(11-12-23)*/

        $note = '[' . $orderId . '] Order payment';
        $plutusId = $this->createPlutusEntry($hotelId,$note,now(),floatval($request->subtotal ?? 0));

        if (floatval($request->paidAmount ?? 0)) {
            
            if($request->paidAmount == $request->subtotal){
                $bankAmount = $request->paidAmount;
                $cashAmount = 0;
            } else if($request->paidAmount == 0){
                $bankAmount = 0;
                $cashAmount = $request->paidAmount;
            } else {
                $bankAmount = $request->paidAmount;
                $cashAmount = $request->subtotal - $request->paidAmount;
            }

            if($bankAmount != 0){
                $bankLedger = LedgerAccount::where('code', 'bank')->first();
                $transaction = AccountTransaction::create([
                    'account_id' => $bankLedger->id,
                    'amount' => floatval($bankAmount ?? 0),
                    'reason' => '[' . $orderId . '] Invoice payment Received In Bank',
                    'type' => 1,
                    'transaction_date' => Carbon::now(),
                    'cheque_no' => $request->chequeNo,
                    'receipt_no' => $request->receiptNo,
                    'created_by' => $userId,
                    'status' => 1,
                    'shop_id' => $hotelId,
                    'order_id' => @$input['invoice_id'],
                    'customer_id' => @$input['client'] ? @$input['client'] : null,
                    'plutus_entries_id' => $plutusId,
                ]);
            }
            
            if($cashAmount != 0){
                $transaction = AccountTransaction::create([
                    'account_id' => $request->account['id'],
                    'amount' => floatval($cashAmount ?? 0),
                    'reason' => '[' . $orderId . '] Invoice payment Received In Cash',
                    'type' => 1,
                    'transaction_date' => Carbon::now(),
                    'cheque_no' => $request->chequeNo,
                    'receipt_no' => $request->receiptNo,
                    'created_by' => $userId,
                    'status' => 1,
                    'shop_id' => $hotelId,
                    'order_id' => @$input['invoice_id'],
                    'customer_id' => @$input['client'] ? @$input['client'] : null,
                    'plutus_entries_id' => $plutusId,
                ]);
            }
            

            NonInvoicePayment::create([
                'slug' => uniqid(),
                'client_id' => @$input['client'] ? @$input['client'] : null,
                'amount' => floatval($request->subtotal ?? 0),
                'type' => 1,
                'transaction_id' => $transaction->id,
                'date' => Carbon::now(),
                'note' => '[' . $orderId . '] Restaurant invoice payment',
                'status' => 1,
                'created_by' => $userId,
                'order_id' => @$input['invoice_id'],
                'shop_id' => $hotelId,
            ]);

            $payableAccount = LedgerAccount::where('code', 'RESTAURANT-REVENUE')->first();
            $payableAccount = $payableAccount->getAccoutnbalance;
            $payableAccount->balanceTransactions()->create([
                'amount' => floatval($request->subtotal ?? 0),
                'reason' => '[' . $orderId . '] Restaurant invoice Payment',
                'type' => 1,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => $userId,
                'status' => 1,
                'order_id' => @$input['invoice_id'],
                'shop_id' => $hotelId,
                'customer_id' => @$input['client'] ? @$input['client'] : null,
                'plutus_entries_id' => $plutusId,
            ]);


            //update Status in RestoOrder Table
            $updateStatus = Restroorder::where('id',$input['invoice_id'])->first();
            $updateStatus->order_status = 1;
            $updateStatus->payment_status = 1;
            $updateStatus->save();

            //Update Product Inventory

            $orderItems = $input['selectedProducts'];

            if ($orderItems && !empty($orderItems)) {
                foreach ($orderItems as $item) {
                    $product = Product::where('id', $item['id'])->first();
                    if ($product) {
                        $updatedInventoryCount = $product->inventory_count - $item['quantity'];
                        $product->update([
                            'inventory_count' => $updatedInventoryCount,
                        ]);
                    }
                }
            }
        }
    }

    protected function cancelOrder(Request $request,$id){

        try {
            $updateStatus = Restroorder::where('id',$id)->first();
            $updateStatus->order_status = 2;
            $updateStatus->cancel_note = $request->cancel_note;
            $updateStatus->save();

            return $this->responseWithSuccess('Order Cancel Successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    protected function updateOrderStatus(Request $request,$id){

        try {
            $updateStatus = Restroorder::where('id',$id)->first();
            $updateStatus->order_status = $request->order_status['id'];
            $updateStatus->save();

            return $this->responseWithSuccess('Order Status Updated Successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    protected function createPlutusEntry($hotelId,$note,$date,$amount){

        $createPlutus = PlutusEntries::create([
            'shop_id' => $hotelId,
            'note' => $note,
            'date' => $date,
            'amount' => $amount,
        ]);

        return $createPlutus->id;
    }

    protected function generateBookingId($previousBookingId,$hotelId)
    {

        $hotel1 = Shop::where('id',$hotelId)->first();

        $hotel = str_replace('Shop', '', $hotel1->shop_name);
        $hotel = str_replace('Shop', '', $hotel);

        $hotel = ($hotel1->shop_prefix !== null) ? $hotel1->shop_prefix : Str::slug($hotel);


        // Extract the numeric part of the previous booking ID
        $previousNumber = substr($previousBookingId, 3);
        $nextNumber = intval($previousNumber) + 1;

        // Pad the numeric part with leading zeros
        $nextNumberPadded = str_pad($nextNumber, strlen($previousNumber), '0', STR_PAD_LEFT);

        // Concatenate the prefix and the padded numeric part to form the new booking ID
        $prefix = substr($previousBookingId, 0, 3);

        return $prefix.$hotel.'-'.$nextNumberPadded;
    }

    public function createInvoice(Request $request)
    {
        try {
            
            $order = $this->storeOrder($request->all());

            NonInvoicePayment::create([
                'slug' => uniqid(),
                'client_id' => $order->customer_id,
                'amount' => $order->total_amount,
                'type' => 0,
                'transaction_id' => null,
                'date' => Carbon::now(),
                'note' => '[' . $order->order_id_uniq . '] order due',
                'status' => 1,
                'created_by' => auth()->user()->id,
                'order_id' => $order->id,
                'shop_id' => $order->shop_id,
            ]);

            return CommonResource::make($order);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function updateInvoice(Request $request,$id)
    {

        try {
            $order = $this->updateOrder($request->all(),$id);

            $nonInvoicePayment = NonInvoicePayment::where('order_id',$id)->get();
            if(!empty($nonInvoicePayment)){
               foreach($nonInvoicePayment as $nonInvoicePayment){
                NonInvoicePayment::updateOrCreate(
                    ['id' => $nonInvoicePayment->id],
                    ['amount' => $order->total_amount]
                );
               }
            }
            return CommonResource::make($order);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }



    public function destroy($id)
    {
        try {
            $hotelcategory = Restroorder::where('id', $id)->first();
            $hotelcategory->delete();

            return $this->responseWithSuccess('Order deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function makePaymentRequest(Request $request)
    {
        try {
            $orderPayment = Restroorder::where("id", $request->order_id)->where('payment_status', 0)->first();
            if ($orderPayment) {
                $invChec = RestroInvoice::latest()->first();
                if (!empty($invChec)) {
                    $inv_id = "TH-HOTEL-0000".$invChec->id;
                } else {
                    $inv_id = "TH-00000";
                }
                if (isset($orderPayment) && !empty($orderPayment)) {
                    $discount = isset($request->discount) && $request->discount > 0 ? $request->discount : $orderPayment->discount;
                    $order_amount = isset($request->order_amount) && $request->order_amount > 0 ? $request->order_amount : $orderPayment->order_amount;
                    $total_amount = isset($request->total_amount) && $request->total_amount > 0 ? $request->total_amount : $orderPayment->total_amount;
                    $tax = isset($request->tax) && $request->tax > 0 ? $request->tax : $orderPayment->tax;
                    $newBooking = RestroInvoice::firstOrNew(['order_id' => $request->order_id]);
                    $newBooking->order_id = $request->order_id;
                    $newBooking->invoice_id = $inv_id;
                    $newBooking->order_amount = $order_amount;
                    $newBooking->total_amount = $total_amount;
                    $newBooking->discount = $discount;
                    $newBooking->tax = $tax;
                    $newBooking->save();
                    if (!empty($newBooking)) {
                        $order_status = Restroorder::where('id', $request->order_id)->first();
                        $order_status->update([
                            'payment_status' => 2,
                        ]);
                    }

                    return $this->responseWithSuccess('Order added successfully!');
                }
            } else {
                return $this->responseWithError("Payment status Already Updated !");
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    //today sales

    public function todaySale(Request $request){
       
        $today = date('Y-m-d');
        
        $todaySale = RestroItem::with('restaurantItem','restaurantorder')->whereHas('restaurantorder', function ($newQuery) use ($request,$today) {
            $newQuery->where('shop_id', $request->shop_id);
            $newQuery->whereBetween('order_date', [$today, $request->todayDate]);
        })->get();
 
        return TodaySalesResource::collection($todaySale);
    }
}
