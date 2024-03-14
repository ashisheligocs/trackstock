<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Rules\MinItem;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\InvoicePayment;
use App\Models\AccountTransaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoicePaymentResource;
use App\Models\Client;
use App\Notifications\ClientInvoicePaymentNotification;
use Modules\Accounts\Entities\{
    LedgerAccount,
    PlutusEntries,
};

class InvoicePaymentController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:invoice-payment-list', ['only' => ['index', 'search']]);
        $this->middleware('can:invoice-payment-create', ['only' => ['create']]);
        $this->middleware('can:invoice-payment-view', ['only' => ['show']]);
        $this->middleware('can:invoice-payment-edit', ['only' => ['update']]);
        $this->middleware('can:invoice-payment-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        return InvoicePaymentResource::collection(InvoicePayment::with('invoice.client', 'invoice.invoiceTax', 'invoicePaymentTransaction.cashbookAccount', 'user')->latest()->paginate($request->perPage));
    }

    public function store(Request $request)
    {
        // validate request
        $this->validate($request, [
            'client' => 'required',
            'selectedInvoices' => [new MinItem('invoice')],
            'account' => 'required',
            'chequeNo' => 'nullable|string|max:255',
            'voucherNo' => 'nullable|string|max:255',
            'paymentDate' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            // get logged in user id
            $userId = auth()->user()->id;
            $invoices = array();
            $client = Client::where('slug', $request['client']['slug'])->first();

            foreach ($request->selectedInvoices as $key => $selectedInvoice) {
                // get invoice
                $invoice = Invoice::where('slug', $selectedInvoice['slug'])->first();

                // store transaction
                $transactionID = null;
                $reason = '['.config('config.invoicePrefix').'-'.$invoice->invoice_no.'] Invoice payment added to ['.$request->account['accountNumber'].']';
                
                /*Create Plutus Entry*/
                $plutusId = $this->createPlutusEntry($invoice->hotel_id,$reason,now(),$selectedInvoice['paidAmount']);
                // create transaction
                $transaction = AccountTransaction::create([
                    'account_id' => $request->account['id'],
                    'amount' => $selectedInvoice['paidAmount'],
                    'reason' => $reason,
                    'type' => 1,
                    'cheque_no' => $request->chequeNo,
                    'receipt_no' => $request->receiptNo,
                    'transaction_date' => $request->paymentDate,
                    'created_by' => $userId,
                    'status' => $request->status,
                    'hotel_id' => $invoice->hotel_id,
                    'customer_id' => $invoice->client_id,
                    'plutus_entries_id' => $plutusId
                ]);
                $transactionID = $transaction->id;

                $this->manageLedger($invoice, $selectedInvoice['paidAmount'],false,$plutusId,$request);

                // store invoice payment record
                InvoicePayment::create([
                    'slug' => uniqid(),
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $transactionID,
                    'amount' => $selectedInvoice['paidAmount'],
                    'date' => $request->paymentDate,
                    'created_by' => $userId,
                    'note' => clean($request->note),
                    'status' => $request->status,
                ]);

                // update invoice
                $invoice->update([
                    'is_paid' => $selectedInvoice['newDue'] == 0 ? 1 : 0,
                ]);

                $invoice['amount_paid'] = $selectedInvoice['paidAmount'];
                array_push($invoices, $invoice);
            }

            if ($request->isSendEmail || $request->isSendEmail) {
                $client->notify(new ClientInvoicePaymentNotification($invoices, [
                    'isSendEmail' => filter_var($request->isSendEmail, FILTER_VALIDATE_BOOLEAN),
                    'isSendSMS' =>  filter_var($request->isSendSMS, FILTER_VALIDATE_BOOLEAN)
                ]));
            }

            return $this->responseWithSuccess('Client payment added successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function manageLedger($invoice, $amount, $credit = false, $plutusId,$request)
    {
        //manage receivable
        $advanceAccount = LedgerAccount::where('code', 'ACCOUNT-RECEIVABLE')->first();
        $advanceAccount = $advanceAccount->getAccoutnbalance;
        if ($advanceAccount) {
            $advanceAccount->balanceTransactions()->create([
                'amount' => $amount,
                'reason' => '[' . config('config.invoicePrefix') . '-' . $invoice->invoice_no . '] Invoice Payment added to account]',
                'type' => $credit ? 1 : 0,
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

    public function show($slug)
    {
        try {
            $invoicePayment = InvoicePayment::with('invoice.client', 'invoicePaymentTransaction.cashbookAccount', 'user')->where('slug', $slug)->first();
            return new InvoicePaymentResource($invoicePayment);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function update(Request $request, $slug)
    {
        $invoicePayment = InvoicePayment::with('invoice', 'invoicePaymentTransaction')->where('slug', $slug)->first();

        // validate request
        $this->validate($request, [
            'invoice' => 'required',
            'account' => 'required',
            'paidAmount' => 'required|numeric|min:'.$request->minAmount.'|max:'.$request->maxAmount,
            'chequeNo' => 'nullable|string|max:255',
            'receiptNo' => 'nullable|string|max:255',
            'paymentDate' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            // get invoice
            $invoice = Invoice::where('slug', $request->invoice['slug'])->first();
            
            $plutusId = $this->createPlutusEntry($invoice->hotel_id,'Invoice Update',now(),$request->paidAmount);
            // update invoice payment record
            $invoicePayment->update([
                'amount' => $request->paidAmount,
                'date' => $request->paymentDate,
                'note' => clean($request->note),
                'status' => $request->status,
            ]);

            if (! empty($request->account)) {
                // update transaction
                $invoicePayment->invoicePaymentTransaction->update([
                    'account_id' => $request->account['id'],
                    'amount' => $request->paidAmount,
                    'cheque_no' => $request->chequeNo,
                    'receipt_no' => $request->receiptNo,
                    'transaction_date' => $request->paymentDate,
                    'status' => $request->status,
                ]);

                $amount = floatval($request->paidAmount ?? 0) - floatval($invoicePayment->invoicePaymentTransaction->amount ?? 0);
                $this->manageLedger($invoice, $amount, $amount < 0,$plutusId,$request);
            }

            // update invoice
            $invoice->update([
                'is_paid' => $invoicePayment->invoice->totalDue() == 0 ? 1 : 0,
            ]);

            return $this->responseWithSuccess('Invoice payment updated successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function destroy($slug)
    {
        try {
            $invoicePayment = InvoicePayment::where('slug', $slug)->with('invoice.invoiceReturn.returnTransaction', 'invoicePaymentTransaction')->first();

            // update invoice
            if ($invoicePayment->invoice->totalDue() - $invoicePayment->amount <= 0) {
                $invoicePayment->invoice->update([
                    'is_paid' => 1,
                ]);
            }

            // delete payment
            $invoicePayment->delete();

            return $this->responseWithSuccess('Invoice payment deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $term = $request->term;
        $query = InvoicePayment::with('invoice.client', 'invoicePaymentTransaction.cashbookAccount', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('amount', '=', $term)
                ->orWhereHas('invoice', function ($newQuery) use ($term) {
                    $newQuery->where('invoice_no', 'LIKE', '%'.$term.'%')
                        ->orWhere('po_reference', 'LIKE', '%'.$term.'%')
                        ->orWhereHas('client', function ($anotherQuery) use ($term) {
                            $anotherQuery->where('name', 'LIKE', '%'.$term.'%')
                                ->orWhere('phone', 'LIKE', '%'.$term.'%');
                        });
                })
                ->orWhereHas('invoicePaymentTransaction', function ($newQuery) use ($term) {
                    $newQuery->where('cheque_no', 'LIKE', '%'.$term.'%')
                        ->orWhere('receipt_no', 'LIKE', '%'.$term.'%')
                        ->orWhereHas('cashbookAccount', function ($newQuery) use ($term) {
                            $newQuery->where('account_number', 'LIKE', '%'.$term.'%')
                                ->orWhere('bank_name', 'LIKE', '%'.$term.'%');
                        });
                });
        });

        return InvoicePaymentResource::collection($query->latest()->paginate($request->perPage));
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
