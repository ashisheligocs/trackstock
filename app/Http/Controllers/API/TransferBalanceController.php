<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\BalanceTansfer;
use App\Models\AccountTransaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceTransferResource;
use Modules\Accounts\Entities\PlutusEntries;

class TransferBalanceController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:account-transfer-balance-list', ['only' => ['index', 'search']]);
        $this->middleware('can:account-transfer-balance-create', ['only' => ['create']]);
        $this->middleware('can:account-transfer-balance-view', ['only' => ['show']]);
        $this->middleware('can:account-transfer-balance-edit', ['only' => ['update']]);
        $this->middleware('can:account-transfer-balance-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = auth()->user()->id;
     
        if(auth()->user()->roles[0]->id !== 1){
            return BalanceTransferResource::collection(BalanceTansfer::with('debitTransaction.cashbookAccount', 'creditTransaction.cashbookAccount', 'user')->where('created_by',$userId)->latest()->paginate($request->perPage));
        } else {
            return BalanceTransferResource::collection(BalanceTansfer::with('debitTransaction.cashbookAccount', 'creditTransaction.cashbookAccount', 'user')->latest()->paginate($request->perPage));
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate request
       
        $this->validate($request, [
            // 'transferReason' => 'required|string|max:255',
            'cashAccount' => 'required',
            'bankAccount' => 'required',
            'toAccount' => 'required|different:cashAccount',
            'amountcash' => 'required|numeric|min:1|max:'.$request->availableBalanceCash,
            'amountqr' => 'required|numeric|min:1|max:'.$request->availableBalanceQr,
            'date' => 'nullable|date_format:Y-m-d',
            // 'note' => 'nullable|string|max:255',
            'shop_id' => 'required'
        ]);

        try {
            // get logged in user id
            $userId = auth()->user()->id;

            $toAccountNumber = $request->toAccount['accountNumber'];
            $fromCashAccountNumber = $request->cashAccount['accountNumber'];

            $fromBankAccountNumber = $request->bankAccount['accountNumber'];

            $totalAmount = $request->amountcash + $request->amountqr;

            $note = "Balance transfer to [$toAccountNumber] by ".auth()->user()->name;
            $plutusId = $this->createPlutusEntry($request->shop_id,$note,now(),$totalAmount);

            
            $debitReason = "Balance transfer from [$fromCashAccountNumber]";
            // store debit transaction
            $debitTransaction = AccountTransaction::create([
                'account_id' => $request->cashAccount['id'],
                'amount' => $request->amountcash,
                'reason' => $debitReason,
                'type' => 0,
                'transaction_date' => $request->date,
                'created_by' => $userId,
                'status' => $request->status,
                'shop_id' => $request->shop_id,
                'plutus_entries_id' => $plutusId
            ]);

            
            $creditReason = "Balance transfer to [$toAccountNumber]";
            // store credit transaction
            $creditTransaction = AccountTransaction::create([
                'account_id' => $request->toAccount['id'],
                'amount' => $request->amountcash,
                'reason' => $creditReason,
                'type' => 1,
                'transaction_date' => $request->date,
                'created_by' => $userId,
                'status' => $request->status,
                'shop_id' => $request->shop_id,
                'plutus_entries_id' => $plutusId
            ]);

            // create transfer
            BalanceTansfer::create([
                'reason' => ($request->transferReason != null) ? $request->transferReason : 'Cash Collect',
                'debit_id' => $debitTransaction->id,
                'credit_id' => $creditTransaction->id,
                'amount' => $request->amountcash,
                'date' => $request->date,
                'note' => clean($request->note),
                'status' => $request->status,
                'created_by' => $userId,
                'shop_id' => $request->shop_id
            ]);

            if($request->amountqr != 0){

            $debitReason = "Balance transfer from [$fromBankAccountNumber]";
            // store debit transaction
            $debitTransaction1 = AccountTransaction::create([
                'account_id' => $request->bankAccount['id'],
                'amount' => $request->amountqr,
                'reason' => $debitReason,
                'type' => 0,
                'transaction_date' => $request->date,
                'created_by' => $userId,
                'status' => $request->status,
                'shop_id' => $request->shop_id,
                'plutus_entries_id' => $plutusId
            ]);

            
            $creditReason = "Balance transfer to [$toAccountNumber]";
            // store credit transaction
            $creditTransaction1 = AccountTransaction::create([
                'account_id' => $request->toAccount['id'],
                'amount' => $request->amountqr,
                'reason' => $creditReason,
                'type' => 1,
                'transaction_date' => $request->date,
                'created_by' => $userId,
                'status' => $request->status,
                'shop_id' => $request->shop_id,
                'plutus_entries_id' => $plutusId
            ]);

            // create transfer
            BalanceTansfer::create([
                'reason' => ($request->transferReason != null) ? $request->transferReason : 'Cash Collect From Qr',
                'debit_id' => $debitTransaction1->id,
                'credit_id' => $creditTransaction1->id,
                'amount' => $request->amountqr,
                'date' => $request->date,
                'note' => clean($request->note),
                'status' => $request->status,
                'created_by' => $userId,
                'shop_id' => $request->shop_id
            ]);

            }

            return $this->responseWithSuccess('Transfer added successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage().'--'.$e->getLine());
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
            $transfer = BalanceTansfer::with('debitTransaction', 'creditTransaction', 'user')->where('slug', $slug)->first();
            return new BalanceTransferResource($transfer);
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
        $transfer = BalanceTansfer::with('debitTransaction', 'creditTransaction', 'user')->where('slug', $slug)->first();
        // validate request
        $this->validate($request, [
            // 'transferReason' => 'required|string|max:255',
            'fromAccount' => 'required',
            'amount' => 'required|numeric|min:1|max:'.$request->availableBalance,
            'date' => 'nullable|date_format:Y-m-d',
            // 'note' => 'nullable|string|max:255',
            'shop_id' => 'required'
        ]);

        try {
            // update debit transaction
            $transfer->debitTransaction->update([
                'account_id' => $request->fromAccount['id'],
                'amount' => $request->amount,
                'transaction_date' => $request->date,
                'status' => $request->status,
                'shop_id' => $request->shop_id
            ]);

            // update debit transaction
            $transfer->creditTransaction->update([
                'amount' => $request->amount,
                'transaction_date' => $request->date,
                'status' => $request->status,
                'shop_id' => $request->shop_id
            ]);

            // update transfer
            $transfer->update([
                'reason' => ($request->transferReason != null) ? $request->transferReason : 'Cash Collect',
                'amount' => $request->amount,
                'date' => $request->date,
                'note' => clean($request->note),
                'status' => $request->status,
                'shop_id' => $request->shop_id
            ]);

            return $this->responseWithSuccess('Transfer updated successfully');
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
            $transfer = BalanceTansfer::where('slug', $slug)->first();

            // check if the transfer can be delete
            $canDelete = true;
            if ($transfer->creditTransaction->cashbookAccount->availableBalance() < $transfer->amount) {
                $canDelete = false;
            }

            if ($canDelete) {
                // delete transfer transactions
                $transfer->debitTransaction->delete();
                $transfer->creditTransaction->delete();
                $transfer->delete();
            } else {
                return $this->responseWithError('Sorry you can\'t delete this transfer!');
            }

            return $this->responseWithSuccess('Transfer deleted successfully');
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
        $query = BalanceTansfer::with('debitTransaction.cashbookAccount', 'creditTransaction.cashbookAccount', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('reason', 'LIKE', '%'.$term.'%')
                ->orWhere('amount', 'LIKE', '%'.$term.'%')
                ->orWhereHas('debitTransaction', function ($newQuery) use ($term) {
                    $newQuery->whereHas('cashbookAccount', function ($newQuery) use ($term) {
                        $newQuery->where('account_number', 'LIKE', '%'.$term.'%')
                            ->orWhere('bank_name', 'LIKE', '%'.$term.'%');
                    });
                })
                ->orWhereHas('creditTransaction', function ($newQuery) use ($term) {
                    $newQuery->whereHas('cashbookAccount', function ($newQuery) use ($term) {
                        $newQuery->where('account_number', 'LIKE', '%'.$term.'%')
                            ->orWhere('bank_name', 'LIKE', '%'.$term.'%');
                    });
                });
        });

        return BalanceTransferResource::collection($query->latest()->paginate($request->perPage));
    }

     /*Manage Plutus Entry*/
     protected function createPlutusEntry($hotelId, $note, $date, $amount)
     {
         $createPlutus = PlutusEntries::create([
             'shop_id' => $hotelId,
             'note' => $note,
             'date' => $date,
             'amount' => $amount,
         ]);
         
         return $createPlutus->id;
     }
}
