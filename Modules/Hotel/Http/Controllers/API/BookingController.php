<?php

namespace Modules\Hotel\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use App\Models\AccountTransaction;
use App\Models\GeneralSetting;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\NonInvoicePayment;
use App\Models\VatRate;
use Auth;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Accounts\Entities\LedgerAccount;
use Modules\Accounts\Entities\PlutusEntries;
use Modules\Hotel\Entities\Booking;
use Modules\Hotel\Entities\BookingComplementray;
use Modules\Hotel\Entities\BookingDetails;
use Modules\Hotel\Entities\BookingStatus;
use Modules\Hotel\Entities\ClinetBookingDetails;
use Modules\Hotel\Entities\Hotel;
use Modules\Hotel\Entities\Room;
use Modules\Hotel\Entities\BookingAmendStay;
use Modules\Hotel\Traits\ApiResponse;
use Modules\Hotel\Transformers\BookingGetResource;
use Modules\Hotel\Transformers\CheckoutResource;
use Modules\Hotel\Transformers\CheckOutRoomResource;
use Modules\Hotel\Transformers\CommonResource;
use Modules\Hotel\Http\Requests\BookingRequest;
use DB;
use Modules\Restaurant\Entities\Restroorder;
use Modules\Restaurant\Http\RestaurantHelper;
use function Nette\Utils\in;
use function Symfony\Component\HttpFoundation\add;

class BookingController extends Controller
{
    use ApiResponse, RestaurantHelper;

    public function index(Request $request)
    {
        try {
            // dd($request->type);
            $query = Booking::query();
             if($request->type == 6){
                 $query = $query->whereIn('booking_status_main',[1,4]);
             } else {
                if($request->type == 4){
                    $query = $query->filter($request->only(['type']))->whereNotIn('booking_status_main',[1,4]);
                } else {
                    $query = $query->filter($request->only(['type']));
                }
             }
            
            // $query = $query->filter($request->only(['type']));
           
            //            if (@$request->past) {
            //                $query = $query->where('booking_status_main', Booking::CHECKOUT);
            //            }
            $query->with('Customer', 'Source', 'BookingType', 'Hotel', 'BookingDetails.room', 'BookingDetails.mealPlanDetails.mealname', 'BookingDetails.mealPlanDetails.taxName', 'BookingDetails.room.Roomcategory','BookingDetails.room.hotelRoomCategory', 'BookingDetails.room.Bed', 'BookingDetails.complementrays.paidFacility');
            if ($request->hotel_id) {
                $query->where('hotel_id', $request->hotel_id);
            }
            $hotel = null;
            $setting = GeneralSetting::where('key', 'selected_hotel')->first();
            if ($setting && $setting->value && $setting->value !== 'all') {
                $query->where('hotel_id', $setting->value);
                $hotel = $setting->value;
            }
            
            $bookingCounts = [
                'upcoming' => Booking::whereDate('check_in_date', '>', Carbon::now())->when($request->hotel_id || $hotel, function ($query) use ($request, $hotel) {
                    $query->where('hotel_id', $request->hotel_id ?? $hotel);
                })->whereIn('booking_status_main', [Booking::BOOKED, Booking::PENDING_PAYMENT])->count(),
                'checkin'  => Booking::whereDate('check_in_date', '<=', Carbon::now())->when($request->hotel_id || $hotel, function ($query) use ($request, $hotel) {
                    $query->where('hotel_id', $request->hotel_id ?? $hotel);
                })->whereDate('check_out_date', '>', Carbon::now())
                    ->whereIn('booking_status_main', [Booking::BOOKED, Booking::PENDING_PAYMENT])->count(),
                'occupied' => Booking::whereDate('check_out_date', '>=', Carbon::now())->when($request->hotel_id || $hotel, function ($query) use ($request, $hotel) {
                    $query->where('hotel_id', $request->hotel_id ?? $hotel);
                })->where('booking_status_main', Booking::CHECKIN)->count(),
                'checkout' => Booking::whereDate('check_out_date', '<=', Carbon::now())->when($request->hotel_id || $hotel, function ($query) use ($request, $hotel) {
                    $query->where('hotel_id', $request->hotel_id ?? $hotel);
                })->where('booking_status_main', '!=', Booking::CHECKOUT)->where('booking_status_main', '!=', Booking::CANCEL)->count(),
                'canceled' => Booking::when($request->hotel_id || $hotel, function ($query) use ($request, $hotel) {
                    $query->where('hotel_id', $request->hotel_id ?? $hotel);
                })->where('booking_status_main', Booking::CANCEL)->count(),
            ];

            $bookings = $query->latest()->paginate($request->perPage);
            $bookings = BookingGetResource::collection($bookings);
           
            return $bookings->additional(['bookingCounts' => $bookingCounts]);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function store(BookingRequest $request)
    {
        // dd($request->input());
        // Get the current date and time in a custom format
        $customDateTime = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {

            $lstBookingData = Booking::latest()->first();
            $getBookingData = Booking::where('id', $request->id)->first();
            $paidAmount = $request->discount_amount + $request->paid_amount;

            if ($getBookingData && $request->type == 3 && $getBookingData->total_price != $paidAmount) {
                return $this->responseWithError('Amount is not be less then total amount');
            }

            $newBookingId = ($getBookingData) ? $getBookingData->booking_number : $this->generateBookingId($lstBookingData, $request->hotel_id);
            // date format for get total no of days
            $date1 = $request->check_in_date;
            $date2 = $request->check_out_date;

            $time = Carbon::now()->toTimeString();
            $date1 = date('Y-m-d H:i:s', strtotime("$date1 $time"));
            $date2 = date('Y-m-d H:i:s', strtotime("$date2 $time"));

            $newBooking = Booking::firstOrNew(['id' => $request->id]);
            // dd($newBooking);
            $advanceAmountOption = 0;
             //setup customer_id in account_transaction table base on booking source (28-11-23 Abhi)
             $customerId = @$request->booking_source ? $request->booking_source : $request->booked_by;

             /*Create Plutus entry only if it's credit to manage grouping in transaction*/
             $plutusId = NULL;
             if((!$request->id && @$request->commission_to && $request->commission_amount > 0) || (int) $request->advance_amount - (int) ($newBooking->advance_amount ?? 0) > 0){
                 $note = ($request->payment_method && @$request->advance_amount) ? "[".$newBookingId."] booking advance payment" : "[".$newBookingId."] Commission to Guide" ;
                 $plutusId = $this->createPlutusEntry($request->hotel_id,$note,now(),$request->advance_amount,$newBooking);
             } 
             
             /*Manage Commission Ledger to handle commission at time of booking (ABHI-7-12)*/
             if(!$request->id && @$request->commission_to && $request->commission_amount > 0){
                 $msg = "Commission to guide during booking of $newBookingId";
                 $commission = $this->manageCommissionLedger($newBooking, $request->hotel_id, $request->commission_amount, $msg,$request->commission_to,$plutusId,$request->advance_amount);
             }

            if ($request->payment_method && @$request->advance_amount) {
                $roundOff = 0;
                if ($request->paid_amount && $request->paid_amount < 1 && abs($request->paid_amount) < 2) {
                    $roundOff = $request->paid_amount;
                    $reason = "Round off during booking of $newBookingId";
                    $this->manageRoundOffLedger(
                        $newBooking,
                        $request->hotel_id,
                        abs($request->paid_amount),
                        $reason,
                        $request->paid_amount < 0
                    );
                }

                if($request->bank_charges != "" && $request->bank_charges > 0){
                    $msg = "Bank Charges during booking of $newBookingId";
                    $bankCharge = $this->manageBankChargesLedger($newBooking, $request->hotel_id, $request->bank_charges, $msg,$customerId,$plutusId);
                }
                if(!$request->id && @$request->commission_to && $request->commission_amount > 0){
                    $commissionAmount = $request->commission_amount;
                } else {
                    $commissionAmount = 0;
                }
                $accountTransaction = $this->creditBalanceInAccount($request->payment_method,
                    $request->advance_amount, $newBooking, null, $request->hotel_id, null, $roundOff,$customerId,$plutusId,$request->bank_charges,null,$commissionAmount);

                /*Manage Multiple Payment at a time (ABHI-7-12)*/

                if($request->payment_method1 !== null && $request->is_extra_payment_method && $request->advance_amount1 > 0){
                    if($request->id){
                        $bookingAmount = $request->advance_amount - $newBooking->advance_amount;
                        $bookingAmount1 = $request->advance_amount1 - ($newBooking->advance_amount + $bookingAmount);
                        // $advanceBookingAmount = abs($bookingAmount1 - $bookingAmount);
                        $advanceAmountOption = $request->advance_amount + ($bookingAmount1 - $bookingAmount);
                    } else {
                        $advanceAmountOption = $request->advance_amount1;
                    }
                    
                    if($request->bank_charges1 != "" && $request->bank_charges1 > 0){
                        $msg = "Bank Charges during booking of $newBookingId";
                        $bankCharge1 = $this->manageBankChargesLedger($newBooking, $request->hotel_id, $request->bank_charges1, $msg,$customerId,$plutusId);
                    }

                    $accountTransaction1 = $this->creditBalanceInAccount($request->payment_method1,
                    $advanceAmountOption, $newBooking, null, $request->hotel_id, null, $roundOff,$customerId,$plutusId,$request->bank_charges1,null,0);

                }
            }

            /*Handle Advance Payment Option*/
            if ($request->is_extra_payment_method && $request->id) {
                if ($request->advance_amount1 != $request->advance_amount) {
                    $advanceAmountOption = abs($request->advance_amount1 - $request->advance_amount);
                } else {
                    $advanceAmountOption = 0;
                }
            }

            $newBooking->booking_number = $newBookingId;
            $newBooking->customer_id = @$request->booked_by;
            $newBooking->total_room = $request->total_room;
            $newBooking->advance_amount = $request->bank_charges1 + $request->bank_charges + $advanceAmountOption + $request->advance_amount ?? 0;
            $newBooking->advance_remarks = @$request->advance_remarks;
            $newBooking->paid_amount = $request->paid_amount <= 0 ? 0 : $request->paid_amount;
            $newBooking->total_price = $request->total_price;
            $newBooking->payment_method = $request->payment_method;
            $newBooking->discount_type = $request->discount_type;
            $newBooking->remarks = $request->remarks;
            $newBooking->discount_amount = $request->discount_amount;
            $newBooking->commission_amount =($request->commission_amount != null) ? $request->commission_amount : 0;
            $newBooking->commission_to = @$request->commission_to ? $request->commission_to : NULL;
            $newBooking->full_guest_name = $request->full_guest_name;
            $newBooking->special_request = $request->special_request;
            $newBooking->comments = $request->comments;
            $newBooking->purpose = $request->purpose;
            $newBooking->booked_from = auth()->user()->account_role;
            $newBooking->final_gst_rates = @$request->final_gst_rates;
            if($request->type == 2){
                $newBooking->client_booking_status = $request->client_booking_status;
            } else {
                $newBooking->client_booking_status = $request->advance_amount > 0 ? $request->client_booking_status : 1;
            }
            

            /**** price booking status  ******/
            $newBooking->booking_status_main = $request->type ? (int)$request->type + 1 : 2;

            /*  end user  status for booking  */
            $newBooking->booking_date = $customDateTime;
            $newBooking->check_in_date = $date1;
            $newBooking->check_out_date = $date2;
            $newBooking->hotel_id = $request->hotel_id;
            $newBooking->booked_from = Auth::user()->account_role;
            $newBooking->booked_from = Auth::user()->account_role;
            $newBooking->booking_source = $request->booking_source;
            $newBooking->booking_type_id = @$request->booking_type_id;
            $newBooking->arrival_from = $request->arrival_from;
            $newBooking->tax_included = @$request->tax_included;
            $newBooking->save();

            $lastInsertId = $newBooking->id;

            if ($request->payment_method && @$request->advance_amount && isset($accountTransaction) && count($accountTransaction)) {
                foreach ($accountTransaction as $transaction) {
                    $transaction->booking_id = $lastInsertId;
                    $transaction->save();
                }
            }
            /*save booking id for extra payment*/
            if ($request->is_extra_payment_method && $request->payment_method && @$request->advance_amount1 && isset($accountTransaction1) && count($accountTransaction1)) {
                foreach ($accountTransaction1 as $transaction) {
                    $transaction->booking_id = $lastInsertId;
                    $transaction->save();
                }
            }

            if (!$request->id && @$request->commission_to && $request->commission_amount > 0 && isset($commission) && count($commission)) {
                foreach ($commission as $transaction) {
                    $transaction->booking_id = $lastInsertId;
                    $transaction->save();
                }
            }

            if ($request->bank_charges != "" && $request->bank_charges > 0 && isset($bankCharge) && count($bankCharge)) {
              
                foreach ($bankCharge as $transaction) {
                    $transaction->booking_id = $lastInsertId;
                    $transaction->save();
                }
            }
            if ($request->bank_charges1 != "" && $request->bank_charges1 > 0 && isset($bankCharge1) && count($bankCharge1)) {
                
                foreach ($bankCharge1 as $transaction) {
                    $transaction->booking_id = $lastInsertId;
                    $transaction->save();
                }
            }
            /*--------------------- INSERT CUSTOMER DATA -------------------------------*/
            if (isset($request->customer) && count($request->customer) > 0) {
                $customer = $request->customer;
                foreach ($customer as $item) {
                    if (!$item['customer_id']) {
                        continue;
                    }
                    $hotelFacility = ClinetBookingDetails::firstOrNew(['id' => @$item['id']]);
                    $hotelFacility->customer_id = $item['customer_id'];
                    $hotelFacility->booking_id = $lastInsertId;
                    $hotelFacility->save();
                }
            }

            if (isset($request->rooms) && count($request->rooms) > 0) {
                $rooms = $request->rooms;
                
                foreach ($rooms as $key => $room) {

                    if(!$request->id){
                        if($room['noOfRooms'] == 1){        
                            $this->addRoomsForBooking($lastInsertId,$room,$request,0);
                        } else {
                            for($i=0; $i<=$room['noOfRooms']-1; $i++){
                                $this->addRoomsForBooking($lastInsertId,$room,$request,$i);
                                // unset($room[$key]['totalRoom'][$i]);
                                // array_values($room[$key]['totalRoom']);
                            }
                        }
                    } else {
                        $this->addRoomsForBooking($lastInsertId,$room,$request,0);
                    }
                } 
                    // $hotelFacilityLastInsert = $hotelBooking->id;

                    /*---------------  Booking facilities ------------------------*/
                    // if (!empty($room['complementary']) && count($room['complementary']) > 0) {
                        
                    //     foreach ($room['complementary'] as $complementary) {
                    //         $hotelFacility = BookingComplementray::firstOrNew(['id' => @$complementary['complementary_id']]);
                    //         $hotelFacility->complementary_id = $complementary['room_facility_id'];
                    //         $hotelFacility->booking_Detail_id = $hotelFacilityLastInsert;
                    //         $hotelFacility->quantity = $room['extra_facility_days'];
                    //         $hotelFacility->save();
                    //     }

                    //     /*Update Price*/
                    //     foreach ($room['complementary_price'] as $complementaryPrice) {
                    //         $hotelFacilityPrice = BookingComplementray::where([
                    //             'complementary_id' => @$complementaryPrice['facilityId'],
                    //             'booking_Detail_id' => $hotelFacilityLastInsert
                    //         ])->first();

                    //         if ($hotelFacilityPrice) { 
                    //             $hotelFacilityPrice->modified_rate = $complementaryPrice['price'];
                    //             $hotelFacilityPrice->save();
                    //         }
                    //     }
                    // }
                
                //                }
            }
            DB::commit();
            return $this->responseWithSuccess('booking added successfully!');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->responseWithError($e->getMessage().' -- '.$e->getLine());
        }
    }
    public function save_booking_app(Request $request)
    {
        try {
            DB::beginTransaction();

            $booking = new Booking();
            $selectedRoom = $request->input('selected_room');
            dd($selectedRoom);
            if ($request->has('selected_room') && $selectedRoom !== null) {
                // Handle selected_room case
                $lstBookingData = Booking::latest()->first();
                $getBookingData = Booking::where('id', $request->id)->first();
                $newBookingId = $getBookingData ? $getBookingData->booking_number : $this->generateBookingId($lstBookingData, $request->hotel_id);
                $booking->BookingId = $newBookingId;
                $booking->startDate = $request->input('startDate');
                $booking->endDate = $request->input('endDate');
            } else {
                // Handle non-selected_room case
                $lstBookingData = Booking::latest()->first();
                $getBookingData = Booking::where('id', $request->id)->first();
                $newBookingId = $getBookingData ? $getBookingData->booking_number : $this->generateBookingId($lstBookingData, $request->hotel_id);
                $booking->hotel_id = $request->input('hotel_id');
                $booking->check_in_date = $request->input('startDate');
                $booking->check_out_date = $request->input('endDate');
                $booking->subtotal = $request->input('subtotal');
                $booking->total_price = $request->input('total_amount');
                $booking->total_room = $request->input('total_room');
                // $booking->adults = $request->input('adults');
                // $booking->children = $request->input('children');
                // $booking->mealPlan = $request->input('mealPlan');
                // $booking->extra_bed = $request->input('extra_bed');
                // $booking->price = $request->input('price');
            }
            $booking->save();

            DB::commit();

            return response()->json(['message' => 'Booking saved successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => "Booking not saved successfully"]);
        }
    }
    protected function creditBalanceInAccount($paymentId, $amount, $booking = null, $checkOut = null, $hotelId = null, $accPayableRequired = false, $actualRemaining = 0, $customerId = null , $plutusId = null,$bankCharge,$date = null,$commission) {
        
        $bookingNumber = @$booking->booking_number ?? @$checkOut->booking_number;
        if (!$bookingNumber) {
            $lstBookingData = Booking::latest()->first();
            $hotel_id = @$booking->hotel_id ?? @$checkOut->hotel_id ?? $hotelId;
            $bookingNumber = $this->generateBookingId($lstBookingData, $hotel_id);
        }
        // echo $paymentId; exit();
        $query = LedgerAccount::find($paymentId);
        $account = $query->getAccoutnbalance;
        $isBank = $query->is_bank;
        $credit = (int) $amount;
        if ($booking) {
            $credit = (int) $amount - (int) ($booking->advance_amount ?? 0);
        }
        $accountTransaction = [];
        if ($account && $credit > 0) {

            $accountTransaction[] = $account->balanceTransactions()->create([
                'amount' => $credit,
                'reason' => $checkOut ? "[$bookingNumber] booking checkout payment - {$checkOut->advance_remarks}"
                    : "[$bookingNumber] booking payment - " . ($booking ? $booking->advance_remarks : ''),
                'type' => 1,
                'transaction_date' => ($date != null) ? $date : now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'note' => @$checkOut->advance_remarks,
                'booking_id' => @$booking->id ?? @$checkOut->id,
                'hotel_id' => @$booking->hotel_id ?? @$checkOut->hotel_id ?? $hotelId,
                'customer_id'=> $customerId,
                'plutus_entries_id' => $plutusId,
                'bank_charges' => ($isBank == 1) ? $bankCharge : 0
            ]);

            if ($booking || $accPayableRequired) {
                $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-PAYABLE')->first();
                if (!$advanceAccount) {
                    $advanceAccount = LedgerAccount::whereHas('ledgerCategory', function ($q) {
                        $q->where('name', 'Account Payable');
                    })->first();
                }
                $remainingAfterCommission = ($credit + floatval($actualRemaining) + $bankCharge) - $commission;
                if ($advanceAccount && $remainingAfterCommission > 0) {

                    $advanceAccount = $advanceAccount->getAccoutnbalance;

                    $advanceAccount && $accountTransaction[] = $advanceAccount->balanceTransactions()->create([
                        'amount' => ($credit + floatval($actualRemaining) + $bankCharge) - $commission,
                        'reason' => "[$bookingNumber] booking advance payment",
                        'type' => 1,
                        'transaction_date' => now(),
                        'cheque_no' => null,
                        'receipt_no' => null,
                        'created_by' => auth()->user()->id,
                        'status' => 1,
                        'booking_id' => @$booking->id,
                        'hotel_id' => @$booking->hotel_id ?? $hotelId,
                        // 'customer_id'=> @$booking->customer_id ?? @$lstBookingData->customer_id ?? $customerId
                        'customer_id' => $customerId,
                        'plutus_entries_id' => $plutusId
                    ]);
                }
            }
        }

        return $accountTransaction;
    }

    //Insert Into Plutus due to Grouping the Account Transaction and return the last Inserted Id to store into table

    protected function createPlutusEntry($hotelId, $note, $date, $amount, $booking, $checkOut = NULL)
    {

        $credit = (int) $amount;
        if ($booking) {
            $credit = (int) $amount - (int) ($booking->advance_amount ?? 0);
        }
        if ($checkOut) {
            $credit = (int) $amount;
        }

        $createPlutus = PlutusEntries::create([
            'hotel_id' => $hotelId,
            'note' => $note,
            'date' => $date,
            'amount' => $credit,
        ]);

        return $createPlutus->id;
    }

    /*Insert Rooms*/
    protected function addRoomsForBooking($bookingId, $room , $request , $key){
    //    dd($room);
        if ($room['id'] != '') {
            if ($room['room_id'] == NULL) {
                $room_id = $room['room']['id'];
            } else {
                $room_id = (@$room['room_id']['id']) ? $room['room_id']['id'] : $room['room_id'];
            }
        } else {
            $totalRoomIds = [];

            foreach($room['totalRoom'] as $roomId){
                $checkRoomAlreadyAssign = BookingDetails::where('booking_id',$bookingId)->where('room_id',$roomId['id'])->count();
                
                if($checkRoomAlreadyAssign == 0){
                    $room_id = $roomId['id'];
                    break;
                }
            }

            // $checkRoomAlreadyAssign = BookingDetails::where('booking_id',$bookingId)->where('room_id',$room['totalRoom'][$key]['id'])->count();
            // if($checkRoomAlreadyAssign){
            //     $randomKey = count($room['totalRoom']) -1;
            //     $room_id = $room['totalRoom'][$randomKey]['id'];
            // } else {
            //     $room_id = $room['totalRoom'][$key]['id'];
            // }
        }
        
        $hotelBooking = BookingDetails::firstOrNew(['id' => @$room['id']]);
        $hotelBooking->room_id = $room_id;
        $hotelBooking->booking_status = 5;
        if ($request->advance_amount > 0) {
            $hotelBooking->booking_status = $request->type ? (int)$request->type + 1 : 2;
        }
        $hotelBooking->adult = $room['adult'];
        $hotelBooking->children = $room['children'];
        $hotelBooking->infant = $room['infant'];
        $hotelBooking->room_no = $room_id;
        $hotelBooking->meal_plan_id = @$room['meal_id'] ?? @$room['meal_plan_id'] ?? 1;
        $hotelBooking->meal_plan_persons = @$room['extra_meal_plan'] ?? @$room['meal_plan_persons'];
        $hotelBooking->extra_bed = $room['extra_bed'];
        $hotelBooking->extra_person = $room['extra_person'];
        $hotelBooking->extra_child = $room['extra_child'];
        $hotelBooking->extra_facility_days = $room['extra_facility_days'];
        $hotelBooking->booking_id = $bookingId;
        $hotelBooking->room_tax = $room['room_tax'];
        $hotelBooking->meal_plan_tax = ($room['id'] != '') ? $room['meal_plan_tax'] : @$room['meal_plan_tax'] / $room['noOfRooms'] ?? 0;
        $hotelBooking->facility_tax = $room['facility_tax'];
        $hotelBooking->extra_bed_tax = $room['extra_bed_tax'];
        $hotelBooking->extra_person_tax = $room['extra_person_tax'];
        $hotelBooking->extra_bed_price = $room['extra_bed_price'];
        $hotelBooking->extra_person_price = $room['extra_person_price'];
        $hotelBooking->room_rate = $room['room_rate'];
        $hotelBooking->modified_room_rate = @$room['modified_room_rate'];
        $hotelBooking->discount = @$room['discount'];
        $hotelBooking->save();

    }

    protected function generateBookingId($lastBookingData, $hotelId)
    {

        //Set Booking Prefix With Actual Hotel Name Instead of Invoice Prefix at the time of booking (28-11-23 Abhi)

        $hotelName = Hotel::where('id', $hotelId)->get();
        $hotel = str_replace('Hotel', '', $hotelName[0]->hotel_name);
        $hotel = ($hotelName[0]->hotel_prefix !== null) ? $hotelName[0]->hotel_prefix : Str::slug($hotel);

        return $lastBookingData && !empty($lastBookingData) ? $this->bookingPrefix() . '-' . $hotel . '-' . (intval($lastBookingData->id) + 1) : $this->bookingPrefix() . '-' . $hotel . '-1';

        // return $lastBookingData && !empty($lastBookingData) ? $this->invoicePrefix().'-Hotel-'.(intval($lastBookingData->id)+1) : $this->invoicePrefix().'Hotel-1';
    }

    public function show(Booking $booking)
    {
        try {
            $booking = Booking::where('id', $booking->id)->with([
                'Customer', 'Source', 'BookingType', 'Hotel', 'BookingDetails.room',
                'BookingDetails.room.Roomcategory', 'BookingDetails.room.Bed', 'BookingDetails.mealPlanDetails.mealname',
                'BookingDetails.mealPlanDetails.taxRate.taxName', 'bokingStatus', 'customers',
                'BookingDetails.complementrays.paidFacility.facilityRate' => function ($q) use ($booking) {
                    $q->where('hotel_id', $booking->hotel_id);
                }
            ])->first();
            if (@$booking) {
                return new CommonResource($booking);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function getRestaurantOrderDetails(Booking $booking)
    {
        return $this->getBookingOrderDetails($booking);
    }

    public function bookingList()
    {
        return CommonResource::collection(Room::with('bookingDetails', 'Roomcategory')->get());
    }

    public function roomCheckInOutDetails(Request $request)
    {
        $condition = $request->status; //0=hold,1=cancel,2=booked,3=checkin,4=checkout,5=pending_payment
        $request = request();
        $rooms = Room::with(['roomCheckInOut' => function ($query) use ($condition) {
            $query->where('booking_status', $condition);
        }])->paginate($request->perPage);

        return CommonResource::collection($rooms);
    }

    public function Booking_status_get()
    {
        try {
            $bookinStatus = BookingStatus::get();
            if (@$bookinStatus) {
                return new CommonResource($bookinStatus);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }


    public function checkoutView(Request $request, Booking $booking)
    {
        try {
            $booking = Booking::where('id', $booking->id)->with([
                'Customer', 'Source', 'Hotel', 'BookingDetails.room', 'BookingDetails.room.Roomcategory',  'bokingStatus',
                'BookingDetails.room.Bed', 'BookingDetails.mealPlanDetails.mealname', 'BookingDetails.complementrays.paidFacility.facilityRate' => function ($q) use ($booking) {
                    $q->where('hotel_id', $booking->hotel_id);
                }
            ])->first();
            if (@$booking) {
                return new CheckoutResource($booking);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function checkOutRoomView(Request $request, $roomId)
    {
        // dd($request->input());
        $hotelId = $request->hotel_id;
        try {
            $booking = BookingDetails::where('id', $roomId)->with([
                'booking', 'room', 'room.Roomcategory', 'mealPlanDetails.mealname',
                'complementrays.paidFacility.facilityRate' => function ($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId);
                }
            ])->first();
            if (@$booking) {
                return new CheckOutRoomResource($booking);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function checkout(Request $request, Booking $booking)
    {
        DB::beginTransaction();
        try {
            
            $input = $request->all();
            
            $booking->bookingDetails()
                ->whereIn('id', $input['selected_rooms'])
                ->update(['booking_status' => Booking::CHECKOUT]);

            $paymentId = @$input['credit_payment_method'] ?? $booking->credit_payment_method;
            $creditAmt = (int) (@$input['credit_amount'] ?? 0) + (@$booking->credit_amount ?? 0);

            $paymentId1 = '';
            $creditAmt1 = 0;
            if ($request->input('is_extra_payment_method')) {
                $paymentId1 = @$input['credit_payment_method1'] ?? $booking->credit_payment_method1;
                $creditAmt1 = (int) (@$input['credit_amount1'] ?? 0) + (@$booking->credit_amount1 ?? 0);
            }

            $extraCharge = intVal(@$input['extra_charge'] ?? 0) + intVal(@$booking->extra_charge ?? 0);
            $due = @$booking->paid_amount + $extraCharge - (@$input['credit_amount'] ?? 0) - $creditAmt1 - $request->bank_charges - $request->bank_charges1;
            if (floatval($due) <= 1 && floatval($due) > 0) {
                $input['credit_amount'] = $input['credit_amount'] + 1;
            }

            $data = [
                'extra_charge' => $extraCharge,
                'extra_charge_comment' => @$input['extra_charge_comment'],
                'special_discount_amount' => @$input['special_discount'] ?? $booking->special_discount_amount,
                'special_discount_type' => @$input['special_discount_type'] ?? $booking->special_discount_type,
                'credit_amount' => $creditAmt + $creditAmt1 + $request->bank_charges + $request->bank_charges1,
                'advance_remarks' => @$input['advance_remarks'],
                'credit_payment_method' => $paymentId,
                'special_discount_rate' => @$input['special_discount_rate'] ?? $booking->special_discount_rate,
                'remaining_amount' => floatval(@$input['remaining_amount'] ?? 0) <= 1 ? 0 : @$input['remaining_amount'],
                'paid_amount' => floatval($due ?? 0) <= 1 ? 0 : $due,
            ];
            
            $checkOutPendingRoom = $booking->bookingDetails()
                ->where('booking_status', '!=', Booking::CHECKOUT)
                ->exists();

            if (!$checkOutPendingRoom) {
                $data['booking_status_main'] = Booking::CHECKOUT;
            }
            if ($creditAmt > 0) {
                $note = "[$booking->booking_number] Booking Checkout & Tax Payment";
                $plutusId = $this->createPlutusEntry($booking->hotel_id,$note,now(),$request->input('credit_amount'),$booking,'checkout');
                $this->creditBalanceInAccount($paymentId, $creditAmt, null, $booking, $booking->hotel_id, $checkOutPendingRoom, $input['actual_remaining_amount'],$booking->customer_id,$plutusId,$request->bank_charges,null,0);
                
                /*Manage Bank Charges Ledger*/
                if($request->bank_charges != "" && $request->bank_charges > 0){
                    $msg = "Bank Charges during booking of $booking->booking_number";
                    $this->manageBankChargesLedger($booking, $booking->hotel_id, $request->bank_charges, $msg,$booking->customer_id,$plutusId);
                }
                /*Extra Payment Method*/

                if($paymentId1 !='' && $creditAmt1 > 0){
                    $this->creditBalanceInAccount($paymentId1, $creditAmt1, null, $booking, $booking->hotel_id, $checkOutPendingRoom, $input['actual_remaining_amount'],$booking->customer_id,$plutusId,$request->bank_charges1,null,0); 
                }
                /*Manage Bank Charges Ledger For Extra Payment Method*/
                if($request->bank_charges1 != "" && $request->bank_charges1 > 0){
                    $msg = "Bank Charges during booking of $booking->booking_number";
                    $this->manageBankChargesLedger($booking, $booking->hotel_id, $request->bank_charges1, $msg,$booking->customer_id,$plutusId);
                }

                if (!$input['remaining_amount'] && abs($input['actual_remaining_amount']) <= 1) {
                    $reason = "Round off during checkout of $booking->booking_number";
                    $this->manageRoundOffLedger(
                        $booking,
                        $booking->hotel_id,
                        abs($input['actual_remaining_amount']),
                        $reason,
                        $input['actual_remaining_amount'] < 0
                    );
                }

                /*Manage Restaurant Order Status & Order Payment Status*/
                if(!empty($input['restaurantOrderId'])){
                    foreach($input['restaurantOrderId'] as $resOrderId){
                        $resOrder = Restroorder::where('id',$resOrderId)->first();
                        $resOrder->order_status = 1;
                        $resOrder->payment_status = 1;
                        $resOrder->save();
                    }
                }
            }

            $booking->update($data);
            $invoice = 0;
            if (!$checkOutPendingRoom) {
                
                if(!@$plutusId){
                    $note = "[$booking->booking_number] Booking Checkout & Tax Payment";
                    $plutusId = $this->createPlutusEntry($booking->hotel_id,$note,now(),$request->input('credit_amount'),$booking,'checkout');
                }

                $bankCharges = $request->bank_charges + $request->bank_charges1;
                $invoice = $this->createInvoice($booking, $request->invoicePerson);
                $this->manageLedgerAfterCheckout($booking, $creditAmt, $input['actual_remaining_amount'],$request->invoicePerson,$plutusId,$creditAmt1,$bankCharges);
            }
            DB::commit();
            return $this->responseWithSuccess('checkout successfully!', ['invoice' => $invoice]);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->responseWithError($e->getMessage());
        }
    }

    public function manageLedgerAfterCheckout($booking, $creditAmt, $actualRemaining = 0,$invoicePerson,$plutusId = NULL,$creditAmt1,$bankCharges)
    {
        
        // Manage GST
       
        $gstRates = $booking->final_gst_rates ?? [];
        $totalGstRate = 0;

        $orders = $booking->restaurantOrders;
        if (count($orders)) {
            foreach ($orders as $order) {
                $totalGstRate += floatval($order->tax ?? 0);
            }
        }
        foreach ($gstRates as $name => $rate) {
            if ($rate) {
                $totalGstRate += $rate;
                $vat = VatRate::where('name', $name)->first();

                $vat && $vat->taxCalculations()->create([
                    'output' => $rate,
                    'type' => 'Booking',
                    'reference' => $booking->id,
                ]);
            }
        }

        $restOrdersTax = @$booking->restaurantOrders()->sum('tax');
        $totalGstRate += floatval($restOrdersTax ?? 0);
        if ($totalGstRate) {
            $outputGst = LedgerAccount::where('code', 'GST-OUTPUT')->first();
            if ($outputGst) {
                $outputGst = $outputGst->getAccoutnbalance;
                $outputGst && $outputGst->balanceTransactions()->create([
                    'amount' => $totalGstRate,
                    'reason' => "[$booking->booking_number] booking tax",
                    'type' => 1,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'booking_id' => @$booking->id,
                    'reference' => 'Booking',
                    'hotel_id' => @$booking->hotel_id,
                    'plutus_entries_id' => $plutusId
                ]);
            }
        }
        // Manage payable
        $totalPayable = 0;
        $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-PAYABLE')->first();
        $advanceAccount = $advanceAccount->getAccoutnbalance;
        $transactions = $advanceAccount ? $advanceAccount->balanceTransactions()->where('booking_id', $booking->id)->get() : [];
        if (count($transactions)) {
            $totalPayable = $transactions->sum('amount');
            $totalPayable && $advanceAccount->balanceTransactions()->create([
                'amount' => $totalPayable,
                'reason' => "[$booking->booking_number] booking room checkout",
                'type' => 0,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'booking_id' => @$booking->id,
                'hotel_id' => @$booking->hotel_id,
                'customer_id' => $invoicePerson ?? ($booking->booking_source ?? $booking->customer_id),
                'plutus_entries_id' => $plutusId
            ]);
        }

        //Start Update Customer As Per Invoice Person in Account Transaction (Abhi 24-11)
        $commissionAccount = LedgerAccount::where('code','commission-payable')->first();
        AccountTransaction::where('booking_id', $booking->id)->whereNotIn('account_id',[$commissionAccount->id])
                ->update(['customer_id' => $invoicePerson ?? ($booking->booking_source ?? $booking->customer_id)]);

        //End Update Customer As Per Invoice Person in Account Transaction (Abhi 24-11)

        //manage receivable
        if ($booking->paid_amount && intval($booking->paid_amount) > 1) {
            $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-RECEIVABLE')->first();
            $advanceAccount = $advanceAccount->getAccoutnbalance;
            if ($advanceAccount) {
                $advanceAccount->balanceTransactions()->create([
                    'amount' => $booking->paid_amount,
                    'reason' => "[$booking->booking_number] booking pending amount",
                    'type' => 1,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'booking_id' => @$booking->id,
                    'hotel_id' => @$booking->hotel_id,
                    'customer_id' => $invoicePerson ?? ($booking->booking_source ?? $booking->customer_id),
                    'plutus_entries_id' => $plutusId
                ]);
            }
        }

        //Handle Restaurant order
        $restOrdersTotal = @$booking->restaurantOrders()->sum('total_amount');
        $restOrdersTotal = floatval($restOrdersTotal ?? 0) - floatval($restOrdersTax ?? 0);

        if ($restOrdersTotal) {
            $restRevenueAccount = LedgerAccount::where('code', 'RESTAURANT-REVENUE')->first();
            $restRevenueAccount = $restRevenueAccount->getAccoutnbalance;
            $restRevenueAccount->balanceTransactions()->create([
                'amount' => $restOrdersTotal ?? 0,
                'reason' => 'Restaurant order associated with booking [' . $booking->booking_number . ']',
                'type' => 1,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'booking_id' => @$booking->id,
                'hotel_id' => @$booking->hotel_id,
            ]);
        }

        //manage revenue
        if($booking->paid_amount && $booking->paid_amount > 1){
            $totalRevenue = floatVal($bankCharges ?? 0) + floatVal($creditAmt ?? 0) + floatVal($creditAmt1 ?? 0) + floatval($totalPayable ?? 0) - floatval($totalGstRate ?? 0)
            + floatval($booking->paid_amount) - floatval($booking->extra_charge) - floatval($restOrdersTotal);
        } else {
            $totalRevenue = floatVal($bankCharges ?? 0) + floatVal($creditAmt ?? 0) + floatVal($creditAmt1 ?? 0) + floatval($totalPayable ?? 0) - floatval($totalGstRate ?? 0)
            + floatval($booking->paid_amount) - floatval($booking->extra_charge) - floatval($restOrdersTotal) + floatval($actualRemaining);
        }
        
        if ($totalRevenue > 0) {
            $revenueAccount = LedgerAccount::where('code', 'OPERATING_REVENUE')->first();
            $revenueAccount = $revenueAccount->getAccoutnbalance;
            if ($revenueAccount) {
                $revenueAccount->balanceTransactions()->create([
                    'amount' => $totalRevenue,
                    'reason' => "[$booking->booking_number] booking revenue",
                    'type' => 1,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'booking_id' => @$booking->id,
                    'hotel_id' => @$booking->hotel_id,
                    'customer_id' => $invoicePerson ?? ($booking->booking_source ?? $booking->customer_id),
                    'plutus_entries_id' => $plutusId
                ]);
            }
        }

        if ($booking->extra_charge && intval($booking->extra_charge) >= 1) {
            $revenueAccount = LedgerAccount::where('code', 'CHARGES')->first();
            $revenueAccount = $revenueAccount->getAccoutnbalance;
            if ($revenueAccount) {
                $revenueAccount->balanceTransactions()->create([
                    'amount' => intval($booking->extra_charge),
                    'reason' => "[$booking->booking_number] booking extra charge",
                    'type' => 1,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'booking_id' => @$booking->id,
                    'hotel_id' => @$booking->hotel_id,
                    'customer_id' => $invoicePerson ?? ($booking->booking_source ?? $booking->customer_id),
                    'plutus_entries_id' => $plutusId
                ]);
            }
        }
    }

    public function createInvoice($booking, $invoicePerson)
    {
        DB::beginTransaction();
        try {
            $lastInvoice = Invoice::latest()->first();
            $number = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $hotel = str_replace('hotel', '', $booking->hotel->hotel_name);
            $hotel = str_replace('Hotel', '', $hotel);
            $hotel = ($booking->hotel->hotel_prefix !== null) ? $booking->hotel->hotel_prefix : Str::slug($hotel);
            $code = "$hotel-$number";
            $restOrdersTax = @$booking->restaurantOrders()->sum('tax');

            $userId = auth()->user()->id;
            $due = @$booking->paid_amount ?? 0;
            $isPaid = $due <= 1;
            $tax = (float) $booking->totalGst() + floatval($restOrdersTax ?? 0);
            $subTotal = (int) $booking->total_price + (int) $booking->extra_charge - (int) $booking->discount_amount - (int) $booking->special_discount_amount - $tax;

            // create invoice
            $invoice = Invoice::create([
                'invoice_no' => $code,
                'reference' => "booking number $booking->booking_number",
                'slug' => uniqid(),
                'client_id' => $invoicePerson ?? ($booking->booking_source ?? $booking->customer_id),
                'sub_total' => $subTotal,
                'invoice_date' => Carbon::now(),
                'status' => 1,
                'tax_value' => $tax,
                'is_paid' => $isPaid,
                'created_by' => $userId,
                'hotel_id' => $booking->hotel_id
            ]);

            $accountTransactions = $booking->accountTransactions()->whereHas('cashbookAccount', function ($query) {
                $query->whereHas('ledgerAccount', function ($q) {
                    $q->where('is_bank', 1);
                });
            })->get();
            $booking->invoice_id = $invoice->id;
            $booking->save();

            foreach ($accountTransactions as $accountTransaction) {
                if ($accountTransaction) {
                    InvoicePayment::create([
                        'slug' => uniqid(),
                        'invoice_id' => $invoice->id,
                        'transaction_id' => $accountTransaction->id,
                        'amount' => $accountTransaction->amount + $accountTransaction->bank_charges,
                        'date' => Carbon::parse($accountTransaction->transaction_date),
                        'note' => "booking number $booking->booking_number - {$booking->advance_remarks}",
                        'created_by' => $accountTransaction->created_by,
                        'status' => 1,
                    ]);
                }
            }

            DB::commit();
            return $invoice->slug;
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseWithError($e->getMessage());
        }
    }

    public function destroy(Booking $booking)
    {
        try {
            $booking->accountTransactions()->delete();
            $booking->delete();
            return $this->responseWithSuccess('Booking deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function cancel(Request $request, Booking $booking)
    {
        try {
            
            $booking->update(['booking_status_main' => 1]);
            
            $bookingDetail = BookingDetails::where('booking_id',$booking->id)->update([
                'booking_status' => 1
            ]);

            $advance = floatval($booking->advance_amount);
            $chargeRate = floatval($request->charge ?? 0);
            $accountId = $request->account_id;


            if ($advance > 0 || $request->charge) {
                $note = "[$booking->booking_number] Booking Cancellation and Charges";
                $plutusId = null;
                if($advance > 0){
                    $plutusId = $this->createPlutusEntry($booking->hotel_id, $note, now(), $advance, $booking);
                }
                // $plutusId = $this->createPlutusEntry($booking->hotel_id, $note, now(), $advance, $booking);
                $this->manageCancelBookingLedger($booking, $chargeRate, $plutusId,$accountId);
            }
            return $this->responseWithSuccess('Booking canceled successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function manageCancelBookingLedger($booking, $rate = 0, $plutusId,$accountId)
    {
        $paymentId = $booking->payment_method;
        $advance = floatval($booking->advance_amount ?? 0);
        $cancellationCharge = floatval($rate ?? 0);
        $refund = ($advance - $cancellationCharge) ?? 0;

        if ($advance >= 1) {
            $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-PAYABLE')->first();
            $advanceAccount = $advanceAccount->getAccoutnbalance;
            if ($advanceAccount) {
                $advanceAccount->balanceTransactions()->create([
                    'amount'           => ($accountId != null) ? $advance : $cancellationCharge,
                    'reason'           => "[$booking->booking_number] Booking cancellation",
                    'type'             => 0,
                    'transaction_date' => now(),
                    'cheque_no'        => null,
                    'receipt_no'       => null,
                    'created_by'       => auth()->user()->id,
                    'status'           => 1,
                    'booking_id'       => @$booking->id,
                    'hotel_id'         => @$booking->hotel_id,
                    'customer_id'      => @$booking->booking_source ? $booking->booking_source : @$booking->customer_id,
                    'plutus_entries_id'   => $plutusId
                ]);
            }
        }

        if ($refund >= 1 && $accountId != null) {
            $account = LedgerAccount::find($accountId)->getAccoutnbalance;
            // $account = LedgerAccount::find($paymentId)->getAccoutnbalance;
            $account->balanceTransactions()->create([
                'amount'           => abs($refund),
                'reason'           => "[$booking->booking_number] Booking Refund",
                'type'             => 0,
                'transaction_date' => now(),
                'cheque_no'        => null,
                'receipt_no'       => null,
                'created_by'       => auth()->user()->id,
                'status'           => 1,
                'booking_id'       => @$booking->id,
                'hotel_id'         => @$booking->hotel_id,
                'customer_id'      => @$booking->booking_source ? $booking->booking_source : @$booking->customer_id,
                'plutus_entries_id'   => $plutusId
            ]);
        }

        if ($cancellationCharge > 0 && $booking->booking_source) {
            $revenueAccount = LedgerAccount::where('code', 'CHARGES')->first();
            $revenueAccount = $revenueAccount->getAccoutnbalance;
            if ($revenueAccount) {
                $revenueAccount->balanceTransactions()->create([
                    'amount' => $cancellationCharge,
                    'reason' => "[$booking->booking_number] Booking cancellation charge",
                    'type' => 1,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'booking_id'  => @$booking->id,
                    'hotel_id'    => @$booking->hotel_id,
                    'customer_id' => @$booking->booking_source ? $booking->booking_source : @$booking->customer_id,
                    'plutus_entries_id' => $plutusId
                ]);
            }

            if ($refund < 0) {
                $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-RECEIVABLE')->first();
                $advanceAccount = $advanceAccount->getAccoutnbalance;
                if ($advanceAccount) {

                    $acc = $advanceAccount->balanceTransactions()->create([
                        'amount' => abs($refund),
                        'reason' => "[$booking->booking_number] Booking cancellation charge receivable",
                        'type' => 1,
                        'transaction_date' => now(),
                        'cheque_no' => null,
                        'receipt_no' => null,
                        'created_by' => auth()->user()->id,
                        'status' => 1,
                        'booking_id' => @$booking->id,
                        'hotel_id' => @$booking->hotel_id,
                        'customer_id' => @$booking->booking_source ? $booking->booking_source : @$booking->customer_id,
                    ]);
                }

                NonInvoicePayment::create([
                    'slug' => uniqid(),
                    'client_id' => $booking->booking_source,
                    'amount' => abs($refund),
                    'type' => 0,
                    'transaction_id' => null,
                    'date' => Carbon::now(),
                    'note' => "[$booking->booking_number] Booking cancellation charge pending",
                    'status' => 1,
                    'created_by' => auth()->user()->id,
                    'hotel_id' => @$booking->hotel_id,
                ]);
            }
        }

        if ($cancellationCharge > 0 && $advance >= 1 && empty($booking->booking_source)) {
            $revenueAccount = LedgerAccount::where('code', 'CHARGES')->first();
            $revenueAccount = $revenueAccount->getAccoutnbalance;
            if ($revenueAccount) {
                $acc = $revenueAccount->balanceTransactions()->create([
                    'amount' => abs($cancellationCharge),
                    'reason' => "[$booking->booking_number] Booking cancellation charge",
                    'type' => 1,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'booking_id' => @$booking->id,
                    'hotel_id' => @$booking->hotel_id,
                    'customer_id' => @$booking->customer_id,
                    'plutus_entries_id' => $plutusId
                ]);
            }

            NonInvoicePayment::create([
                'slug' => uniqid(),
                'client_id' => @$booking->customer_id,
                'amount' => abs($cancellationCharge),
                'type' => 0,
                'transaction_id' => null,
                'date' => Carbon::now(),
                'note' => "[$booking->booking_number] Booking cancellation charge pending",
                'status' => 1,
                'created_by' => auth()->user()->id,
                'hotel_id' => @$booking->hotel_id,
            ]);

            NonInvoicePayment::create([
                'slug' => uniqid(),
                'client_id' => @$booking->customer_id,
                'amount' => abs($cancellationCharge),
                'type' => 1,
                'transaction_id' => $acc->id,
                'date' => Carbon::now(),
                'note' => "[$booking->booking_number] Booking cancellation charge received",
                'status' => 1,
                'created_by' => auth()->user()->id,
                'hotel_id' => @$booking->hotel_id,
            ]);
        }
    }

    public function manageRoundOffLedger($booking, $hotelId, $amount, $reason, $isRevenue = true, $credit = true)
    {
        $revenueAccount = LedgerAccount::where('code', $isRevenue ? 'ROUND-OFF-REVENUE' : 'ROUND-OFF-EXPENSE')->first();
        $revenueAccount = $revenueAccount->getAccoutnbalance;
        if ($revenueAccount) {
            $revenueAccount->balanceTransactions()->create([
                'amount' => round($amount, 2),
                'reason' => $reason,
                'type' => $credit ? 1 : 0,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'booking_id' => @$booking->id,
                'hotel_id' => @$hotelId,
            ]);
        }
    }

    /*Manage Commission Ledger At time of booking (ABHI-7-12)*/

    public function manageCommissionLedger($booking, $hotelId, $amount, $reason, $customer_id, $plutusId,$advanceAmount)
    {
        
        $commissionAccount = LedgerAccount::where('code', 'commission-payable')->first();
        $commissionAccount = $commissionAccount->getAccoutnbalance;
        $commission = [];
        if($commissionAccount) {
            $commission[] = $commissionAccount->balanceTransactions()->create([
                'amount' => round($amount, 2),
                'reason' => $reason,
                'type' => 1,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'booking_id' => @$booking->id,
                'hotel_id' => @$hotelId,
                'customer_id' => $customer_id,
                'plutus_entries_id' => $plutusId
            ]);
        }

        if($advanceAmount <= 0){
            $accountReceivable = LedgerAccount::where('code', 'ACCOUNT-RECEIVABLE')->first();
            $accountReceivable = $accountReceivable->getAccoutnbalance;
            if($accountReceivable){
                $commission[] = $accountReceivable->balanceTransactions()->create([
                    'amount' => round($amount, 2),
                    'reason' => $reason,
                    'type' => 1,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'booking_id' => @$booking->id,
                    'hotel_id' => @$hotelId,
                    'customer_id' => $customer_id,
                    'plutus_entries_id' => $plutusId
                ]);
            }
            
        }
        return $commission;
   }

   /*Manage Bank Charges Ledger At time of booking*/

   public function manageBankChargesLedger($booking,$hotelId,$amount,$reason,$customer_id,$plutusId){
        $bankChargesAccount = LedgerAccount::where('code','bank-charges')->first();
        $bankChargesAccount = $bankChargesAccount->getAccoutnbalance;
        $charges = [];
        if($bankChargesAccount){
            $charges[] = $bankChargesAccount->balanceTransactions()->create([
                'amount' => round($amount, 2),
                'reason' => $reason,
                'type' => 1,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'booking_id' => @$booking->id,
                'hotel_id' => @$hotelId,
                'customer_id' => $customer_id,
                'plutus_entries_id' => $plutusId
            ]);
        }
        return $charges;
   }

   /*Manage Advance Payment */

   public function advancePayment(Request $request){

    DB::beginTransaction();
    try {

        $getBookingData = Booking::where('id', $request->booking_id)->first();
        $totalPaidAmount = $request->paidAmount + $request->bankCharges;
        $customerId = ($getBookingData->booking_source != NULL) ? $getBookingData->booking_source : $getBookingData->customer_id;
        $roundOff = 0;
        $advanceAmount = $request->paidAmount + $getBookingData->advance_amount;
            if (!$getBookingData) {
                return $this->responseWithError('Booking Not Found');
            }

            /*Manage Plutus Entry*/
            $note = ($request->note !== '') ? "[".$getBookingData->booking_number."]" .$request->note : "[".$getBookingData->booking_number."] booking advance payment";
            $plutusId = $this->createPlutusEntry($getBookingData->hotel_id,$note,now(),$totalPaidAmount,$getBookingData,'advance');

            /*Manage Bank Ledger Account*/

            if($request->account['bankName'] != "Cash" && $request->bankCharges > 0){
                $msg = "Bank Charges during advance of $getBookingData->booking_number";
                $bankCharge = $this->manageBankChargesLedger($getBookingData, $getBookingData->hotel_id, $request->bankCharges, $msg,$customerId,$plutusId);
            }
            
            /* Manage Credit Debit Ledger */
            $accountTransaction = $this->creditBalanceInAccount($request->account['id'],
                $advanceAmount, $getBookingData, null, $getBookingData->hotel_id, null, $roundOff,$customerId,$plutusId,$request->bankCharges,$request->paymentDate,0);


            /*Update Booking Amount*/

            $getBookingData->advance_amount = $advanceAmount + $request->bankCharges;
            $getBookingData->paid_amount = $request->pendingAmount;
            $getBookingData->payment_method = $request->account['id'];
            $getBookingData->save();

        DB::commit();
        return $this->responseWithSuccess('Advance payment added successfully!');
    } catch (Exception $e) {
        DB::rollBack();
        return $this->responseWithError($e->getMessage());
    }
   }

   /*Manage Amend Stay*/

   public function amendStay(Request $request){
    // dd($request->input());
    DB::beginTransaction();
    try {
        $getBookingData = Booking::where('id', $request->booking_id)->first();
        $getBookingDetail = BookingDetails::where('booking_id', $request->booking_id)->get();
        
        $currentDateTime = date('Y-m-d H:i:s');

        /*Update Booking*/

            $getBookingData->check_out_date = $request->check_out_date . ' ' . date('H:i:s', strtotime($currentDateTime));
            $getBookingData->total_price = $request->netTotal;
            $getBookingData->paid_amount = $request->netTotal - $getBookingData->advance_amount - $getBookingData->discount_amount;
            $getBookingData->final_gst_rates = $request->final_gst_rates;
            $getBookingData->tax_included = (@$request->tax_included) ? 1 : 0;
            $getBookingData->save();

        /*Update Booking Detail*/

        foreach ($getBookingDetail as $k => $bookingDetail) {
            $bookingDetail->extra_facility_days = $request->noOfDays;
            $bookingDetail->room_rate = $request->individualroomRate[$k];
            $bookingDetail->modified_room_rate = $request->individualmodifiedRate[$k];
            $bookingDetail->room_tax = $request->individualroomTax[$k];
            $bookingDetail->extra_person_tax = @$request->individualroomExtraPersonTax[$k] ? $request->individualroomExtraPersonTax[$k] : 0;
            $bookingDetail->extra_person_price = @$request->individualroomExtraPersonPrice[$k] ? $request->individualroomExtraPersonPrice[$k] : 0;
            $bookingDetail->extra_bed_tax = @$request->individualroomExtraBedTax[$k] ? $request->individualroomExtraBedTax[$k] : 0;
            $bookingDetail->extra_bed_price = @$request->individualroomExtraBedPrice[$k] ? $request->individualroomExtraBedPrice[$k] : 0;
            $bookingDetail->meal_plan_tax = @$request->individualroomMealTax[$k] ? $request->individualroomMealTax[$k] : 0;
            $bookingDetail->save();
        }

        /*Insert Into Amend Log*/

        BookingAmendStay::create([
            'hotel_id' => $getBookingData->hotel_id,
            'booking_id' => $getBookingData->id,
            'notes' => 'Checkout Date Extend',
            'modified_net_total' => $request->netTotal
        ]);

        DB::commit();
        return $this->responseWithSuccess('Checkout Date Extended successfully!');
    } catch (Exception $e) {
        DB::rollBack();
        return $this->responseWithError($e->getMessage());
    }
   }

   /*Manage Additional Services*/

   public function additionalService(Request $request){
        
        DB::beginTransaction();
        try {
           
            $getBookingData = Booking::where('id', $request->booking_id['booking_id'])->first();
            $getBookingDetail = BookingDetails::where('id', $request->booking_id['id'])->first();
            $totalMealPrice = 0;
            $totalExtraBed = 0;
            $totalExtraPerson = 0;
            $totalPrice = 0;
            if(!empty($request->booking_id['complementrays'])){
                $updatedBooking = BookingDetails::with('room','mealPlanDetails')->where('booking_id', $request->booking_id['booking_id'])->get();
            
            
            $totalRate   = 0;
            $totalGST    = 0;
            $totalBed    = 0;
            $totalPerson = 0;
            $totalMeal   = 0;
            
            foreach($updatedBooking as $upBooking){
                $totalRate += $upBooking->room_rate;
                $totalGST += $upBooking->room_tax;
                
                if($upBooking->extra_bed_tax != 0){
                    // $totalBed += $upBooking->extra_bed_tax + ($upBooking->room->extra_bed_rate * $upBooking->extra_facility_days);
                    $totalBed += $upBooking->extra_bed_tax + $upBooking->extra_bed_price;
                }
                if($upBooking->extra_person_tax != 0){
                    // $totalPerson += $upBooking->extra_person_tax + ($upBooking->room->per_person * $upBooking->extra_facility_days);
                    $totalPerson += $upBooking->extra_person_tax + $upBooking->extra_person_price;
                }                
                if($upBooking->meal_plan_tax != 0){
                    $totalMeal += $upBooking->meal_plan_tax + ($upBooking->mealPlanDetails->price * $upBooking->meal_plan_persons * $upBooking->extra_facility_days);
                }

            }

                $totalPrice = $totalRate + $totalGST + $totalBed + $totalPerson + $totalMeal + $request->totalGST + ($request->netTotal -  $request->totalGST);
                $paidAmount = $totalPrice - $getBookingData->advance_amount - $getBookingData->discount_amount; 
            } else {
                $totalPrice = $getBookingData->total_price + $request->totalGST + ($request->netTotal - $request->totalGST);
                $paidAmount = $totalPrice - $getBookingData->advance_amount - $getBookingData->discount_amount;
            }
         
            // echo $totalPrice.'--'.$paidAmount; exit();
            /*Update In Booking Complementrays Delete First and then Insert*/

            if(!empty($request->selectedService)){
                BookingComplementray::where('booking_Detail_id',$request->booking_id['id'])->delete();
                foreach ($request->selectedService as $complementary) {
                    BookingComplementray::create([
                        'complementary_id' => $complementary['room_facility_id'],
                        'booking_Detail_id' => $request->booking_id['id'],
                        'quantity' => $complementary['quantity'],
                        'modified_rate' => $complementary['price']
                    ]);    
                }
            }

            /*Update In Booking Detail*/
            $getBookingDetail->facility_tax = $request->totalGST;
            $getBookingDetail->save();

            /*Update In Booking*/
            $getBookingData->total_price = $totalPrice;
            $getBookingData->paid_amount = $paidAmount;
            $getBookingData->final_gst_rates = $request->final_gst_rates;
            $getBookingData->save();
            
            DB::commit();
            return $this->responseWithSuccess('Additional Service added successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseWithError($e->getMessage());
        }
   }

   /*Manage Room Move*/

   public function roomMove(Request $request){
       
        DB::beginTransaction();
        try {
           
            $getBookingData = Booking::where('id', $request->booking_id['booking_id'])->first();
            $getBookingDetail = BookingDetails::where('id', $request->booking_id['id'])->first();
          
            /*Update In Booking Detail*/

                $getBookingDetail->room_tax = $request->totalRoomGST;
                $getBookingDetail->room_rate = $request->price * $getBookingDetail->extra_facility_days;
                $getBookingDetail->room_no = $request->room_id['id'];
                $getBookingDetail->room_id = $request->room_id['id'];
                $getBookingDetail->modified_room_rate = $request->price;
                $getBookingDetail->save();

            /*Update In Booking*/
            $updatedBooking = BookingDetails::with('room','mealPlanDetails')->where('booking_id', $request->booking_id['booking_id'])->get();
            
            $totalAdditionalService = 0;
            $totalRate   = 0;
            $totalGST    = 0;
            $netTotal    = 0; 
            $totalBed    = 0;
            $totalPerson = 0;
            $totalMeal   = 0;
            
            foreach($updatedBooking as $upBooking){
                $totalRate += $upBooking->room_rate;
                $totalGST += $upBooking->room_tax;
                
                if($upBooking->extra_bed_tax != 0){
                    // $totalBed += $upBooking->extra_bed_tax + ($upBooking->room->extra_bed_rate * $upBooking->extra_facility_days);
                    $totalBed += $upBooking->extra_bed_tax + $upBooking->extra_bed_price;
                }
                if($upBooking->extra_person_tax != 0){
                    // $totalPerson += $upBooking->extra_person_tax + ($upBooking->room->per_person * $upBooking->extra_facility_days);
                    $totalPerson += $upBooking->extra_person_tax + $upBooking->extra_person_price;
                }                
                if($upBooking->meal_plan_tax != 0){
                    $totalMeal += $upBooking->meal_plan_tax + ($upBooking->mealPlanDetails->price * $upBooking->meal_plan_persons * $upBooking->extra_facility_days);
                }

                if($upBooking->facility_tax != 0 && !empty($upBooking->complementrays)){

                    foreach($upBooking->complementrays as $com){
                        $totalAdditionalService += $com->modified_rate * $com->quantity;
                    }

                    $totalAdditionalService += $upBooking->facility_tax;
                }

            }

            $netTotal = $totalRate + $totalGST + $totalBed + $totalPerson + $totalMeal + $totalAdditionalService;

            $getBookingData->total_price = $netTotal;
            $getBookingData->paid_amount = $netTotal - $getBookingData->advance_amount - $getBookingData->discount_amount;
            $getBookingData->final_gst_rates = $request->final_gst_rates;
            $getBookingData->save();


            /*Insert Into Amend Log*/

            BookingAmendStay::create([
                'hotel_id' => $getBookingData->hotel_id,
                'booking_id' => $getBookingData->id,
                'notes' => 'Room Move',
                'modified_net_total' => $netTotal
            ]);
            DB::commit();
            return $this->responseWithSuccess('Room Move successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseWithError($e->getMessage());
        }    
        
   }

}