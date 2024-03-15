<?php

namespace Modules\Accounts\Http\Controllers\Api;

use App\Models\GeneralSetting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Modules\Shops\Transformers\CommonResource;
use Modules\Shops\Traits\ApiResponse;
use Exception;
use Modules\Accounts\Entities\Ledger;
use Modules\Accounts\Entities\LedgerAccount;
use Modules\Accounts\Entities\LedgerCategory;
use App\Models\Account;
// use Modules\Accounts\Transformers\RevenueResource;
use Modules\Accounts\Transformers\LedgerNameAndGroupResource;
use Modules\Accounts\Transformers\{
    RevenueResource,
    ExpenseResource,
    AssetResource,
    LiabilityResource
};
use Illuminate\Support\Collection;

class LedgerController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    // define middleware
    public function __construct()
    {
        $this->middleware('can:ledger-group-list', ['only' => ['index']]);
        $this->middleware('can:ledger-group-create', ['only' => ['ledgerGroupCreate']]);
        $this->middleware('can:ledger-group-edit', ['only' => ['ledgerGroupCreate']]);
        $this->middleware('can:ledger-group-delete', ['only' => ['destroyLedgerTypehow']]);
    }

    public function index()
    {
        return CommonResource::collection(Ledger::orderBy("position", "asc")->get());
    }

    /**
     * Show the form for creating a  ledger group.
     * @return Renderable
     */
    public function ledgerGroupCreate(Request  $request)
    {

        if (!empty($request->cat_id)) {
            $this->validate($request, [
                'name' =>  'required|string|max:100|unique:ledgers_categories,name,' . $request->cat_id,
                'coa_id' =>  'required',
                'system_name' =>  'required|string|max:100|unique:ledgers_categories,system_name,' . $request->cat_id,
            ]);
            try {
                $hotelcategory = LedgerCategory::where('id', $request->cat_id)->first();
                $hotelcategory->update([
                    'coa_id' => $request->coa_id,
                    'name' => $request->name,
                    'system_name' => $request->system_name,
                    'position' => $request->position,
                ]);
                return $this->responseWithSuccess('Ledger Group Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'name' =>  'required|string|max:100|unique:ledgers_categories,name',
                'coa_id' =>  'required',
                'system_name' =>  'required|string|max:100|unique:ledgers_categories,system_name',
            ]);
            try {
                LedgerCategory::create([
                    'coa_id' => $request->coa_id,
                    'name' => $request->name,
                    'system_name' => $request->system_name,
                    'position' => $request->position,
                ]);
                return $this->responseWithSuccess('Ledger Group added successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showLedgerTypehow($id)
    {
        try {
            $ledgerType = LedgerCategory::with('ledger')->where('id', $id)->first();
            if (@$ledgerType) {
                return new CommonResource($ledgerType);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroyLedgerTypehow($id)
    {
        try {
            $ledgercategory = LedgerCategory::where('id', $id)->first();
            $ledgercategory->delete();
            return $this->responseWithSuccess('Ledger Group deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function listLedgerType(Request $request)
    {
        return CommonResource::collection(LedgerCategory::with('ledger')->latest()->paginate($request->perPage));
    }

    public function ledgerCreate(Request  $request)
    {
        if (!empty($request->cat_id)) {
            $this->validate($request, [
                'ledger_name' =>  'required|string|max:100|unique:ledgers_accounts,ledger_name,' . $request->cat_id,
                'ledger_type' =>  'required',
                'code' =>  'required',
                'system_name' =>  'required|string|max:100|unique:ledgers_accounts,system_name,' . $request->cat_id,
                'hotel_id' => 'nullable'
            ]);
            try {
                $hotelcategory = LedgerAccount::where('id', $request->cat_id)->first();
                $hotelcategory->update([
                    'ledger_group' => $request->ledger_group,
                    'ledger_type' => $request->ledger_type,
                    'ledger_name' => $request->ledger_name,
                    'system_name' => $request->system_name,
                    'code' => $request->code,
                    'is_bank' => $request->is_bank,
                    'show_in_day_book' => $request->show_in_day_book,
                    'hotel_id' => @$request->hotel_id['id']
                ]);

                Account::where('id', $request->acc_id)->update([
                    'bank_name' => $request->bankName,
                    'branch_name' => $request->branchName,
                    'account_number' => $request->accountNumber,
                    'date' => $request->date,
                    'note' => clean($request->note),
                    'status' => $request->status,
                ]);
                return $this->responseWithSuccess('Ledger  Edit successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        } else {
            $this->validate($request, [
                'ledger_name' =>  'required|string|max:100|unique:ledgers_accounts,ledger_name',
                'ledger_type' =>  'required',
                'code' =>  'required',
                'system_name' =>  'required|string|max:100|unique:ledgers_accounts,system_name',
            ]);
            try {
                $account =  LedgerAccount::create([
                    'ledger_group' => $request->ledger_group,
                    'ledger_type' => $request->ledger_type,
                    'ledger_name' => $request->ledger_name,
                    'system_name' => $request->system_name,
                    'code' => $request->code,
                    'is_bank' => $request->is_bank,
                    'show_in_day_book' => $request->show_in_day_book,
                    'hotel_id' => @$request->hotel_id['id']
                ]);
                Account::create([
                    'bank_name' => $request->bankName ?? '',
                    'branch_name' => $request->branchName ?? '',
                    'account_number' => $request->accountNumber ?? $request->ledger_name,
                    'date' => $request->date,
                    'created_by' => auth()->user()->id,
                    'note' => clean($request->note),
                    'status' => $request->status,
                    'account_id' => $account->id,
                ]);
                return $this->responseWithSuccess('Ledger  added successfully!');
            } catch (Exception $e) {
                return $this->responseWithError($e->getMessage());
            }
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showLedger($id)
    {
        try {
            $ledgerType = LedgerAccount::with('ledger', 'ledgerCategory', 'getAccoutnbalance', 'hotel')->where('id', $id)->first();
            if (@$ledgerType) {
                return new CommonResource($ledgerType);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function getIdWise(Request $request)
    {
        try {
            if (@$request->bank_only) {
                $ledgerType = LedgerAccount::with('ledger', 'ledgerCategory', 'hotel', 'getAccoutnbalance')->where('is_bank', 1)->paginate($request->perPage);
            } else if (@$request->ledger_type) {
                $ledgerType = LedgerAccount::with('ledger', 'ledgerCategory', 'hotel', 'getAccoutnbalance')->where('ledger_type', $request->ledger_type)->paginate($request->perPage);
            } else {
                $ledgerType = LedgerAccount::with('ledger', 'ledgerCategory', 'getAccoutnbalance', 'hotel')->paginate($request->perPage);
            }
            if (@$ledgerType) {
                return new CommonResource($ledgerType);
            } else {
                return $this->responseWithError('Sorry you request can\'t Process!');
            }
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroyLedger($id)
    {
        try {
            $ledgercategory = LedgerAccount::where('id', $id)->first();
            $ledgercategory->delete();
            return $this->responseWithSuccess('Ledger deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function listLedger(Request $request)
    {
        return CommonResource::collection(LedgerAccount::with('ledger', 'ledgerCategory', 'getAccoutnbalance')->latest()->paginate($request->perPage));
    }

    /*
    Profit Loss Revenue & Expense
    */

    public function revenueCalculation(Request $request)
    {

        try {
            $data = LedgerAccount::with([
                'ledger',
                'ledgerCategory',
                'getAccoutnbalance'   => function ($q) use ($request) {
                    $q->whereHas('balanceTransactions', function ($query) use ($request) {
                        $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                    });
                },
                'balanceTransactions' => function ($query) use ($request) {
                    $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                },
            ])->where('ledger_type', $request->ledger_type)->get();

            $collection = new Collection($data);
            $groupedData = $collection->groupBy('ledger_group');
            return RevenueResource::collection($groupedData);

            // return RevenueResource::collection(LedgerAccount::with('ledger', 'ledgerCategory', 'hotel', 'getAccoutnbalance')->where('ledger_type', 4)->get())->groupBy('ledger_group');

        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function expenseCalculation(Request $request)
    {
        try {

            $data = LedgerAccount::with([
                'ledger',
                'ledgerCategory',
                'getAccoutnbalance'   => function ($q) use ($request) {
                    $q->whereHas('balanceTransactions', function ($query) use ($request) {
                        $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                    });
                },
                'balanceTransactions' => function ($query) use ($request) {
                    $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                },
            ])->where('ledger_type', $request->ledger_type)->get();

            //            $data = LedgerAccount::with('ledger', 'ledgerCategory', 'getAccoutnbalance','balanceTransactions')
            //                    ->where('ledger_type', $request->ledger_type)
            //                    ->whereHas('balanceTransactions', function ($query) use ($request) {
            //                        $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
            //                    })
            //                    ->get();
            $collection = new Collection($data);
            $groupedData = $collection->groupBy('ledger_group');
            return ExpenseResource::collection($groupedData);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /*Balance Sheet Asset & Liability */

    public function assetCalculation(Request $request)
    {
        try {
            $data = LedgerAccount::with([
                'ledger', 'ledgerCategory',
                'getAccoutnbalance'   => function ($q) use ($request) {
                    $q->whereHas('balanceTransactions', function ($query) use ($request) {
                        $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                    });
                },
                'balanceTransactions' => function ($query) use ($request) {
                    $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                },
            ])->where('ledger_type', $request->ledger_type)->get();

            $collection = new Collection($data);
            $groupedData = $collection->groupBy('ledger_group');
            return AssetResource::collection($groupedData);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    public function liabilityCalculation(Request $request)
    {
        try {
            /*Get Total Revenue*/
            $revenue = LedgerAccount::with([
                'ledger', 'ledgerCategory',
                'getAccoutnbalance'   => function ($q) use ($request) {
                    $q->whereHas('balanceTransactions', function ($query) use ($request) {
                        $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                    });
                },
                'balanceTransactions' => function ($query) use ($request) {
                    $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                },
            ])->where('ledger_type', 4)->get();
            $collection = new Collection($revenue);
            $groupedData = $collection->groupBy('ledger_group');
            $getTotalRevenue = $groupedData->map(function ($items) {
                $sum = $items->sum('getAccoutnbalance.available_balance');
                return [
                    $sum,
                ];
            });

            $totalRevenueArray = $getTotalRevenue->toArray();
            $reArrange = array_values($totalRevenueArray);
            $totalRevenue = array_sum(array_map('array_sum', $reArrange));

            // $totalRevenue = 0;

            /*Get Total Expense*/

            $expense = LedgerAccount::with([
                'ledger', 'ledgerCategory',
                'getAccoutnbalance'   => function ($q) use ($request) {
                    $q->whereHas('balanceTransactions', function ($query) use ($request) {
                        $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                    });
                },
                'balanceTransactions' => function ($query) use ($request) {
                    $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                },
            ])->where('ledger_type', 5)->get();
            $collection = new Collection($expense);
            $groupedData = $collection->groupBy('ledger_group');
            $getTotalExpense = $groupedData->map(function ($items) {
                $sum = $items->sum('getAccoutnbalance.available_balance');
                return [
                    $sum,
                ];
            });

            $totalExpenseArray = $getTotalExpense->toArray();
            $reArrange = array_values($totalExpenseArray);
            $totalExpense = array_sum(array_map('array_sum', $reArrange));
            // $totalExpense = 0;

            $ledgerType = explode(',', $request->ledger_type);
            $data = LedgerAccount::with([
                'ledger', 'ledgerCategory',
                'getAccoutnbalance'   => function ($q) use ($request) {
                    $q->whereHas('balanceTransactions', function ($query) use ($request) {
                        $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                    });
                },
                'balanceTransactions' => function ($query) use ($request) {
                    $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                },
            ])->whereIn('ledger_type', $ledgerType)->get();
            $collection = new Collection($data);
            $groupedData = $collection->groupBy('ledger_group');

            $profitLossArray = [
                [
                    "group_name" => "Profit & Loss A/c",
                    "total_amount" => $totalRevenue - $totalExpense,
                    "items" => [
                        // [
                        //     "name" => "Opening Bal",
                        //     "amount" => $totalRevenue - $totalExpense
                        // ]
                    ],
                ]
            ];

            return $combinedCollection = new Collection([
                'groupedData' => LiabilityResource::collection($groupedData),
                'newData' => $profitLossArray,
            ]);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }
}
