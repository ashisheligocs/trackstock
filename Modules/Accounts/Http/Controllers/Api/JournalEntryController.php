<?php

namespace Modules\Accounts\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Hotel\Transformers\CommonResource;
use Modules\Hotel\Traits\ApiResponse;
use Exception;
// use Modules\Accounts\Entities\Ledger;
use Modules\Accounts\Entities\{
    LedgerAccount,
    PlutusEntries,
    JournalEntry
};
use App\Models\Account;
use \App\Models\AccountTransaction;

class JournalEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:journal-entry-list', ['only' => ['getJournalData']]);
        $this->middleware('can:journal-entry-create', ['only' => ['journalCreate']]);
        $this->middleware('can:journal-entry-edit', ['only' => ['journalUpdate']]);
        $this->middleware('can:journal-entry-delete', ['only' => ['deleteJournalEntry']]);
    }

    use ApiResponse;

    /*Get Ledger Group and Name for Journal Group*/
     /**
     *
     * @return Renderable
     */
    public function getAllAccountGroups(){
        try {
            $ledgerAccounts = LedgerAccount::with('ledgerCategory')->get();
            $ledgerAccounts = $ledgerAccounts->map(function ($item) {
                return [$item->ledger_name . ' - ' . $item->ledgerCategory->name];
            });
            return CommonResource::collection($ledgerAccounts);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /*Get All Journal Entry*/

    public function getJournalData(){
        try {
            $journalEntry = JournalEntry::get();
            return CommonResource::collection($journalEntry);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /*Get All Detail Entry*/
    public function getJournalDetail($id){
        try {

            $journalEntryDetails = JournalEntry::with('hotel', 'balanceTransactions.ledgerAccount.ledgerCategory')
                ->where('id', $id)->first();
            
            $setTableData = collect($journalEntryDetails->balanceTransactions)->map(function ($transaction) {
                $isAssetOrExpense = $transaction->ledgerAccount->code === 'GST-INPUT' ||
                    in_array($transaction->ledgerAccount->ledger_type, [1, 5]);
            
                $type = $isAssetOrExpense ? !((bool) $transaction->type) : $transaction->type;
            
                return [
                    'account' => [$transaction->ledgerAccount->ledger_name . ' - ' . $transaction->ledgerAccount->ledgerCategory->name],
                    'debit' => ($type == 0) ? $transaction->amount : 0,
                    'credit' => ($type == 1) ? $transaction->amount : 0,
                    'description' => $transaction->note,
                ];
            });

            return [
                'journaldData' => CommonResource::make($journalEntryDetails),
                'tableData' => $setTableData,
            ];
            //  return CommonResource::make($journalEntryDetails);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /* Create Journal Entry*/

    public function journalCreate(Request $request){

        $data = $request->input();
        
        $this->validate($request, [
            'note' =>  'required|string|max:800|',
            'date' => 'required|date_format:Y-m-d',
            'hotel_id' => 'nullable',
            'amount'=> 'required|numeric'
        ]);

        try {
                $createJournal = JournalEntry::create([
                    'hotel_id' => @$request->hotel_id['id'],
                    'note' => $request->note,
                    'date' => $request->date,
                    'amount' => $request->amount,
                ]);
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
            $journalId = $createJournal->id;

            $note = "Journal Entry for ".$request->hotel_id['hotel_name'] ;
            $plutusId = $this->createPlutusEntry($request->hotel_id['id'],$note,now(),$request->amount);
            
            /*Add New Transactions*/
            $createTransaction = $this->saveTransactionJournalEntry($data,$journalId,$plutusId);
            if($createTransaction){
                return $this->responseWithSuccess('Journal Entry added successfully!');
            }
    }

    /* Update Journal Entry */

    public function journalUpdate(Request $request){
        $data = $request->input();
        $journalId = $request->id;

        $this->validate($request, [
            'note' =>  'required|string|max:800|',
            'date' => 'required|date_format:Y-m-d',
            'hotel_id' => 'nullable',
            'amount'=> 'required|numeric'
        ]);

        try {

            $journalEntry = JournalEntry::where('id', $journalId)->first();
                $journalEntry->update([
                    'hotel_id' => @$request->hotel_id['id'],
                    'note' => $request->note,
                    'date' => $request->date,
                    'amount' => $request->amount,
                ]);

            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        
        /*Get Old Plutus ID If get then use otherwise create*/
        $getPlutusId = AccountTransaction::where('journal_entry_id',$journalId)->first();
        if($getPlutusId->plutus_entries_id !== NULL){
            $plutusId = $getPlutusId->plutus_entries_id;
        } else {
            $note = "Journal Entry for ".$request->hotel_id['hotel_name'] ;
            $plutusId = $this->createPlutusEntry($request->hotel_id['id'],$note,now(),$request->amount);
        }

        /*Delete Old Transactions*/
        AccountTransaction::where('journal_entry_id',$journalId)->delete();
        
        /*Add New Transactions*/    
        $updateTransaction = $this->saveTransactionJournalEntry($data,$journalId,$plutusId);
        if($updateTransaction){
            return $this->responseWithSuccess('Journal Entry updated successfully!');
        }
    }

    /*Delete Journal Entry && Plutus entry*/

    public function deleteJournalEntry($id){
        try {
            
            $getPlutusId = AccountTransaction::where('journal_entry_id',$id)->first();
            if($getPlutusId->plutus_entries_id !== NULL){
                PlutusEntries::where('id',$getPlutusId->plutus_entries_id)->delete();
            }
            
            $journalEntry = JournalEntry::where('id', $id)->first();
            $journalEntry->delete();
            return $this->responseWithSuccess('Journal deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function saveTransactionJournalEntry($postData,$journalId,$plutusId){

        for($i=0; $i<count($postData['tableData']); $i++){
            
            if($postData['tableData'][$i][0] !== null && 
                ($postData['tableData'][$i][1] !== null  || $postData['tableData'][$i][2] !== null)){
                
                $extractFirstColumn = $postData['tableData'][$i][0][0];
                $ledgerAccount = explode(' - ',$extractFirstColumn);
                $getLedgerAccount = LedgerAccount::where('ledger_name',$ledgerAccount[0])->get();
                $account = LedgerAccount::where('ledger_name',$ledgerAccount[0])->first()->getAccoutnbalance;

                if(!empty($getLedgerAccount)){
                    $accountId = $getLedgerAccount[0]->id;
                    $isAssetOrExpense = $getLedgerAccount[0]->code === 'GST-INPUT' || in_array($getLedgerAccount[0]->ledger->id, [1, 5]);
                    $type = ($postData['tableData'][$i][1] !== null && $postData['tableData'][$i][1] != 0) ? 0 : 1;
                    $type = $isAssetOrExpense ? intVal(!(boolean) $type) : $type;
                    $accountTransaction[] = $account->balanceTransactions()->create([
                        'amount' => ($postData['tableData'][$i][1] !== null && $postData['tableData'][$i][1] != 0) ? $postData['tableData'][$i][1] : $postData['tableData'][$i][2],
                        'reason' => 'Journal Entry',
                        'type' => $type,
                        'transaction_date' => $postData['date'],
                        'cheque_no' => null,
                        'receipt_no' => null,
                        'created_by' => auth()->user()->id,
                        'status' => 1,
                        'note' => $postData['tableData'][$i][3],
                        'journal_entry_id' => $journalId,
                        'hotel_id' => $postData['hotel_id']['id'],
                        'account_id ' => $accountId,
                        'plutus_entries_id' => $plutusId
                    ]);
                }
            }
           
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
