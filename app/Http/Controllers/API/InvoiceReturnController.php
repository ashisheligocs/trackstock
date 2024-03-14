<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceReturnListResource;
use App\Http\Resources\InvoiceReturnResource;
use App\Models\AccountTransaction;
use App\Models\InvoiceReturn;
use App\Models\InvoiceReturnProduct;
use App\Models\Product;
use App\Rules\MinOne;
use Exception;
use Illuminate\Http\Request;
use Modules\Accounts\Entities\{
    PlutusEntries,
    LedgerAccount
};

class InvoiceReturnController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:invoice-return-list', ['only' => ['index', 'search']]);
        $this->middleware('can:invoice-return-create', ['only' => ['create']]);
        $this->middleware('can:invoice-return-view', ['only' => ['show']]);
        $this->middleware('can:invoice-return-edit', ['only' => ['update']]);
        $this->middleware('can:invoice-return-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $invoiceReturn = InvoiceReturn::with('invoice.client', 'user')->whereHas('invoice')->latest()->paginate($request->perPage);
        
        return InvoiceReturnListResource::collection($invoiceReturn);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // validate request
        $this->validate($request, [
            'returnReason' => 'required|string|min:2|max:255',
            'client' => 'required',
            'invoice' => 'required',
            'selectedProducts' => !empty($request->selectedProducts) ? ['required', 'distinct', new MinOne] : 'nullable',
            'selectedServices' => !empty($request->selectedServices) ? ['required', 'distinct', new MinOne] : 'nullable',
            'account' => $request->returnAmount > 0 ? 'required' : 'nullable',
            'availableBalance' => $request->returnAmount > 0 ? 'required|numeric|min:'.$request->returnAmount : 'nullable',
            'totalReturn' => 'required|numeric|min:1',
            'date' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
        ]);
    
        try {
            // generate code
            $code = 1;
            $lastReturn = InvoiceReturn::latest()->first();
            if ($lastReturn) {
                $code = $lastReturn->return_no + 1;
            }

            // get logged in user id
            $userId = auth()->user()->id;
    
            // store retrun amount
            $isPaid = 0;
            $transactionID = null;
         
            if ($request->returnAmount > 0) {
                $reason = '['.config('config.invoiceReturnPrefix').'-'.$code.'] Invoice Return payable sent from ['.$request->account['accountNumber'].']';
                // create transaction
                $transaction = AccountTransaction::create([
                    'account_id' => $request->account['id'],
                    'amount' => $request->returnAmount,
                    'reason' => $reason,
                    'type' => 0,
                    'transaction_date' => $request->date,
                    'cheque_no' => $request->chequeNo,
                    'receipt_no' => $request->receiptNo,
                    'created_by' => $userId,
                    'status' => $request->status,
                ]);
                $isPaid = 1;
                $transactionID = $transaction->id;
            }
         
            // store invoice return
            $invoiceReturn = InvoiceReturn::create([
                'reason' => $request->returnReason,
                'return_no' => $code,
                'invoice_id' => $request->invoice['id'],
                'total_return' => $request->totalReturn,
                'total_return_tax' => $request->invoiceTax - $request->newTax,
                'date' => $request->date,
                'note' => clean($request->note),
                'transaction_id' => $transactionID,
                'created_by' => $userId,
                'status' => $request->status,
            ]);

            // update invoice
            $invoiceReturn->invoice->update([
                'discount' => $request->invoiceDiscount,
                'is_paid' => $isPaid,
                'tax_value' => $request->newTax,
            ]);
            $type = 0;
            if(!empty($request->selectedProducts)){
                $type = 'product';
                // store invoice products
                foreach ($request->selectedProducts as $key => $selectedProduct) {
                    // update product inventory
                    $product = Product::where('slug', $selectedProduct['slug'])->first();
                    $product->update([
                        'inventory_count' => $product->inventory_count + $selectedProduct['returnQty'],
                    ]);

                    // store return product
                    if ($selectedProduct['returnQty'] > 0) {
                        InvoiceReturnProduct::create([
                            'return_id' => $invoiceReturn->id,
                            'product_id' => $selectedProduct['id'],
                            'sale_price' => $selectedProduct['unitCost'],
                            'purchase_price' => $selectedProduct['avgPurchasePrice'],
                            'quantity' => $selectedProduct['returnQty'],
                            'service_id' => NULL,
                        ]);
                    }
                }
            }

            if(!empty($request->selectedServices)){
                $type = 'service';
                foreach ($request->selectedServices as $key => $selectedService) {
                    
                    // store return product
                    if ($selectedService['returnQty'] > 0) {
                        InvoiceReturnProduct::create([
                            'return_id' => $invoiceReturn->id,
                            'product_id' => NULL,
                            'sale_price' => $selectedService['price'],
                            'purchase_price' => $selectedService['price'],
                            'quantity' => $selectedService['returnQty'],
                            'service_id' => $selectedService['id'],
                        ]);
                    }
                }
            }

            /*Manage Ledger Account */

            $this->handleLedgerAccounts($code,$request,$type);

            return $this->responseWithSuccess('Invoice return added successfully', [
                'slug' => $invoiceReturn->slug,
            ]);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        try {
            
            $invoiceReturn = InvoiceReturn::where('slug', $slug)->with('invoice.client', 'invoice.invoiceServiceTax', 'invoice.invoiceService',
            'invoiceReturnProducts.invoiceReturn','invoiceReturnProducts.product.productUnit', 
            'invoiceReturnProducts.product.productTaxRate', 'user')->first();

            return new InvoiceReturnResource($invoiceReturn);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $invoiceReturn = InvoiceReturn::where('slug', $slug)->with('invoiceReturnProducts', 'user')->first();
        
        // validate request
        $this->validate($request, [
            'returnReason' => 'required|string|min:2|max:255',
            // 'selectedProducts' => ['required', 'distinct', new MinOne],
            'selectedProducts' => !empty($request->selectedProducts) ? ['required', 'distinct', new MinOne] : 'nullable',
            'selectedServices' => !empty($request->selectedServices) ? ['required', 'distinct', new MinOne] : 'nullable',
            'account' => $request->returnAmount > 0 ? 'required' : 'nullable',
            'availableBalance' => $request->returnAmount > 0 ? 'required|numeric|min:'.$request->returnAmount : 'nullable',
            'totalReturn' => 'required|numeric|min:1',
            'date' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            // store return amount
            $isPaid = 0;
            $transactionID = null;
            if ($request->returnAmount > 0) {
                if (isset($invoiceReturn->returnTransaction)) {
                    // update transaction
                    $transaction = $invoiceReturn->returnTransaction->update([
                        'account_id' => $request->account['id'],
                        'amount' => $request->returnAmount,
                        'transaction_date' => $request->date,
                        'cheque_no' => $request->chequeNo,
                        'receipt_no' => $request->receiptNo,
                        'status' => $request->status,
                    ]);
                    $transactionID = $invoiceReturn->returnTransaction->id;
                } else {
                    // get logged in user id
                    $userId = auth()->user()->id;
                    $reason = '['.config('config.purchaseReturnPrefix').'-'.$invoiceReturn->code.'] Invoice Return payable sent from ['.$request->account['accountNumber'].']';
                    // create transaction
                    $transaction = AccountTransaction::create([
                        'reason' => $reason,
                        'account_id' => $request->account['id'],
                        'amount' => $request->returnAmount,
                        'transaction_date' => $request->date,
                        'type' => 0,
                        'cheque_no' => $request->chequeNo,
                        'receipt_no' => $request->receiptNo,
                        'status' => $request->status,
                        'created_by' => $userId,
                    ]);
                    $transactionID = $transaction->id;
                }
                $isPaid = 1;
            } else {
                if (isset($invoiceReturn->returnTransaction)) {
                    $invoiceReturn->returnTransaction->delete();
                }
            }

            // update invoice return
            $invoiceReturn->update([
                'reason' => $request->returnReason,
                'transaction_id' => $transactionID,
                'total_return' => $request->totalReturn,
                'date' => $request->date,
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            /*Update Invoice Return Product*/

            if(!empty($request->selectedProducts)){
                // delete return products and store new return products
                $invoiceReturn->invoiceReturnProducts->each->delete();
                foreach ($request->selectedProducts as $key => $selectedProduct) {
                    // update product inventory
                    $product = Product::where('slug', $selectedProduct['slug'])->first();
                    $product->update([
                        'inventory_count' => $product->inventory_count - $selectedProduct['oldQty'] + $selectedProduct['returnQty'],
                    ]);

                    // store product
                    if ($selectedProduct['returnQty']) {
                        InvoiceReturnProduct::create([
                            'return_id' => $invoiceReturn->id,
                            'product_id' => $selectedProduct['id'],
                            'sale_price' => $selectedProduct['unitCost'],
                            'purchase_price' => $selectedProduct['purchasePrice'],
                            'quantity' => $selectedProduct['returnQty'],
                            'service_id' => NULL,
                        ]);
                    }
                }
            }
            
            /*Update Invoice Return Service*/

            if(!empty($request->selectedServices)){
                /*delete All Return Services And Added New*/
                
                // store service
                if ($selectedProduct['returnQty']) {
                    InvoiceReturnProduct::create([
                        'return_id' => $invoiceReturn->id,
                        'product_id' => NULL,
                        'sale_price' => $selectedProduct['unitCost'],
                        'purchase_price' => $selectedProduct['purchasePrice'],
                        'quantity' => $selectedProduct['returnQty'],
                        'service_id' => $selectedProduct['service_id'],
                    ]);
                }
            }

            // update invoice
            $invoiceReturn->invoice->update([
                'discount' => $request->invoiceDiscount,
                'is_paid' => $isPaid,
            ]);

            return $this->responseWithSuccess('Invoice return updated successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
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
            $invoiceReturn = InvoiceReturn::where('slug', $slug)->with('invoiceReturnProducts.product', 'invoice', 'returnTransaction')->first();
            // update invoice
            $invoice = $invoiceReturn->invoice;
            $isPaid = $invoiceReturn->invoice->totalDue() == 0 ? 1 : 0;
            $newDiscount = $invoice->discount;
            if ($invoice->discount_type == 1) {
                $newDiscount = ($invoice->discountPercentage() / 100) * $invoice->sub_total;
                $invoice->update([
                    'discount' => $newDiscount,
                    'is_paid' => $isPaid,
                ]);
            }
            $invoiceReturn->delete();

            return $this->responseWithSuccess('Invoice return deleted successfully!');
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
        $query = InvoiceReturn::with('invoice.client', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('reason', 'LIKE', '%'.$term.'%')
                ->orWhere('slug', 'LIKE', '%'.$term.'%')
                ->orWhereHas('invoice', function ($newQuery) use ($term) {
                    $newQuery->where('invoice_no', 'LIKE', '%'.$term.'%')
                        ->orWhere('po_reference', 'LIKE', '%'.$term.'%')
                        ->orWhereHas('client', function ($anotherQuery) use ($term) {
                            $anotherQuery->where('name', 'LIKE', '%'.$term.'%')
                                ->orWhere('client_id', 'LIKE', '%'.$term.'%');
                        });
                });
        });

        return InvoiceReturnListResource::collection($query->latest()->paginate($request->perPage));
    }

    public function handleLedgerAccounts($code,$request,$type)
    {
        
        $reason = '['.config('config.invoiceReturnPrefix').'-'.$code.'] Invoice Return Reference invoice is ['.$request->invoice["label"].']';
        $hotelId = $request->client['hotel']['id'];
        $plutusId = $this->createPlutusEntry($hotelId, $reason, now(), $request->newDue);
        
        $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-RECEIVABLE')->first();
        $advanceAccount = $advanceAccount->getAccoutnbalance;
        if ($advanceAccount) {
            if($type == 'service'){
                $amount = $request->invoiceDue - $request->newDue;
            } else {
                $amount = $request->invoice['invoiceTotal'] - $request->newDue;
            }
            $advanceAccount->balanceTransactions()->create([
                'amount' => $amount,
                'reason' => '[' . config('config.invoiceReturnPrefix') . '-' . $code . '] Invoice Payment added to account]',
                'type' => 0,
                'transaction_date' => now(),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'invoice_id' => $request->invoice['id'],
                'hotel_id' => $hotelId,
                'customer_id' => $request->client['id'],
                'plutus_entries_id' => $plutusId
            ]);
        }

        $outputGst = LedgerAccount::where('code', 'GST-OUTPUT')->first();
        if ($outputGst) {
           
                $outputGst = $outputGst->getAccoutnbalance;
                $outputGst && $outputGst->balanceTransactions()->create([
                    'amount' => $request->invoiceTax - $request->newTax,
                    'reason' => '[' . config('config.invoiceReturnPrefix') . '-' . $code . '] Invoice Payment GST',
                    'type' => 0,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'invoice_id' => $request->invoice['id'],
                    'hotel_id' => $hotelId,
                    'customer_id' => $request->client['id'],
                    'plutus_entries_id' => $plutusId
                ]);
            }

            $payableAccount = LedgerAccount::where('code', 'DIRECT-SALES')->first();
            if($payableAccount){
                if($type == 'service'){
                    $amount = $request->invoice['subTotal'] - $request->newSubTotal;
                } else {
                    $totalPayble = $request->invoice['invoiceTotal'] - $request->newDue;
                    $totalTax = $request->invoiceTax - $request->newTax;
                    $amount = $totalPayble - $totalTax;
                }
                $payableAccount = $payableAccount->getAccoutnbalance;
                $payableAccount->balanceTransactions()->create([
                    'amount' => $amount,
                    'reason' => '[' . config('config.invoiceReturnPrefix') . '-' . $code . '] Invoice Payment pending',
                    'type' => 0,
                    'transaction_date' => now(),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'invoice_id' => $request->invoice['id'],
                    'hotel_id' => $hotelId,
                    'customer_id' => $request->client['id'],
                    'plutus_entries_id' => $plutusId
                ]);
            }
            
            return true;
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