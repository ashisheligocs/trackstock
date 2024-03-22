<?php

namespace App\Http\Controllers\API;
use Razorpay\Api\Api;
use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Account;
use App\Models\Product;
use App\Models\Purchase;
use Modules\Shops\Entities\MealPlan;
use App\Models\LoanPayment;
use Modules\Shops\Entities\HotelMealPlan;
use Illuminate\Http\Request;
use App\Models\InvoiceReturn;
use App\Models\BalanceTansfer;
use App\Models\InvoicePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchasePayment;
use App\Models\NonInvoicePayment;
use App\Models\AccountTransaction;
use App\Models\NonPurchasePayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Artisan;
use App\Http\Resources\InvoiceListResource;
use App\Http\Resources\PurchaseListResource;
use App\Http\Resources\ProductListingResource;
use App\Http\Resources\AccountTransactionResource;
use App\Models\Employee;
use App\Models\Role;
use Modules\Shops\Transformers\CommonResource;
use Modules\Accounts\Entities\Ledger;
use Modules\Accounts\Entities\LedgerAccount;
use Modules\Shops\Entities\BookingDetails;
use Modules\Shops\Entities\Roomcategory;
use Modules\Shops\Entities\Room;

class DashboardController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:account-summery', ['only' => ['dashboardSummery', 'getTodayOrThisWeekSummery', 'getThisMonthSummery', 'getThisYearSummery']]);
        $this->middleware('can:top-selling-products', ['only' => ['topSellingProducts']]);
        $this->middleware('can:recent-activities', ['only' => ['recentInvoices', 'recentPurchases', 'recentExpenses', 'recentTransactions']]);
        $this->middleware('can:payment-sent-vs-payment-received', ['only' => ['monthlyPaymentSentAndReceived']]);
        $this->middleware('can:top-clients', ['only' => ['topClients']]);
        $this->middleware('can:stock-alert', ['only' => ['stockAlert']]);
        $this->middleware('can:sales-vs-purchases', ['only' => ['monthlySalesAndPurchases']]);
        $this->middleware('can:database-backup', ['only' => ['databaseBackup']]);
    }

    // return dashboard summery
    public function dashboardSummery($summeryType)
    {

        if ($summeryType == 'Today') {
            return $this->getTodaySummery();
        } elseif ($summeryType == 'Last 7 Days') {
            return $this->getThisWeekSummery();
        } elseif ($summeryType == 'This Month') {
            return $this->getThisMonthSummery();
        } elseif ($summeryType == 'This Year') {
            return $this->getThisYearSummery();
        } else {
            return 'Invalid Filter';
        }
    }
    public function getDaywiseExpenses(Request $request)
    {  
        $weekDates = []; 
        // Query to retrieve data for the current week, even if some dates have no data
        $from = Carbon::parse($request->startDate)->toDateString(); 
        $to = Carbon::parse($request->endDate)->toDateString(); 
        $currentDate = Carbon::parse($request->startDate);
        $todate = Carbon::parse($request->endDate); 
        while ($currentDate->lte($todate)) {
            $weekDates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }  
        // Query to retrieve data for the specified date range
        $data = DB::table('expenses')
            ->selectRaw('COALESCE(SUM(account_transactions.amount), 0) AS expAmount, DATE(expenses.date) AS date, expenses.status as status')
            ->leftJoin('account_transactions', 'account_transactions.id', '=', 'expenses.transaction_id')
            ->where('expenses.status', 1)
            ->whereDate('expenses.date', '>=', $from)
            ->whereDate('expenses.date', '<=', $to)
            ->groupBy('date')
            ->get();   
        // Create a new collection with a complete array of dates for the current week
        $total = 0;
        $completeData = collect($weekDates)->map(function ($date) use ($data,$total) {
            $filteredData = $data->where('date', $date)->first(); // Filter data for each date 
            return [ 
                'expAmount' => $filteredData ? $filteredData->expAmount : 0, // If data is null, set to 0
            ];
        });
        $vals = [];
        foreach($completeData as $val){ 
            $total +=  !empty($val['expAmount']) ? ($val['expAmount']) : 0;
            $vals[] = $val['expAmount'];
        } 
        return response()->json([
            'data'=>$vals,
            'total_exp'=>round($total,2),
        ]);

    }
    public function getDaywisePurchases(Request $request)
    {  
        $weekDates = []; 
        // Query to retrieve data for the current week, even if some dates have no data
        $from = Carbon::parse($request->startDate)->toDateString(); 
        $to = Carbon::parse($request->endDate)->toDateString(); 
        $currentDate = Carbon::parse($request->startDate);
        $todate = Carbon::parse($request->endDate); 
        while ($currentDate->lte($todate)) {
            $weekDates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }  
        // Query to retrieve data for the specified date range
        $data =  Purchase::where('status', 1)
            ->whereDate('po_date', '>=', $from)
            ->whereDate('po_date', '<=', $to) 
            ->get();   
        // Create a new collection with a complete array of dates for the current week
        $total = 0;
        $completeData = collect($weekDates)->map(function ($date) use ($data,$total) {
            $filteredData = $data->where('po_date', $date)->first(); // Filter data for each date 
            return [ 
                'sub_total' => $filteredData ? $filteredData->sub_total : 0, // If data is null, set to 0
            ];
        });
        $vals = [];
        foreach($completeData as $val){ 
            $total +=  !empty($val['sub_total']) ? ($val['sub_total']) : 0;
            $vals[] = $val['sub_total'];
        } 
        return response()->json([
            'data'=>$vals,
            'total_purchase'=>round($total,2),
        ]);

    }
    public function getDaywiseRevenue(Request $request)
    { 
        $weekDates = []; 
        // Query to retrieve data for the current week, even if some dates have no data
        $from = Carbon::parse($request->startDate)->toDateString(); 
        $to = Carbon::parse($request->endDate)->toDateString(); 
        $currentDate = Carbon::parse($request->startDate);
        $todate = Carbon::parse($request->endDate); 
        while ($currentDate->lte($todate)) {
            $weekDates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }   
        // Query to retrieve data for the current week, even if some dates have no data
        $data = Invoice::where('status', 1)->whereBetween('invoice_date', [$from, $to]) 
            ->get(); 
        // Create a new collection with a complete array of dates for the current week
        $completeData = collect($weekDates)->map(function ($date) use ($data) {
            $filteredData = $data->where('invoice_date', $date)->first(); // Filter data for each date.
            return [ 
                'sub_total' => $filteredData ? $filteredData->sub_total : 0, // If data is null, set to 0
            ];
        });
        $vals = [];
        $total = 0;
        foreach($completeData as $val){ 
            $vals[] =!empty($val['sub_total']) ? ($val['sub_total']) : 0;
            $total +=  !empty($val['sub_total']) ? ($val['sub_total']) : 0;
        } 
        return response()->json([
            'data'=>$vals,
            'total_rev'=>round($total,2),
        ]); 
    }


    public function getDaywiseSales(Request $request)
    { 
        $weekDates = []; 
        // Query to retrieve data for the current week, even if some dates have no data
        $from = Carbon::parse($request->startDate)->toDateString(); 
        $to = Carbon::parse($request->endDate)->toDateString(); 
        $currentDate = Carbon::parse($request->startDate);
        $todate = Carbon::parse($request->endDate); 
        while ($currentDate->lte($todate)) {
            $weekDates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }   
        // Query to retrieve data for the current week, even if some dates have no data
        $data = Invoice::where('status', 1)->whereBetween('invoice_date', [$from, $to]) 
            ->get();  
        // Create a new collection with a complete array of dates for the current week
        $completeData = collect($weekDates)->map(function ($date) use ($data) {
            $filteredData = $data->where('invoice_date', $date)->first(); // Filter data for each date.
            return [ 
                'sub_total' => $filteredData ? $filteredData->sub_total : 0, // If data is null, set to 0
            ];
        });
        $vals = [];
        $total = 0;
        foreach($completeData as $val){ 
            $vals[] = $val['sub_total'];
            $total +=  !empty($val['sub_total']) ? ($val['sub_total']) : 0;
        } 
        return response()->json([
            'data'=>$vals,
            'total_sale'=>round($total,2),
        ]); 
    }

    

    // return today
    public function getTodaySummery()
    {
        $date = Carbon::today();
        // payment received(Invoice + Noninvoice)
        
        //Start Get Data Available BankBalance 
        $total = 0;
        $bankTransactions = LedgerAccount::with('getAccoutnbalance')->where('is_bank', 1)->where('ledger_group', 1)->get();
        foreach ($bankTransactions as $transaction) {
            $total += $transaction->getAccoutnbalance->available_balance;
        }
        //End Get Data Available BankBalance
       
        // $transactions = AccountTransaction::whereDate('transaction_date', $date)->get();
        // $bankTransactions = $transactions->where('is_bank_transaction', 1)->where('type', 1)->sum('amount');

        // $accountTransactions = AccountTransaction::whereHas('cashbookAccount', function ($query) {
        //     $query->whereHas('ledgerAccount', function ($q) {
        //         $q->where('is_bank', 1);
        //         $q->where('ledger_group', 1);
        //     });
        // })->get();

        // dd($accountTransactions);

        $cashbook = AccountTransaction::where('account_id', 15)->sum('amount');
        // $bankTransactions = AccountTransaction::where('journal_entry_id', 2)->where('type', 1)->sum('amount');


        //        $invoicePayment = InvoicePayment::where('status', 1)->whereDate('date', $date)->sum('amount');
        //        $nonInvoicePayment = NonInvoicePayment::where('type', 1)->where('status', 1)->whereDate('date', $date)->sum('amount');

        // payment sent(Purchase + Nonpurhcase )
        $purchasePayment = PurchasePayment::where('status', 1)->whereDate('date', $date)->sum('amount');
        $nonPurchasePayment = NonPurchasePayment::where('status', 1)->whereDate('date', $date)->sum('amount');

        // expenses
        $expenses = Expense::select(DB::raw('SUM(account_transactions.amount) As expAmount'))
            ->leftJoin('account_transactions', 'account_transactions.id', '=', 'expenses.transaction_id')
            ->where('expenses.status', 1)
            ->whereDate('expenses.date', $date)
            ->get();

        return [
            'purchaseAmount' => Purchase::where('status', 1)->whereDate('purchase_date', $date)->get()->sum('calculated_total'),
            'purchaseReturnAmount' => PurchaseReturn::where('status', 1)->whereDate('date', $date)->sum('total_return'),
            'salesAmount' => Invoice::where('status', 1)->whereDate('invoice_date', $date)->with('invoiceTax')->get()->sum('calculated_total'),
            'salesReturnAmount' => InvoiceReturn::where('status', 1)->whereDate('date', $date)->sum('total_return'),
            'paymentSent' => $purchasePayment + $nonPurchasePayment,
            'expenseAmount' => round($expenses[0]->expAmount),
            'balanceTransfer' => BalanceTansfer::where('status', 1)->whereDate('date', $date)->sum('amount'),
            'cashbook' => $cashbook,
            'paymentReceived' => $total,
        ];
    }

    // return last 7 days query
    public function getThisWeekSummery()
    {
        $from = Carbon::now()->subDays(7);
        $to = Carbon::today()->addDay();

        // payment received(Invoice + Noninvoice)

        $transactions = AccountTransaction::whereBetween('transaction_date', [$from, $to])->get();

        $bankTransactions = $transactions->where('is_bank_transaction', 1)->where('type', 1)->sum('amount');
        // $bankTransactions = AccountTransaction::where('journal_entry_id', 2)->where('type', 1)->sum('amount');


        //        $invoicePayment = InvoicePayment::where('status', 1)->whereBetween('date', [$from, $to])->sum('amount');
        //        $nonInvoicePayment = NonInvoicePayment::where('type', 1)->where('status', 1)->whereBetween('date', [$from, $to])->sum('amount');

        // payment sent(Purchase + Nonpurhcase)
        $cashbook = AccountTransaction::where('account_id', 15)->sum('amount');
        $purchasePayment = PurchasePayment::where('status', 1)->whereBetween('date', [$from, $to])->sum('amount');
        $nonPurchasePayment = NonPurchasePayment::where('status', 1)->whereBetween('date', [$from, $to])->sum('amount');

        // expenses
        $expenses = Expense::select(DB::raw('SUM(account_transactions.amount) As expAmount'))
            ->leftJoin('account_transactions', 'account_transactions.id', '=', 'expenses.transaction_id')
            ->where('expenses.status', 1)
            ->whereBetween('expenses.date', [$from, $to])
            ->get();

        return [
            'purchaseAmount' => Purchase::where('status', 1)->whereBetween('purchase_date', [$from, $to])->get()->sum('calculated_total'),
            'purchaseReturnAmount' => PurchaseReturn::where('status', 1)->whereBetween('date', [$from, $to])->sum('total_return'),
            'salesAmount' => Invoice::where('status', 1)->whereBetween('invoice_date', [$from, $to])->with('invoiceTax')->get()->sum('calculated_total'),
            'salesReturnAmount' => InvoiceReturn::where('status', 1)->whereBetween('date', [$from, $to])->sum('total_return'),
            'paymentReceived' => $bankTransactions,
            'paymentSent' => $purchasePayment + $nonPurchasePayment,
            'expenseAmount' => round($expenses[0]->expAmount),
            'balanceTransfer' => BalanceTansfer::where('status', 1)->whereBetween('date', [$from, $to])->sum('amount'),
            'cashbook' => $cashbook,
        ];
    }

    // return this month summery
    public function getThisMonthSummery()
    {
        // payment received(Invoice + Noninvoice)
        $transactions = AccountTransaction::whereMonth('transaction_date', '=', date('m'))->whereYear('transaction_date', date('Y'))->get();
        $bankTransactions = $transactions->where('is_bank_transaction', 1)->where('type', 1)->sum('amount');

        //        $invoicePayment = InvoicePayment::where('status', 1)->whereMonth('date', '=', date('m'))->whereYear('date', date('Y'))->sum('amount');
        //        $nonInvoicePayment = NonInvoicePayment::where('type', 1)->whereMonth('date', '=', date('m'))->whereYear('date', date('Y'))->sum('amount');

        // payment sent(Purchase + Nonpurhcase + loan)
        $cashbook = AccountTransaction::where('account_id', 15)->sum('amount');
        $purchasePayment = PurchasePayment::where('status', 1)->whereMonth('date', '=', date('m'))->whereYear('date', date('Y'))->sum('amount');
        $nonPurchasePayment = NonPurchasePayment::where('status', 1)->whereMonth('date', '=', date('m'))->whereYear('date', date('Y'))->sum('amount');

        // expenses
        $expenses = Expense::select(DB::raw('SUM(account_transactions.amount) As expAmount'))
            ->leftJoin('account_transactions', 'account_transactions.id', '=', 'expenses.transaction_id')
            ->where('expenses.status', 1)
            ->whereMonth('expenses.date', '=', date('m'))->whereYear('expenses.date', date('Y'))
            ->get();

        return [
            'purchaseAmount' => Purchase::where('status', 1)->whereMonth('purchase_date', '=', date('m'))->whereYear('purchase_date', date('Y'))->get()->sum('calculated_total'),
            'purchaseReturnAmount' => PurchaseReturn::where('status', 1)->whereMonth('date', '=', date('m'))->whereYear('date', date('Y'))->sum('total_return'),
            'salesAmount' => Invoice::where('status', 1)->whereMonth('invoice_date', '=', date('m'))->whereYear('invoice_date', date('Y'))->with('invoiceTax')->get()->sum('calculated_total'),
            'salesReturnAmount' => InvoiceReturn::where('status', 1)->whereMonth('date', '=', date('m'))->whereYear('date', date('Y'))->sum('total_return'),
            'paymentReceived' => $bankTransactions,
            'paymentSent' => $purchasePayment + $nonPurchasePayment,
            'expenseAmount' => round($expenses[0]->expAmount),
            'balanceTransfer' => BalanceTansfer::where('status', 1)->whereMonth('date', '=', date('m'))->whereYear('date', date('Y'))->sum('amount'),
            'cashbook' => $cashbook,
        ];
    }

    // return this year summery
    public function getThisYearSummery()
    {
        // payment received(Invoice + Noninvoice)
        $transactions = AccountTransaction::whereYear('transaction_date', date('Y'))->get();
        $bankTransactions = $transactions->where('is_bank_transaction', 1)->where('type', 1)->sum('amount');

        //        $invoicePayment = InvoicePayment::where('status', 1)->whereYear('date', date('Y'))->sum('amount');
        //        $nonInvoicePayment = NonInvoicePayment::where('type', 1)->whereYear('date', date('Y'))->sum('amount');

        // payment sent(Purchase + Nonpurhcase)
        $cashbook = AccountTransaction::where('account_id', 15)->sum('amount');
        $purchasePayment = PurchasePayment::where('status', 1)->whereYear('date', date('Y'))->sum('amount');
        $nonPurchasePayment = NonPurchasePayment::where('status', 1)->whereYear('date', date('Y'))->sum('amount');

        // expenses
        $expenses = Expense::select(DB::raw('SUM(account_transactions.amount) As expAmount'))
            ->leftJoin('account_transactions', 'account_transactions.id', '=', 'expenses.transaction_id')
            ->where('expenses.status', 1)
            ->whereYear('expenses.date', date('Y'))
            ->get();

        return [
            'purchaseAmount' => Purchase::where('status', 1)->whereYear('purchase_date', date('Y'))->sum('sub_total'),
            'purchaseReturnAmount' => PurchaseReturn::where('status', 1)->whereYear('date', date('Y'))->sum('total_return'),
            'salesAmount' => Invoice::where('status', 1)->whereYear('invoice_date', date('Y'))->with('invoiceTax')->sum('sub_total'),
            'salesReturnAmount' => InvoiceReturn::where('status', 1)->whereYear('date', date('Y'))->sum('total_return'),
            'paymentReceived' => $bankTransactions,
            'paymentSent' => $purchasePayment + $nonPurchasePayment,
            'expenseAmount' => round($expenses[0]->expAmount),
            'balanceTransfer' => BalanceTansfer::where('status', 1)->whereYear('date', date('Y'))->sum('amount'),
            'cashbook' => $cashbook,
        ];
    }

    // return top selling products
    public function topSellingProducts()
    {
        $year = date('Y');
        if (Invoice::count() > 0) {
            $sales = DB::table('products')
                ->leftJoin('invoice_products', 'products.id', '=', 'invoice_products.product_id')
                ->selectRaw('COALESCE(sum(invoice_products.quantity),0) value, products.name')
                ->whereYear('invoice_products.created_at', '=', $year)
                ->groupBy('products.id')
                ->orderBy('value', 'desc')
                ->take(5)
                ->get();

            $productNames = [];
            foreach ($sales as $key => $item) {
                array_push($productNames, $item->name);
            }

            return [
                'names' => $productNames,
                'products' => $sales,
            ];
        }
    }

    // return sales
    public function recentInvoices()
    {
        return InvoiceListResource::collection(Invoice::with('client', 'invoiceTax', 'invoicePayments')->latest()->take(6)->get());
    }

    // return purchases
    public function recentPurchases()
    {
        return PurchaseListResource::collection(Purchase::with('supplier', 'purchasePayments', 'purchaseTaxes')->latest()->take(6)->get());
    }

    // return expenses
    public function recentExpenses()
    {
        return ExpenseResource::collection(Expense::with('expSubCategory.expCategory', 'expTransaction.cashbookAccount', 'user')->latest()->take(6)->get());
    }

    // return transactions
    public function recentTransactions()
    {
        $transactions = AccountTransaction::with('cashbookAccount', 'user')->latest()->take(6)->get();
        return AccountTransactionResource::collection($transactions);
    }

    // return monthly payment sent and received
    public function monthlyPaymentSentAndReceived()
    {
        $year = date('Y');
        $monthNum = date('m');
        $shortMonthNames = [];
        $sentByMonth = [];
        $receivedByMonth = [];

        while ($monthNum > 0) {
            // get the monthly payment sent amount
            $purchasePayment = PurchasePayment::where('status', 1)->whereYear('date', $year)->whereMonth('date', $monthNum)->sum('amount');
            $nonPurchasePayment = NonPurchasePayment::where('status', 1)->where('type', 1)->whereYear('date', $year)->whereMonth('date', $monthNum)->sum('amount');
            $termLoanPayment = LoanPayment::with('loan')->where('status', 1)->whereYear('date', $year)->whereMonth('date', $monthNum)->whereHas('loan', function ($newQuery) {
                $newQuery->where('loan_type', 1);
            })->sum('amount');
            $ccLoanPayment = LoanPayment::with('loan')->where('status', 1)->whereYear('date', $year)->whereMonth('date', $monthNum)->whereHas('loan', function ($newQuery) {
                $newQuery->where('loan_type', 0);
            })->sum(DB::raw('amount + interest'));
            $totalPaymentSent = $purchasePayment + $nonPurchasePayment + $termLoanPayment + $ccLoanPayment;

            // get the monthly received amount
            $invoicePayment = InvoicePayment::where('status', 1)->whereYear('date', $year)->whereMonth('date', $monthNum)->sum('amount');
            $nonInvoicePayment = NonInvoicePayment::where('type', 1)->where('status', 1)->whereYear('date', $year)->whereMonth('date', $monthNum)->sum('amount');
            $loanPayment = DB::table('account_transactions')
                ->leftJoin('loans', 'account_transactions.id', '=', 'loans.transaction_id')
                ->where('loans.status', 1)->whereYear('loans.date', $year)->whereMonth('loans.date', $monthNum)
                ->sum('account_transactions.amount');
            $totalPaymentSentReceived = $invoicePayment + $nonInvoicePayment + $loanPayment;

            // make the months array
            $dateObj = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('M');

            // push into array
            if ($totalPaymentSent > 0 || $totalPaymentSentReceived > 0) {
                array_push($sentByMonth, round($totalPaymentSent, 2));
                array_push($receivedByMonth, round($totalPaymentSentReceived, 2));
                array_push($shortMonthNames, $monthName);
            }
            $monthNum--;
        }

        return [
            'months' => array_reverse($shortMonthNames),
            'sent' => array_reverse($sentByMonth),
            'received' => array_reverse($receivedByMonth),
        ];
    }

    // return top clients
    public function topClients()
    {

        $year = date('Y');
        $topCustomers = Invoice::with('client')->whereYear('invoice_date', '=', $year)->addSelect(DB::raw('COUNT(invoices.id) as total_invoice'), DB::raw('SUM(sub_total) as invoice_total, client_id'))->groupBy('client_id')->take(5)->orderBy('invoice_total', 'DESC')->get();

        return $topCustomers;
    }

    // return stock alert products
    public function stockAlert()
    {
        return ProductResource::collection(Product::with('proSubCategory.category', 'productUnit', 'productTax', 'productBrand')->orderBy('inventory_count', 'ASC')->take(6)->get());
    }

    // return monthly sales and purchases
    public function monthlySalesAndPurchases()
    {
        $year = date('Y');
        $monthNum = date('m');
        $shortMonthNames = [];
        $purchaseByMonth = [];
        $salesByMonth = [];

        while ($monthNum > 0) {
            // get the monthly purchase amount
            $purchaseAmount = Purchase::where('status', 1)->whereYear('purchase_date', $year)->whereMonth('purchase_date', $monthNum)->with('purchaseReturn',  'purchaseTaxes')->get()->sum('calculated_total');

            // get the monthly sales amount
            $salesAmount = Invoice::where('status', 1)->whereYear('invoice_date', $year)->whereMonth('invoice_date', $monthNum)->with('invoiceReturn', 'invoiceTax')->get()->sum('calculated_total');

            // make the months array
            $dateObj = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('M');

            // push into array
            if ($purchaseAmount > 0 || $salesAmount > 0) {
                array_push($purchaseByMonth, round($purchaseAmount, 2));
                array_push($salesByMonth, round($salesAmount, 2));
                array_push($shortMonthNames, $monthName);
            }
            $monthNum--;
        }

        return [
            'months' => array_reverse($shortMonthNames),
            'purchase' => array_reverse($purchaseByMonth),
            'sales' => array_reverse($salesByMonth),
        ];
    }

    // get stock notification
    public function stockNotification()
    {
        return Product::whereRaw('alert_qty > inventory_count')->count();
    }

    // get products with stock alert
    public function stockAlertProducts()
    {
        return ProductListingResource::collection(Product::whereRaw('alert_qty > inventory_count')->with('proSubCategory.category', 'productUnit', 'productTax', 'productBrand')->latest()->paginate(10));
    }

    // get products with stock alert
    public function searchStockAlertProducts(Request $request)
    {
        $term = $request->term;

        $products = Product::where('alert_qty', '>', 'inventory_count')->with('proSubCategory.category', 'productUnit', 'productTax', 'productBrand')->where(function ($query) use ($term) {
            $query->where('name', 'LIKE', '%' . $term . '%')
                ->orWhere('slug', 'LIKE', '%' . $term . '%')
                ->orWhere('model', 'LIKE', '%' . $term . '%')
                ->orWhere('code', 'LIKE', '%' . $term . '%')
                ->orWhereHas('proSubCategory', function ($newQuery) use ($term) {
                    $newQuery->where('name', 'LIKE', '%' . $term . '%')
                        ->orWhereHas('category', function ($newQuery) use ($term) {
                            $newQuery->where('name', 'LIKE', '%' . $term . '%');
                        });
                });
        })->latest()->paginate(10);

        return ProductListingResource::collection($products);
    }

    // update user profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        // validate request
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|min:3|unique:users,email,' . $user->id,
            'currentPassword' => $request->newPassword != null ? ['required', 'string', 'min:8', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }] : 'nullable',

            'newPassword' => $request->currentPassword != null ? 'required|string|min:8|required_with:confirmPassword' : 'nullable',
            'confirmPassword' => $request->newPassword != null ? 'required|string|min:8|same:newPassword' : 'nullable',
        ]);

        try {

            $password = $user->password;
            if ($request->newPassword) {
                $password = bcrypt($request->newPassword);
            }


            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password,
            ]);

            return $this->responseWithSuccess('Profile updated successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    //Get Shop Available Balance

    public function shopBalance($id){

        $bankBalance = 0;
        $cashBalance = 0;
        $totalBalance = 0;

        // echo "<pre/>"; print_r($id); exit();
        $bankLedger = LedgerAccount::where('code', 'bank')->first();
        $cashLedger = LedgerAccount::where('code', 'CASH-0001')->first();

        $getBankLedgerBalance = AccountTransaction::where('account_id',$bankLedger->id)->where('shop_id',$id)->get();
        $getCashLedgerBalance = AccountTransaction::where('account_id',$cashLedger->id)->where('shop_id',$id)->get();

        foreach($getBankLedgerBalance as $value){
            if($value->type == 1){
                $bankBalance += $value->amount;
            } else {
                $bankBalance -= $value->amount;
            }
        }

        foreach($getCashLedgerBalance as $values){
            if($values->type == 1){
                $cashBalance += $values->amount;
            } else {
                $cashBalance -= $values->amount;
            }
        }

        $totalBalance = $cashBalance + $bankBalance;

        return response()->json([
            'total_balance'=>round($totalBalance,2),
            'bank'=> round($bankBalance,2),
            'cash'=> round($cashBalance,2),
        ]); 
    }

    // Get Shop Wise Sales Man

    public function shopSalesMan($id){
        
        $getSalesRole = Role::where('slug','sale')->first();

        $getSalesPerson = Employee::with('user')->where('shop_id',$id)->whereHas('user.roles', function ($newQuery) use ($getSalesRole) {
            $newQuery->where('role_id', $getSalesRole->id);
        })->get();

        return CommonResource::collection($getSalesPerson);
    }

    // Room Category & Availability

    public function roomCategoryAvailability(Request $request)
    {

        $checkInDate = date('Y-m-d');
        if ($request->check_in_date != '') {
            $checkInDate = $request->check_in_date;
        }

        $roomAvailability = array();
        $totalRoomCategory = Roomcategory::get();

        // $checkInDate = '24-02-2024';
        // $totalRoom = Room::where('room_categorary', 2)->where('hotel_id', 4)->pluck('id');
        // dd($totalRoom);
        // 
        // join('bookings','bookings.id','=','booking_details.booking_id')
        // $checkAvability = BookingDetails::whereIn('booking_details.room_id',$totalRoom)->whereIn('booking_details.booking_status',[1,4])->get();
        // ->where('bookings.check_out_date','<',$checkInDate)->whereNotIn('bookings.booking_status_main', [2,3])
        
        // dd($checkAvability);

        if (!empty($totalRoomCategory)) {
            foreach ($totalRoomCategory as $category) {
                if ($request->hotel_id != '') {
                    $totalRoom = Room::where('room_categorary', $category->id)->where('hotel_id', $request->hotel_id)->count();
                    $totalConfirm = Room::occupied($checkInDate)->where('room_categorary', $category->id)->where('hotel_id', $request->hotel_id)->count();
                } else {
                    $totalRoom = Room::where('room_categorary', $category->id)->count();
                    $totalConfirm = Room::occupied($checkInDate)->where('room_categorary', $category->id)->count();
                }
                $totalAvailable = $totalRoom - $totalConfirm;
                $hotel = Room::where('room_categorary', $category->id)->where('hotel_id', $request->hotel_id)->with('hotelRoomCategory','hotelRoomCategory.taxRate.taxName')->first();

                $allmealplans = MealPlan::get();
                $mealPlan_price = [];
                if(!empty($allmealplans)){
                    foreach($allmealplans as $plans){
                        $myprice = HotelMealPlan::where('meal_id', $plans->id)
                        ->where('hotel_id', $request->hotel_id)
                        ->select('id', 'price')
                        ->with([
                            'taxRate.taxName' => function ($query) {
                                $query->select('id', 'rate', 'name');
                            },
                        ])->first(); 
                        if(!empty($plans) && !empty($myprice)){
                            $myprice->plan_name = !empty($plans) ? $plans->plan_name : '';
                            $mealPlan_price[] = $myprice;

                        }
                    }
                } 
                // $hotel->mealPlan_price = $mealPlan_price; 
                $roomAvailability[] = array(
                    "cat_id" => $category->id,
                    'cat_name' => $category->room_category_name,
                    'total_room' => $totalRoom,
                    'total_available' => $totalAvailable,
                    'total_confirm' => $totalConfirm,
                    'total_hold' => 0,
                    'hotel' => $hotel,
                    'mealPlan_price' => $mealPlan_price,
                );
            }
        }
         
        return $roomAvailability;
    }

    // create database backup
    public function validatePayment(Request $request){
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET')); 
        $paymentDetail = $api->payment->fetch($request->payment_id)->toArray(); 
        if ($paymentDetail) { 
            return response()->json([
                'status' => 200,
                'data' => $paymentDetail,
            ]);
        } else { 
            return response()->json([
                'status' => 404,
                'message' => 'Payment detail not found',
                'data' => '',
            ], 404);
        }
    }


    public function databaseBackup(Request $request)
    {
        $fileName = env('DB_DATABASE') . '_' . Carbon::now()->getTimestamp() . '.' . $request->format;
        try {
            Artisan::call('database:backup', ['fileName' => $fileName]);
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
        $pathToFile = storage_path() . '//backup/' . $fileName;
        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/sql',
        ];

        return response()->download($pathToFile, $fileName, $headers);
    }
}
