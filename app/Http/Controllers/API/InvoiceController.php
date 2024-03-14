<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceListResource;
use App\Http\Resources\InvoiceResource;
use App\Models\AccountTransaction;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceProduct;
use App\Models\OtherServices;
use App\Models\InvoiceService;
use App\Models\InvoiceServiceTax;
use App\Models\Product;
use App\Notifications\InvoiceNotification;
use App\Notifications\InvoicePaymentNotification;
use App\Rules\MinTotal;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Modules\Accounts\Entities\{
    PlutusEntries,
    LedgerAccount
};
use Modules\Restaurant\Entities\RestroItem;
use Modules\Restaurant\Entities\Restroorder;

class InvoiceController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:invoice-list', ['only' => ['index', 'search']]);
        $this->middleware('can:invoice-create', ['only' => ['create']]);
        $this->middleware('can:invoice-view', ['only' => ['show']]);
        $this->middleware('can:invoice-edit', ['only' => ['update']]);
        $this->middleware('can:invoice-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $invoices = Invoice::with('client', 'invoiceTax', 'invoicePayments')
            ->when($search, function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%$search%");
                $q->whereHas('client', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            })
            ->latest()->paginate($request->perPage);

        return InvoiceListResource::collection($invoices);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // dd($request->rows);

        // validate request
        $this->validate($request, [
            'client' => 'required',
            'reference' => 'nullable|string|max:255',
            // 'selectedProducts' => 'required|array|min:1',
            // 'selectedProducts.*' => 'required|distinct',
            'discount' => $request->discountType == true ? 'nullable|numeric|min:1|max:100' : 'nullable|numeric|min:1|max:' . $request->subTotal,
            // 'transportCost' => 'nullable|numeric|min:1',
            'orderTax' => $request->rows[0]['service_name'] !== null && $request->rows[0]['price'] !== 0 ? 'required' : 'nullable',
            'netTotal' => 'required|numeric|min:1',
            'poReference' => 'nullable|string|max:255',
            'paymentTerms' => 'nullable|string|max:255',
            'deliveryPlace' => 'nullable|string|max:255',
            'account' => $request->addPayment == true ? 'required' : 'nullable',
            'paidAmount' => $request->addPayment == true ? 'required|min:1|max:' . $request->netTotal : 'nullable',
            'chequeNo' => 'nullable|string|max:255',
            'receiptNo' => 'nullable|string|max:255',
            'date' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
            'hotel_id' => 'nullable'
        ]);

        $tax_order = [];
        $tax_order_all = [];
        if (!empty($request->orderTax) && count($request->orderTax) > 0) {
            foreach ($request->orderTax as $tax_) {
                $tax_order['id'] = $tax_['id'];
                $tax_order['name'] = $tax_['name'];
                $tax_order['rate'] = $tax_['rate'];
                $tax_order['amount'] = ($tax_['rate'] / 100) * $request->subTotal;
                $tax_order_all[] = $tax_order;
            }
        } else if (!empty($request->orderTax)) {
            $orderTax = @$request->orderTax ? @$request->orderTax['id'] : null;
        }


        try {
            // generate code
            $isRestaurantOrder = @$request->isRestaurantOrder ?? false;
            $number = 1;
            $lastInvoice = Invoice::latest()->first();
            if ($lastInvoice) {
                $number = $lastInvoice->id + 1;
            }

            $hotel = str_replace('hotel', '', $request->hotel_id['hotel_name']);
            $hotel = str_replace('Hotel', '', $hotel);
            $hotel = ($request->hotel_id['hotel_prefix'] !== null) ? $request->hotel_id['hotel_prefix'] : Str::slug($hotel);
            $code = "$hotel-$number";

            // get logged in user id
            $userId = auth()->user()->id;

            // calculate is paid
            $isPaid = 0;
            if ($request->netTotal == $request->paidAmount) {
                $isPaid = 1;
            }

            // calculate discount
            $discount = $isRestaurantOrder ? @$request->discountAmount : @$request->discount;
            if ($request->discountType == 1 && !$isRestaurantOrder) {
                $discount = $request->totalDiscount;
            }

            $tax_order = [];
            $tax_order_all = [];
            if (!empty($request->orderTax) && count($request->orderTax) > 0) {
                foreach ($request->orderTax as $tax_) {
                    $tax_order['id'] = $tax_['id'];
                    $tax_order['name'] = $tax_['name'];
                    $tax_order['rate'] = $tax_['rate'];
                    $tax_order['amount'] = ($tax_['rate'] / 100) * $request->subTotal;
                    $tax_order['code'] = $tax_['code'];
                    $tax_order_all[] = $tax_order;
                }
            } else if (!empty($request->orderTax)) {
                $orderTax = @$request->orderTax ? @$request->orderTax['id'] : null;
            }

            // create invoice
            $invoice = Invoice::create([
                'invoice_no' => $isRestaurantOrder ? "hotel-$code" : $code,
                'reference' => $request->reference,
                'slug' => uniqid(),
                'client_id' => $request->client['id'],
                'transport' => @$request->transportCost ?? 0,
                'discount_type' => @$request->discountType ?? 0,
                'discount' => $discount,
                'sub_total' => (@$request->productTotalTax) ? $request->subTotal - $request->totalTax : $request->subTotal,
                'po_reference' => $request->poReference,
                'payment_terms' => $request->paymentTerms,
                'delivery_place' => $request->deliveryPlace,
                'tax_id' => @$orderTax ? @$orderTax : null,
                'tax_value' => @$request->tax ? $request->tax : $request->totalTax,
                // 'tax_value' => $request->totalTax,
                'invoice_date' => $request->date ?? Carbon::now(),
                'note' => clean($request->note),
                'status' => $request->status,
                'is_paid' => $isPaid,
                'created_by' => $userId,
                'hotel_id' => $isRestaurantOrder ? @$request->hotel_id : @$request->hotel_id['id']
            ]);

            if (!empty($tax_order_all)) {
                foreach ($tax_order_all as $key => $taxer) {
                    InvoiceServiceTax::create([
                        'invoice_id' => $invoice->id,
                        'tax_id' => $taxer['id'],
                        'name' => $taxer['name'],
                        'rate' => $taxer['rate'],
                        'amount' => $taxer['amount'],
                        'code' => $taxer['code'],
                    ]);
                }
            }

            // store invoice products
            if (!$isRestaurantOrder) {
                if (!empty($request->selectedProducts)) {

                    foreach ($request->selectedProducts as $key => $selectedProduct) {
                        $product = Product::where('slug', $selectedProduct['slug'])->first();
                        // update product stock
                        $product->update([
                            'inventory_count' => $product->inventory_count - $selectedProduct['qty'],
                        ]);

                        InvoiceProduct::create([
                            'invoice_id' => $invoice->id,
                            'product_id' => $selectedProduct['id'],
                            'quantity' => $selectedProduct['qty'],
                            'purchase_price' => $selectedProduct['avgPurchasePrice'],
                            'sale_price' => $selectedProduct['unitPrice'],
                            'unit_cost' => $selectedProduct['unitCost'],
                            'tax_amount' => $selectedProduct['totalTax'],
                        ]);
                    }
                } else if (!empty($request->rows)) {
                    foreach ($request->rows as $key => $service_name) {
                        $serviceexist = OtherServices::where('name', 'LIKE', '%' . $service_name['service_name'] . '%')->first();
                        if (!empty($serviceexist)) {
                            $service_id = $serviceexist->id;
                        } else {
                            $createservice = OtherServices::create([
                                'name' => $service_name['service_name']
                            ]);
                            $service_id = $createservice->id;
                        }
                        InvoiceService::create([
                            'invoice_id' => $invoice->id,
                            'service_id' => $service_id,
                            'service_name' => $service_name['service_name'],
                            'quantity' => $service_name['quantity'],
                            'price' => $service_name['price'],
                            'myTax' => $service_name['myTax'],
                        ]);
                    }
                }
            } else {
                $this->createRestOrder($request->all(), $invoice);
            }

            // store transaction

            /*Create Plutus Entry*/
            $reason = '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment created for [' . $request->client['name'] . ']';
            $plutusId = $this->createPlutusEntry($request->hotel_id['id'], $reason, now(), $request->totalTax + $request->subTotal);

            if ($request->addPayment == true) {
                $reason = '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment added to [' . $request->account['accountNumber'] . ']';

                // create transaction
                $transaction = AccountTransaction::create([
                    'account_id' => $request->account['id'],
                    'amount' => $request->paidAmount,
                    'reason' => $reason,
                    'type' => 1,
                    'transaction_date' => $request->date,
                    'cheque_no' => $request->chequeNo,
                    'receipt_no' => $request->receiptNo,
                    'created_by' => $userId,
                    'status' => $request->status,
                    'hotel_id' => $isRestaurantOrder ? @$request->hotel_id : @$request->hotel_id['id'],
                    'invoice_id' => $invoice->id,
                    'customer_id' => $request->client['id'],
                    'plutus_entries_id' => $plutusId,
                ]);

                // store invoice payment record
                InvoicePayment::create([
                    'slug' => uniqid(),
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $request->paidAmount,
                    'date' => $request->date,
                    'note' => clean($request->note),
                    'created_by' => $userId,
                    'status' => $request->status,
                ]);

            }
            // Add revenue ledger
            $this->manageRevenueLedger($invoice, $isRestaurantOrder ? @$request->hotel_id : @$request->hotel_id['id'], $isRestaurantOrder, $plutusId);

            //manage receivable
            if (!$isPaid) {
                $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-RECEIVABLE')->first();
                $advanceAccount = $advanceAccount->getAccoutnbalance;
                if ($advanceAccount) {
                    $advanceAccount->balanceTransactions()->create([
                        'amount' => floatval($request->netTotal ?? 0) - floatval($request->paidAmount ?? 0),
                        'reason' => '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment pending',
                        'type' => 1,
                        'transaction_date' => ($request->date) ? $request->date : now(),
                        'cheque_no' => null,
                        'receipt_no' => null,
                        'created_by' => $userId,
                        'status' => 1,
                        'hotel_id' => $isRestaurantOrder ? @$request->hotel_id : @$request->hotel_id['id'],
                        'invoice_id' => $invoice->id,
                        'customer_id' => $request->client['id'],
                        'plutus_entries_id' => $plutusId
                    ]);
                }
            }

            //send notification
            if ($request->isSendEmail || $request->isSendSMS) {
                $this->notifyCustomer($invoice->slug, $request);
            }

            return $this->responseWithSuccess('Invoice added successfully', [
                'invoice_id' => $invoice->id,
                'invoice_slug' => $invoice->slug,
                'slug' => $invoice->slug,
            ]);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function manageRevenueLedger($invoice, $hotelId, $isRestOrder = false, $plutusId)
    {
        //Manage GST ledger
        $outputGst = LedgerAccount::where('code', 'GST-OUTPUT')->first();
        if ($outputGst) {
            $totalTax = ($isRestOrder || $invoice->tax_value != NULL) ? $invoice->tax_value : $invoice->calculated_service_tax;
            if ($totalTax > 0) {
                $outputGst = $outputGst->getAccoutnbalance;
                $outputGst && $outputGst->balanceTransactions()->create([
                    'amount' => ($isRestOrder || $invoice->tax_value != NULL) ? $invoice->tax_value : $invoice->calculated_service_tax,
                    'reason' => '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment GST',
                    'type' => 1,
                    'transaction_date' => ($invoice->invoice_date) ? $invoice->invoice_date : now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'invoice_id' => @$invoice->id,
                    'reference' => 'Booking',
                    'hotel_id' => $hotelId,
                    'customer_id' => $invoice->client_id,
                    'plutus_entries_id' => $plutusId
                ]);
            }
        }

        //Manage Inventory
        if (!$isRestOrder) {
            // $payableAccount = LedgerAccount::where('code', 'INVENTORY')->first();
            $payableAccount = LedgerAccount::where('code', 'DIRECT-SALES')->first();
            $payableAccount = $payableAccount->getAccoutnbalance;
            $payableAccount->balanceTransactions()->create([
                'amount' => floatval($invoice->sub_total ?? 0) + floatval($invoice->transport ?? 0) - floatval($invoice->discount ?? 0),
                'reason' => '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment pending',
                'type' => 1,
                'transaction_date' => ($invoice->invoice_date) ? $invoice->invoice_date : now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'invoice_id' => $invoice->id,
                'hotel_id' => $hotelId,
                'customer_id' => $invoice->client_id,
                'plutus_entries_id' => $plutusId
            ]);
        } else {
            //            Manage revenue
            $payableAccount = LedgerAccount::where('code', 'RESTAURANT-REVENUE')->first();
            $payableAccount = $payableAccount->getAccoutnbalance;
            $payableAccount->balanceTransactions()->create([
                'amount' => floatval($invoice->sub_total ?? 0) + floatval($invoice->transport ?? 0) - floatval($invoice->discount ?? 0),
                'reason' => '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment received',
                'type' => 1,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'invoice_id' => $invoice->id,
                'hotel_id' => $hotelId,
                'customer_id' => $invoice->client_id,
                'plutus_entries_id' => $plutusId
            ]);
        }

        //
//        Manage revenue
//        $payableAccount = LedgerAccount::where('code', 'OPERATING_REVENUE')->first();
//        $payableAccount = $payableAccount->getAccoutnbalance;
//        $payableAccount->balanceTransactions()->create([
//            'amount' => floatval($invoice->sub_total ?? 0) + floatval($invoice->transport ?? 0) - floatval($invoice->discount ?? 0),
//            'reason' => '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment received',
//            'type' => 1,
//            'transaction_date' => now(),
//            'cheque_no' => null,
//            'receipt_no' => null,
//            'created_by' => auth()->user()->id,
//            'status' => 1,
//            'invoice_id' => $invoice->id,
//            'hotel_id' => $hotelId,
//        ]);
    }

    public function storeInvoicePayments(Request $request)
    {
        
        $this->validate($request, [
            'account' => 'required',
            'paidAmount' => ['required', 'min:1', 'max:' . $request->netTotal],
            'invoice_id' => ['required', 'integer'],
            'chequeNo' => 'nullable|string|max:255',
            'receiptNo' => 'nullable|string|max:255',
            'date' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        $userId = auth()->id();
        // store transaction
        // @$request->selectedInvoice['client']['name']) ? $request->selectedInvoice['client']['name'] : 

        $clientName = (is_array($request->selectedInvoice['client'])) ? $request->selectedInvoice['client']['name'] : $request->selectedInvoice['client'];
        $reason = '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment added to [' . $clientName . ']';

        $plutusId = $this->createPlutusEntry($invoice->hotel_id, $reason, now(), $request->paidAmount);

        try {
            // create transaction
            $transaction = AccountTransaction::create([
                'account_id' => $request->account['id'],
                'amount' => $request->paidAmount,
                'reason' => $reason,
                'type' => 1,
                'transaction_date' => $request->paymentDate,
                'cheque_no' => $request->chequeNo,
                'receipt_no' => $request->receiptNo,
                'created_by' => $userId,
                'status' => $request->status,
                'invoice_id' => @$invoice->id,
                'hotel_id' => $invoice->hotel_id,
                'customer_id' => $invoice->client_id,
                'plutus_entries_id' => $plutusId
            ]);

            // store invoice payment record
            InvoicePayment::create([
                'slug' => uniqid(),
                'invoice_id' => $invoice->id,
                'transaction_id' => $transaction->id,
                'amount' => $request->paidAmount,
                'date' => $request->paymentDate,
                'note' => clean($request->note),
                'created_by' => $userId,
                'status' => $request->status,
            ]);

            $this->manageLedger($invoice, $request->paidAmount, $plutusId,$request);

            $isPaid = 0;
            if ($request->netTotal == $request->paidAmount) {
                $isPaid = 1;
            }

            // update invoice data
            $invoice->update([
                'reference' => $request->reference,
                'po_reference' => $request->poReference,
                'payment_terms' => $request->paymentTerms,
                'delivery_place' => $request->deliveryPlace,
                'invoice_date' => $request->date,
                'is_paid' => $isPaid,
                'note' => clean($request->note),
            ]);

            if ($request->isSendEmail || $request->isSendEmail) {
                $invoice['amount_paid'] = $request->paidAmount;
                $invoice->client->notify(new InvoicePaymentNotification($invoice, [
                    'isSendEmail' => filter_var($request->isSendEmail, FILTER_VALIDATE_BOOLEAN),
                    'isSendSMS' => filter_var($request->isSendSMS, FILTER_VALIDATE_BOOLEAN)
                ]));
            }

            return $this->responseWithSuccess('Invoice payment added successfully!', [
                'invoice_id' => $invoice->id,
            ]);

        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage() . '--' . $e->getLine());
        }
    }

    public function manageLedger($invoice, $amount, $plutusId,$request)
    {
        //manage receivable
        $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-RECEIVABLE')->first();
        $advanceAccount = $advanceAccount->getAccoutnbalance;
        if ($advanceAccount) {
            $advanceAccount->balanceTransactions()->create([
                'amount' => $amount,
                'reason' => '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment added to account]',
                'type' => 0,
                'transaction_date' => ($request->paymentDate) ? $request->paymentDate : now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'invoice_id' => @$invoice->id,
                'hotel_id' => $invoice->hotel_id,
                'customer_id' => $invoice->client_id,
                'plutus_entries_id' => $plutusId
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return InvoiceResource
     */
    // public function show($slug)
    // {
    //     try {
    //         $invoice = Invoice::where('slug', $slug)->with('client','invoiceServiceTax','invoiceService', 'invoiceProducts.invoice', 'invoicePayments.invoicePaymentTransaction.cashbookAccount', 'invoiceProducts.product.productUnit', 'invoiceProducts.product.productTaxRate', 'invoiceTax', 'user')->first();

    //         return new InvoiceResource($invoice);
    //     } catch (Exception $e) {
    //         return $this->responseWithError($e->getMessage());
    //     }
    // }
    public function show($slug)
    {
        try {
            $invoice = Invoice::where('slug', $slug)
                ->with('client', 'invoiceServiceTax', 'invoiceService', 'invoiceProducts.invoice', 'invoicePayments.invoicePaymentTransaction.cashbookAccount', 'invoiceProducts.product.productUnit', 'invoiceProducts.product.productTaxRate', 'invoiceTax', 'user', 'companyDetails')
                ->first();
            return new InvoiceResource($invoice);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $slug)
    {

        $invoice = Invoice::where('slug', $slug)->with('invoiceProducts.product', 'invoiceService', 'invoiceReturn')->first();
        $totalPaid = $invoice->invoiceTotalPaid();
        $minAmount = !isset($invoice->invoiceReturn) ? $totalPaid : $totalPaid - $invoice->invoiceReturn->returnTransaction->amount;

        // validate request
        $this->validate($request, [
            'client' => 'required',
            'reference' => 'nullable|string|max:255',
            // 'selectedProducts' => 'required|array|min:1',
            // 'selectedProducts.*' => 'required|distinct',
            'discount' => $request->discountType == true ? 'nullable|numeric|min:1|max:100' : 'nullable|numeric|min:1|max:' . $request->subTotal,
            // 'transportCost' => 'nullable|numeric|min:1',
            'netTotal' => ['required', 'numeric', new MinTotal($minAmount, $request->netTotal)],
            'poReference' => 'nullable|string|max:255',
            'paymentTerms' => 'nullable|string|max:255',
            'deliveryPlace' => 'nullable|string|max:255',
            'date' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
            'orderTax' => !empty($request->rows) ? 'required' : 'nullable',

        ]);
        try {

            // calculate is paid
            $isPaid = 0;
            if ($request->netTotal == $request->totalPaid) {
                $isPaid = 1;
            }

            // calculate discount
            $discount = $request->discount;
            if ($request->discountType == 1) {
                $discount = $request->totalDiscount;
            }

            $tax_order = [];
            $tax_order_all = [];
            if (!empty($request->orderTax) && count($request->orderTax) > 0) {
                foreach ($request->orderTax as $tax_) {
                    $tax_order['id'] = (@$tax_['tax_id']) ? $tax_['tax_id'] : $tax_['id'];
                    $tax_order['name'] = $tax_['name'];
                    $tax_order['rate'] = $tax_['rate'];
                    $tax_order['amount'] = $tax_['amount']; //($tax_['rate'] / 100) * $request->subTotal;
                    $tax_order['code'] = $tax_['code'];
                    $tax_order_all[] = $tax_order;
                }
            } else if (!empty($request->orderTax)) {
                $orderTax = @$request->orderTax ? @$request->orderTax['id'] : null;
            }


            // update invoice
            $invoice->update([
                'reference' => $request->reference,
                'client_id' => $request->client['id'],
                'transport' => @$request->transportCost ?? 0,
                'discount_type' => $request->discountType,
                'discount' => $discount,
                'sub_total' => (@$request->productTotalTax) ? $request->subTotal - $request->totalTax : $request->subTotal,
                'po_reference' => $request->poReference,
                'payment_terms' => $request->paymentTerms,
                'delivery_place' => $request->deliveryPlace,
                'tax_id' => @$orderTax ? @$orderTax : null,
                'tax_value' => @$request->tax ? $request->tax : $request->totalTax,
                'invoice_date' => $request->date,
                'note' => clean($request->note),
                'status' => $request->status,
                'is_paid' => $isPaid,
                'hotel_id' => @$request->hotel_id['id']
            ]);

            if (!empty($tax_order_all)) {
                foreach ($tax_order_all as $key => $taxer) {

                    InvoiceServiceTax::where('invoice_id', $invoice->id)->where('tax_id', $taxer['id'])->update([
                        'name' => $taxer['name'],
                        'rate' => $taxer['rate'],
                        'amount' => $taxer['amount'],
                        'code' => $taxer['code'],
                    ]);
                }
            }

            $invoice->invoiceProducts->each->delete();
            // store invoice products
            if (!empty($request->selectedProducts)) {
                foreach ($request->selectedProducts as $key => $selectedProduct) {
                    $product = Product::where('slug', $selectedProduct['slug'])->first();
                    $totalQty = $product->inventory_count + $selectedProduct['oldQty'] - $selectedProduct['qty'];
                    // update product stock
                    $product->update([
                        'inventory_count' => $totalQty,
                    ]);

                    InvoiceProduct::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $selectedProduct['id'],
                        'quantity' => $selectedProduct['qty'],
                        'purchase_price' => $selectedProduct['avgPurchasePrice'],
                        'sale_price' => $selectedProduct['unitPrice'],
                        'unit_cost' => $selectedProduct['unitCost'],
                        'tax_amount' => $selectedProduct['totalTax'],
                    ]);
                }
            } else if (!empty($request->rows)) {
                InvoiceService::where('invoice_id', $invoice->id)->delete();
                foreach ($request->rows as $key => $service_name) {
                    $serviceexist = OtherServices::where('name', 'LIKE', '%' . $service_name['service_name'] . '%')->first();
                    if (!empty($serviceexist)) {
                        $service_id = $serviceexist->id;
                    } else {
                        $createservice = OtherServices::create([
                            'name' => $service_name['service_name']
                        ]);
                        $service_id = $createservice->id;
                    }
                    // InvoiceService::where('invoice_id',$invoice->id)->where('service_id',$service_id)->update([  
                    //     'service_name' => $service_name['service_name'],
                    //     'quantity' => $service_name['quantity'],
                    //     'price' => $service_name['price'],
                    //     'myTax' => $service_name['myTax'],
                    // ]);

                    InvoiceService::updateOrCreate(
                        [
                            'invoice_id' => $invoice->id,
                            'service_id' => $service_id,
                        ],
                        [
                            'service_name' => $service_name['service_name'],
                            'quantity' => $service_name['quantity'],
                            'price' => $service_name['price'],
                            'myTax' => $service_name['myTax'],
                        ]
                    );

                }
            }

            $this->updateAccountTransaction($invoice);
            return $this->responseWithSuccess('Invoice updated successfully', [
                'slug' => $invoice->slug,
            ]);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function updateAccountTransaction($invoice)
    {
        $getAccountTransaction = AccountTransaction::where('invoice_id', $invoice->id)->get();

        if (!empty($getAccountTransaction)) {
            foreach ($getAccountTransaction as $transaction) {
                $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-RECEIVABLE')->first();
                $payableAccount = LedgerAccount::where('code', 'DIRECT-SALES')->first();
                $outputGst = LedgerAccount::where('code', 'GST-OUTPUT')->first();

                if ($transaction->account_id == $outputGst->id) {
                    $transaction->amount = ($invoice->tax_value !== NULL) ? $invoice->tax_value : $invoice->calculated_service_tax;
                }
                if ($transaction->account_id == $payableAccount->id) {
                    $transaction->amount = floatval($invoice->sub_total ?? 0) + floatval($invoice->transport ?? 0) - floatval($invoice->discount ?? 0);
                }
                if ($transaction->account_id == $advanceAccount->id) {
                    $taxAmount = ($invoice->tax_value !== NULL) ? $invoice->tax_value : floatval($invoice->calculated_service_tax ?? 0);
                    $transaction->amount = floatval($taxAmount) + floatval($invoice->sub_total ?? 0);
                }

                $transaction->save();
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        try {
            $invoice = Invoice::where('slug', $slug)->with('invoicePayments.invoicePaymentTransaction', 'invoiceProducts.product', 'invoiceReturn')->first();

            // delete return transaction
            $invoiceReturn = $invoice->invoiceReturn;
            if (isset($invoiceReturn)) {
                if ($invoiceReturn->transaction_id != null) {
                    $invoiceReturn->returnTransaction->delete();
                }
                // update product inventory count
                foreach ($invoiceReturn->invoiceReturnProducts as $invoiceReturnProduct) {
                    $product = $invoiceReturnProduct->product;
                    $product->update([
                        'inventory_count' => $product->inventory_count - $invoiceReturnProduct->quantity,
                    ]);
                }
                // delete return products
                $invoiceReturn->invoiceReturnProducts->each->delete();
            }

            $invoice->delete();
            return $this->responseWithSuccess('Invoice deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->term;
        $query = Invoice::with('client', 'invoicePayments', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('invoice_date', [$request->startDate, $request->endDate]);
        }

        $query = $query->where(function ($query) use ($term) {
            $query->where('invoice_no', 'LIKE', '%' . $term . '%')
                ->where('reference', 'LIKE', '%' . $term . '%')
                ->orWhere('sub_total', 'LIKE', '%' . $term . '%')
                ->orWhere('po_reference', 'LIKE', '%' . $term . '%')
                ->orWhere('payment_terms', 'LIKE', '%' . $term . '%')
                ->orWhere('delivery_place', 'LIKE', '%' . $term . '%')
                ->orWhereHas('client', function ($newQuery) use ($term) {
                    $newQuery->where('name', 'LIKE', '%' . $term . '%')
                        ->orWhere('client_id', 'LIKE', '%' . $term . '%');
                });
        });

        return InvoiceListResource::collection($query->paginate($request->perPage));
    }

    // notify customer
    public function notifyCustomer($slug, Request $request)
    {
        $invoice = Invoice::where('slug', $slug)->with('client', 'invoiceProducts.invoice', 'invoicePayments.invoicePaymentTransaction.cashbookAccount', 'invoiceProducts.product.productUnit', 'invoiceProducts.product.productTax', 'invoiceTax', 'user')->first();
        // send notification
        $invoice->client->notify(new InvoiceNotification($invoice, [
            'isSendEmail' => filter_var($request->isSendEmail, FILTER_VALIDATE_BOOLEAN),
            'isSendSMS' => filter_var($request->isSendSMS, FILTER_VALIDATE_BOOLEAN)
        ]));
        return 'Successfully Notified';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allDueInvoices()
    {
        $dueInvoices = Invoice::where('status', 1)->where('is_paid', 0)->latest()->get();
        return InvoiceResource::collection($dueInvoices);
    }

    public function createRestOrder($data, $invoice)
    {
        $hotelId = $data['hotel_id'];
        $prevOrder = Restroorder::where('hotel_id', $hotelId)->latest()->first();
        if (!empty($prevOrder)) {
            $previousBookingId = "RO-0000" . $prevOrder->id;
        } else {
            $previousBookingId = "RO-00000";
        }

        $order = Restroorder::create([
            'order_id_uniq' => $this->generateBookingId($previousBookingId),
            'order_date' => Carbon::now(),
            'order_status' => 0,
            'total_amount' => @$data['netTotal'] ?? 0,
            'discount' => @$data['discountAmount'] ?? 0,
            'tax' => @$data['orderTax'] ?? @$data['tax'] ?? 0,
            'invoice_id' => @$invoice->id,
            'hotel_id' => $hotelId,
        ]);

        $orderItems = $data['selectedProducts'];
        if ($orderItems && !empty($orderItems)) {
            foreach ($orderItems as $item) {
                $optionalItems = @$item['addon'] ? Arr::pluck($item['addon'], 'id') : [];
                RestroItem::create([
                    'order_id' => $order->id,
                    'restaurant_item_id' => $item['variant']['id'],
                    'optional_item_ids' => $optionalItems,
                    'qty' => $item['quantity'],
                ]);
            }
        }
    }
    protected function generateBookingId($previousBookingId)
    {
        // Extract the numeric part of the previous booking ID
        $previousNumber = substr($previousBookingId, 3);
        $nextNumber = intval($previousNumber) + 1;

        // Pad the numeric part with leading zeros
        $nextNumberPadded = str_pad($nextNumber, strlen($previousNumber), '0', STR_PAD_LEFT);

        // Concatenate the prefix and the padded numeric part to form the new booking ID
        $prefix = substr($previousBookingId, 0, 3);

        return $prefix . 'Hotel-' . $nextNumberPadded;
    }

    /*Manage Plutus Entry*/
    protected function createPlutusEntry($hotelId, $note, $date, $amount)
    {
        $createPlutus = PlutusEntries::create([
            'hotel_id' => $hotelId,
            'note' => $note,
            'date' => $date,
            'amount' => $amount,
        ]);

        return $createPlutus->id;
    }
}
