<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountTransactionResource;
use App\Models\AccountTransaction;
use Modules\Accounts\Entities\PlutusEntries;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:transaction-history', ['only' => ['allTransactions', 'searchTransactions', 'allNewTransactions']]);
    }

    //return all transactions
    public function allTransactions(Request $request)
    {
        $transactions = AccountTransaction::with('cashbookAccount', 'user')->whereBetween('transaction_date', [$request->startDate, $request->endDate])->latest()->paginate($request->perPage);
        // dd($transactions);
        return AccountTransactionResource::collection($transactions);
    }

    //all new transaction 

    public function allNewTransactions(Request $request)
    {
        // dd($request->perPage);

        // if ($request->perPage == 'all') {
        //     $plutusEntryDetails = PlutusEntries::with('hotel', 'balanceTransactions.ledgerAccount.ledger','balanceTransactions.user')
        //         ->whereBetween('date', [$request->startDate, $request->endDate])
        //         ->latest()
        //         ->get();

        //     return $plutusEntryDetails;
        // }

        $perPage = ($request->perPage == 'all') ? PHP_INT_MAX : $request->perPage;
        $plutusEntryDetails = PlutusEntries::with(
            'shop',
            'balanceTransactions.ledgerAccount.ledgerCategory',
            'balanceTransactions.ledgerAccount.ledger',
            'balanceTransactions.user'
        )->whereBetween('date', [$request->startDate, $request->endDate])->latest()->paginate($perPage);

        $setTableData = collect();

        $plutusEntryDetails->each(function ($plutusEntry) use ($setTableData) {
            $setTableData->push([
                'note' => $plutusEntry->note,
                'items' => $plutusEntry->balanceTransactions->map(function ($transaction) {
                    
                    $isAssetOrExpense = $transaction->ledgerAccount->code === 'GST-INPUT' ||
                        in_array($transaction->ledgerAccount->ledger_type, [1, 5]);
                    // dd($isAssetOrExpense);
                    $type = $isAssetOrExpense ? !((bool) $transaction->type) : $transaction->type;

                    return [
                        'ledger_category' => $transaction->ledgerAccount->ledgerCategory->name,
                        'ledger_type' => $transaction->ledgerAccount->ledger->name,
                        'ledger_name' => $transaction->ledgerAccount->ledger_name,
                        'slug' => $transaction->ledgerAccount->system_name,
                        'shop' => $transaction->shop->shop_name,
                        'debit' => ($type == 0) ? $transaction->amount : 0,
                        'credit' => ($type == 1) ? $transaction->amount : 0,
                        'date' => $transaction->transaction_date,
                        'user_name' => $transaction->user->name
                    ];
                }),
            ]);
        });

        $paginatedData = new LengthAwarePaginator(
            $setTableData,
            $plutusEntryDetails->total(),
            $plutusEntryDetails->perPage(),
            $plutusEntryDetails->currentPage(),
            ['path' => url()->current()]
        );

        return $paginatedData;
    }


    //     public function allNewTransactions(Request $request)
// {
//     if (empty($request->perPage)) {
//         // If "All" is selected, fetch all data without pagination
//         $plutusEntryDetails = PlutusEntries::with('hotel','balanceTransactions.ledgerAccount.ledger')
//             ->whereBetween('date', [$request->startDate, $request->endDate])
//             ->latest()
//             ->get();

    //         // Process $plutusEntryDetails as needed

    //         return $plutusEntryDetails;
//     }

    //     // If a specific perPage value is provided, continue with pagination logic
//     $plutusEntryDetails->each(function ($plutusEntry) use ($setTableData) {
//             $setTableData->push([
//                 'note' => $plutusEntry->note,
//                 'items' => $plutusEntry->balanceTransactions->map(function ($transaction) {

    //                     $isAssetOrExpense = $transaction->ledgerAccount->code === 'GST-INPUT' ||
//                         in_array($transaction->ledgerAccount->ledger_type, [1, 5]);
//                     // dd($isAssetOrExpense);
//                     $type = $isAssetOrExpense ? !((bool) $transaction->type) : $transaction->type;

    //                     return [
//                         'ledger_category' => $transaction->ledgerAccount->ledgerCategory->name,
//                         'ledger_type' => $transaction->ledgerAccount->ledger->name,
//                         'ledger_name' => $transaction->ledgerAccount->ledger_name,
//                         'slug' => $transaction->ledgerAccount->system_name,
//                         'hotel' => $transaction->hotel->hotel_name,
//                         'debit' => ($type == 0) ? $transaction->amount : 0,
//                         'credit' => ($type == 1) ? $transaction->amount : 0,
//                         'date' => $transaction->transaction_date
//                     ];
//                 }),
//             ]);
//         });

    //         $paginatedData = new LengthAwarePaginator(
//             $setTableData,
//             $plutusEntryDetails->total(),
//             $plutusEntryDetails->perPage(),
//             $plutusEntryDetails->currentPage(),
//             ['path' => url()->current()]
//         );

    //         return $paginatedData;
//     }


    // search and return transactions
    public function searchTransactions(Request $request)
    {
        $term = $request->term;
        $query = AccountTransaction::with('cashbookAccount', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('transaction_date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('reason', 'LIKE', '%' . $term . '%')
                ->orWhere('amount', 'LIKE', '%' . $term . '%')
                ->orWhereHas('cashbookAccount', function ($newQuery) use ($term) {
                    $newQuery->where('bank_name', 'LIKE', '%' . $term . '%')
                        ->orWhere('branch_name', 'LIKE', '%' . $term . '%')
                        ->orWhere('account_number', 'LIKE', '%' . $term . '%');
                });
        });

        return AccountTransactionResource::collection($query->latest()->paginate($request->perPage));
    }
}
