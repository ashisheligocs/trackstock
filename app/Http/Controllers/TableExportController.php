<?php

namespace App\Http\Controllers;


// use Excel;
use App\Models\Loan;
use Illuminate\Support\Collection;
use Modules\Accounts\Transformers\{
    RevenueResource,
    ExpenseResource,
    AssetResource,
    LiabilityResource
};
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\VatRate;
use App\Models\Currency;
use App\Models\Employee;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\AssetType;
use App\Models\Quotation;
use App\Models\Department;
use App\Models\LoanPayment;
use App\Models\InvoiceReturn;
use App\Models\LoanAuthority;
use App\Models\PaymentMethod;
use App\Models\BalanceTansfer;
use App\Models\InvoicePayment;
use App\Models\PurchaseReturn;
use App\Models\ExpenseCategory;
use App\Models\ProductCategory;
use App\Models\PurchasePayment;
use App\Models\SalaryIncrement;
use Barryvdh\DomPDF\Facade\Pdf;
use spatie\SimpleExcel\SimpleExcelWriter;
use App\Http\Resources\PurchaseReturnListResource;
use Modules\Shops\Entities\Hotel;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HotelExport;
use App\Exports\ClientExport;
use App\Exports\PurchaseReturnList;
use App\Http\Resources\PurchaseListResource;
use App\Exports\InvoicesList;
use App\Exports\TranscationHistory;
use App\Exports\BankTransferList;
use App\Exports\BalanceSheet;
use Modules\Accounts\Entities\PlutusEntries;
use App\Http\Resources\InvoicePaymentResource;
use App\Http\Resources\AccountTransactionResource;
use App\Http\Resources\BalanceTransferResource;
use App\Exports\InvoicesPaymentExoprt;
use App\Exports\LedgerAccount;
// use Modules\Accounts\Entities\LedgerAccount;
use App\Exports\PurchaseList;
use App\Exports\ProductCategoryExport;
use App\Exports\ProductSubCategoryExport;
use App\Exports\ProductExport;
use App\Models\NonInvoicePayment;
use App\Models\AccountTransaction;
use App\Models\ExpenseSubCategory;
use App\Models\NonPurchasePayment;
use App\Models\ProductSubCategory;
use App\Models\InventoryAdjustment;
use Modules\Shops\Entities\Booking;
use App\Http\Resources\AdjustmentListResource;
use App\Exports\AdjustInventryList;

use Modules\Accounts\Entities\Ledger;
use Modules\Accounts\Entities\LedgerAccount as LedgerAccountModel;
use Modules\Accounts\Entities\LedgerCategory;


class TableExportController extends Controller
{
    // return all brands pdf
    public function brandsPDF()
    {
        // retrieve all records from db
        $data = Brand::latest()->get()->toArray();
        // share data to view
        view()->share('brands', $data);
        $pdf = PDF::loadView('pdf.brands', $data);
        // download PDF file with download method
        return $pdf->download('brands-list.pdf');
    }

    // return all currencies pdf
    public function currenciesPDF()
    {
        // retreive all records from db
        $data = Currency::latest()->get()->toArray();
        // share data to view
        view()->share('currencies', $data);
        $pdf = PDF::loadView('pdf.currencies', $data);
        // download PDF file with download method
        return $pdf->download('currencies-list.pdf');
    }

    // return all units pdf
    public function unitsPDF()
    {
        // retreive all records from db
        $data = Unit::latest()->get()->toArray();
        // share data to view
        view()->share('units', $data);
        $pdf = PDF::loadView('pdf.units', $data);
        // download PDF file with download method
        return $pdf->download('units-list.pdf');
    }

    // return all vat rates pdf
    public function vatRatesPDF()
    {
        // retreive all records from db
        $data = VatRate::latest()->get()->toArray();
        // share data to view
        view()->share('vatRates', $data);
        $pdf = PDF::loadView('pdf.vat-rates', $data);
        // download PDF file with download method
        return $pdf->download('vat-rates-list.pdf');
    }

    // return all roles pdf
    public function rolesPDF()
    {
        // retreive all records from db
        $data = Role::latest()->get()->toArray();
        // share data to view
        view()->share('roles', $data);
        $pdf = PDF::loadView('pdf.roles', $data);
        // download PDF file with download method
        return $pdf->download('roles-list.pdf');
    }

    // return all payment methods pdf
    public function paymentMethodsPDF()
    {
        // retreive all records from db
        $data = PaymentMethod::latest()->get()->toArray();
        // share data to view
        view()->share('paymentMethods', $data);
        $pdf = PDF::loadView('pdf.payment-methods', $data);
        // download PDF file with download method
        return $pdf->download('payment-methods-list.pdf');
    }

    // return expense category pdf
    public function expCategoriesPDF()
    {
        // retreive all records from db
        $data = ExpenseCategory::latest()->get()->toArray();
        // share data to view
        view()->share('categories', $data);
        $pdf = PDF::loadView('pdf.exp-categories', $data);
        // download PDF file with download method
        return $pdf->download('exp-categories-list.pdf');
    }

    // return expense sub category pdf
    public function expSubCategoriesPDF()
    {
        // retreive all records from db
        $data = ExpenseSubCategory::with('expCategory')->latest()->get()->toArray();
        // share data to view
        view()->share('categories', $data);
        $pdf = PDF::loadView('pdf.exp-sub-categories', $data);
        // download PDF file with download method
        return $pdf->download('exp-sub-categories-list.pdf');
    }

    // return expense pdf
    public function expensesPDF()
    {
        // retreive all records from db
        $data = Expense::with('expSubCategory.expCategory', 'expTransaction.cashbookAccount')->latest()->get()->toArray();
        // share data to view
        view()->share('expenses', $data);
        $pdf = PDF::loadView('pdf.expenses', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('expenses-list.pdf');
    }

    // return purchases pdf
    // public function purchasesPDF()
    // {
    //     // retreive all records from db
    //     $data = Purchase::with('supplier')->latest()->get()->toArray();
    //     // share data to view
    //     view()->share('purchases', $data);
    //     $pdf = PDF::loadView('pdf.purchases', $data)->setPaper('a4', 'landscape');
    //     // download PDF file with download method
    //     return $pdf->download('purchases-list.pdf');
    // }

    public function purchasesExcel()
    {
        $data = PurchaseListResource::collection(Purchase::with('supplier', 'purchasePayments', 'purchaseTaxes', 'hotel')->latest()->get());
        // dd($data);
        if ($data->isNotEmpty()) {
            // Create a new Excel file
            $excelFileName = 'purchases-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new PurchaseList($data), "local/{$excelFileName}");

            // Return a response with a link to the stored Excel file
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }


    public function BalanceSheetExcel(Request $request){
        
        //GET Assest Data

        $assest = LedgerAccountModel::with(['ledger', 'ledgerCategory', 
                        'getAccoutnbalance'   => function ($q) use ($request) {
                            $q->whereHas('balanceTransactions', function ($query) use ($request) {
                                $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                            });
                        },
                        'balanceTransactions' => function ($query) use ($request) {
                            $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
                        },
                    ])
                    ->where('ledger_type', 1)
                    ->get();
         
        $assestData = $assest->groupBy(function ($item) {
            return $item->ledgerCategory->name;
        })->map(function ($items, $categoryName) {
            $totalAmount = $items->sum(function ($item) {
                return optional($item->getAccoutnbalance)->available_balance ?? 0;
            });
    
            $filteredItems = $items->filter(function ($item) {
                return optional($item->getAccoutnbalance)->available_balance && $item->getAccoutnbalance->available_balance !== 0;
            });
    
            return [
                'group_name' => $categoryName,
                'total_amount' => $totalAmount,
                'items' => $filteredItems->map(function ($item) {
                    return [
                        'name' => $item->ledger_name,
                        'amount' => optional($item->getAccoutnbalance)->available_balance ?? 0,
                    ];
                })->toArray(),
            ];
        })->values();
        
       //GET Liability Data   
        $totalRevenue = $this->ledgerRevenue($request);
        $totalExpense = $this->ledgerExpense($request); 
       
        $profitLossArray = [
            [
                "group_name" => "Profit & Loss A/c",
                "total_amount" => $totalRevenue - $totalExpense,
                "items" => [],
            ]
        ];

        $ledgerType = [2,3];

        $liability = LedgerAccountModel::with(['ledger', 'ledgerCategory',
        'getAccoutnbalance'   => function ($q) use ($request) {
            $q->whereHas('balanceTransactions', function ($query) use ($request) {
                $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
            });
        },
        'balanceTransactions' => function ($query) use ($request) {
            $query->whereBetween('transaction_date', [$request->fromDate, $request->toDate]);
        },
        ])->whereIn('ledger_type', $ledgerType)->get();

        $liabilityData = $liability->groupBy(function ($item) {
            return $item->ledgerCategory->name;
        })->map(function ($items, $categoryName) {
            
            if ($categoryName == "Current Liabilities") {
                $convertArray = $items->toArray();
                $key = array_search('GST-INPUT', array_column($convertArray, 'code'));
                if ($key !== false) {
                    if (@$items[$key]->getAccoutnbalance && $items[$key]->getAccoutnbalance->available_balance !== null) {
                        $totalAmount = $items->sum(function ($item) {
                            return optional($item->getAccoutnbalance)->available_balance ?? 0;
                        }) - ($items[$key]->getAccoutnbalance->available_balance * 2);
                    } else {
                        $totalAmount = $items->sum(function ($item) {
                            return optional($item->getAccoutnbalance)->available_balance ?? 0;
                        });
                    }
                } else {
                    $totalAmount = $items->sum(function ($item) {
                        return optional($item->getAccoutnbalance)->available_balance ?? 0;
                    });
                }
            } else {
                $totalAmount = $items->sum(function ($item) {
                    return optional($item->getAccoutnbalance)->available_balance ?? 0;
                });
            }
    
            $filteredItems = $items->filter(function ($item) {
                return optional($item->getAccoutnbalance)->available_balance && $item->getAccoutnbalance->available_balance !== 0;
            });
            return [
                'group_name' => $categoryName,
                'total_amount' => $totalAmount,
                'items' => $filteredItems->map(function ($item) {
                    return [
                        'name' => $item->ledger_name,
                        'amount' => optional($item->getAccoutnbalance)->available_balance ?? 0,
                    ];
                })->toArray(),
            ];
        })->values();

        $mergedArray = array_merge($liabilityData->toArray(), $profitLossArray);

        // Loop through the merged array and adjust the structure
        $liabilitiesArray = array_map(function ($item) {
            return [
                'group_name' => $item['group_name'] ?? '',
                'total_amount' => $item['total_amount'] ?? 0,
                'items' => $item['items'] ?? [],
            ];
        }, $mergedArray);

        $finalArray = [
            'assest' => $assestData->toArray(),
            'liability' => $liabilitiesArray,
        ];

        $excelFileName = 'balance-sheet-list.xlsx';

        Excel::store(new BalanceSheet($finalArray), "local/{$excelFileName}");

            // Return a response with a link to the stored Excel file
        return response()->download(storage_path("app/local/{$excelFileName}"));
    }

    public function ledgerRevenue($request){
        $revenue = LedgerAccountModel::with(['ledger', 'ledgerCategory',
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

        return $totalRevenue;
    }

    public function ledgerExpense($request){
        $expense = LedgerAccountModel::with(['ledger', 'ledgerCategory', 
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

        return $totalExpense;
    }

    // return purchase returns pdf
    // public function purchaseReturnsPDF()
    // {
    //     // retreive all records from db
    //     $data = PurchaseReturn::with('purchase.supplier')->latest()->get()->toArray();
    //     // share data to view
    //     view()->share('returns', $data);
    //     $pdf = PDF::loadView('pdf.purchase-returns', $data)->setPaper('a4', 'landscape');
    //     // download PDF file with download method
    //     return $pdf->download('purchase-returns-list.pdf');
    // }

    public function purchaseReturnsExcel()
    {
        $data = PurchaseReturnListResource::collection(PurchaseReturn::with('purchase.supplier')->latest()->get());
        // dd($data);
        if ($data->isNotEmpty()) {
            // Create a new Excel file
            $excelFileName = 'PurchaseReturn-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new PurchaseReturnList($data), "local/{$excelFileName}");

            // Return a response with a link to the stored Excel file
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }


    // return quotation pdf
    public function quotationsPDF()
    {
        // retreive all records from db
        $data = Quotation::with('client')->latest()->get()->toArray();
        // share data to view
        view()->share('quotations', $data);
        $pdf = PDF::loadView('pdf.quotations', $data);
        // download PDF file with download method
        return $pdf->download('quotation-list.pdf');
    }

    // return invoice pdf
    public function invoicePDF()
    {
        // retreive all records from db
        $data = Invoice::with('client', 'invoicePayments')->latest()->get()->toArray();
        // share data to view
        view()->share('invoices', $data);
        $pdf = PDF::loadView('pdf.invoices', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('invoice-list.pdf');
    }

    public function invoiceExcel()
    { {
            // Get the data from the database
            $data = Invoice::with('client', 'invoiceTax', 'invoicePayments')->latest()->get();
            $data->each(function ($invoice) {
                $invoice->taxAmount = $invoice->taxAmount();
            });
            $data->each(function ($invoice) {
                $invoice->NetTotal = $invoice->invoiceTotal();
            });
            $data->each(function ($invoice) {
                $invoice->invoiceTotalPaid = $invoice->invoiceTotalPaid();
            });
            $data->each(function ($invoice) {
                $invoice->totalDue = $invoice->totalDue();
            });


            if ($data->isNotEmpty()) {
                // Create a new Excel file
                $excelFileName = 'Invoice-list.xlsx';

                // Use the store method with the export class to generate Excel file
                Excel::store(new InvoicesList($data), "local/{$excelFileName}");

                // Return a response with a link to the stored Excel file
                return response()->download(storage_path("app/local/{$excelFileName}"));
            } else {
                // Handle the case where there is no data
                return response()->json(['message' => 'No data to export'], 404);
            }
        }
    }

    public function invoiceReturnPDF()
    {
        // retreive all records from db
        $data = InvoiceReturn::with('invoice.client')->latest()->get()->toArray();
        // share data to view
        view()->share('returns', $data);
        $pdf = PDF::loadView('pdf.invoice-returns', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('invoice-return-list.pdf');
    }

    // return accounts pdf
    public function accountsPDF()
    {
        // retreive all records from db
        $data = Account::with('balanceTransactions')->latest()->get()->toArray();
        // share data to view
        view()->share('accounts', $data);
        $pdf = PDF::loadView('pdf.accounts', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('account-list.pdf');
    }

    // return account transaction pdf
    
    public function accountTransactionsExcel($slug)
    {
        $account = Account::where('slug', $slug)->first();
        $data = AccountTransactionResource::collection(AccountTransaction::with('user', 'cashbookAccount', 'customer', 'purchase')->where('account_id', $account->id)->orderBy('created_at', 'asc')->latest()->get());
    
        $parts = explode('-', $slug);
        $result = $parts[0];
        $excelFileName = $result;
       
        if ($data->isNotEmpty()) {
            Excel::store(new LedgerAccount($data), "local/{$excelFileName}.xlsx");
    
            return response()->download(storage_path("app/local/{$excelFileName}.xlsx"));
        } else {
            return response()->json(['message' => 'No data to export'], 404);
        }
    }
    

    // return non invoice add balances pdf
    public function nonInvoiceBalancesPDF()
    {
        $data = AccountTransaction::with('cashbookAccount')->where('reason', 'LIKE', 'Non invoice balance added%')->latest()->get()->toArray();
        // share data to view
        view()->share('balances', $data);
        $pdf = PDF::loadView('pdf.non-invoice-balances', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('account-transaction-list.pdf');
    }

    // return transfer balances pdf
    // public function transferBalancesPDF()
    // {
    //     $data = BalanceTansfer::with('debitTransaction.cashbookAccount', 'creditTransaction.cashbookAccount', 'user')->latest()->get()->toArray();
    //     // share data to view
    //     view()->share('transfers', $data);
    //     $pdf = PDF::loadView('pdf.transfer-balances', $data)->setPaper('a4', 'landscape');
    //     // download PDF file with download method
    //     return $pdf->download('balance-transfer-list.pdf');
    // }


    // AccountTransaction
    // return transactions PDF
    public function transactionsPDF()
    {
        // $data = AccountTransaction::latest()->get();

        // if ($data->isNotEmpty()) {
        //     // Create a new Excel file
        //     $excelFileName = 'AccountTransaction-list.xlsx';

        //     // Use the store method with the export class to generate Excel file
        //     Excel::store(new ClientExport($data), "local/{$excelFileName}");

        //     // Return a response with a link to the stored Excel file
        //     return response()->download(storage_path("app/local/{$excelFileName}"));
        // } else {
        //     // Handle the case where there is no data
        //     return response()->json(['message' => 'No data to export'], 404);
        // }


        $data = AccountTransaction::with('cashbookAccount', 'user')->latest()->get()->toArray();
        // share data to view
        view()->share('transactions', $data);
        $pdf = PDF::loadView('pdf.all-transactions', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('transaction-list.pdf');
    }

    // return client non invoice payment pdf   NonInvoicePayment
    public function invoicePaymentsexcel()
    {
        $data = InvoicePaymentResource::collection(InvoicePayment::with('invoice', 'invoice.invoiceTax', 'invoicePaymentTransaction.cashbookAccount', 'user')->latest()->get());

        if ($data->isNotEmpty()) {
            // Create a new Excel file
            $excelFileName = 'invoicePayment-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new InvoicesPaymentExoprt($data), "local/{$excelFileName}");

            // Return a response with a link to the stored Excel file
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }

    // return client invoice payment pdf
    public function invoicePaymentsPDF()
    {
        $data = InvoicePayment::with('invoice.client', 'invoicePaymentTransaction.cashbookAccount', 'user')->latest()->get()->toArray();
        // share data to view
        view()->share('payments', $data);
        $pdf = PDF::loadView('pdf.invoice-payments', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('invoice-payment-list.pdf');
    }

    // return supplier non purchase payment pdf
    public function nonPurchasePaymentsPDF()
    {
        $data = NonPurchasePayment::with('supplier', 'paymentTransaction.cashbookAccount')->latest()->get()->toArray();
        // share data to view
        view()->share('payments', $data);
        $pdf = PDF::loadView('pdf.non-purchase-payments', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('non-purchase-payment-list.pdf');
    }

    // return supplier purchase payment pdf
    public function purchasePaymentsPDF()
    {
        $data = PurchasePayment::with('purchase.supplier', 'purchasePaymentTransaction.cashbookAccount', 'user')->latest()->get()->toArray();
        // share data to view
        view()->share('payments', $data);
        $pdf = PDF::loadView('pdf.purchase-payments', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('purchase-payments-list.pdf');
    }

    // return loan authorities pdf
    public function loanAuthoritiesPDF()
    {
        // retreive all records from db
        $data = LoanAuthority::latest()->get()->toArray();
        // share data to view
        view()->share('loanAuthorities', $data);
        $pdf = PDF::loadView('pdf.loan-authorities', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('loan-authority-list.pdf');
    }

    // return loans pdf
    public function loansPDF()
    {
        // retreive all records from db
        $data = Loan::with('loanAuthority', 'loanPayments', 'loanTransaction.cashbookAccount')->latest()->get();
        // share data to view
        view()->share('loans', $data);
        $pdf = PDF::loadView('pdf.loans', compact('data'))->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('loan-list.pdf');
    }

    // return loan payments pdf
    public function loanPaymentsPDF()
    {
        // retreive all records from db
        $data = LoanPayment::with('loan.loanAuthority', 'loanPaymentTransaction.cashbookAccount')->latest()->get();
        // share data to view
        view()->share('loanPayments', $data);
        $pdf = PDF::loadView('pdf.loan-payments', compact('data'))->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('loan-payment-list.pdf');
    }

    // return asset types pdf
    public function assetTypesPDF()
    {
        // retreive all records from db
        $data = AssetType::latest()->get()->toArray();
        // share data to view
        view()->share('assetTypes', $data);
        $pdf = PDF::loadView('pdf.asset-types', $data);
        // download PDF file with download method
        return $pdf->download('asset-type-list.pdf');
    }

    // return assets pdf
    public function assetsPDF()
    {
        // retreive all records from db
        $data = Asset::with('assetType')->latest()->get()->toArray();
        // share data to view
        view()->share('assets', $data);
        $pdf = PDF::loadView('pdf.assets', $data);
        // download PDF file with download method
        return $pdf->download('asset-list.pdf');
    }

    // return payroll pdf
    public function payrollPDF()
    {
        // retreive all records from db
        $data = Payroll::with('employee.department', 'payrollTransaction.cashbookAccount')->latest()->get()->toArray();
        // share data to view
        view()->share('allPayroll', $data);
        $pdf = PDF::loadView('pdf.payroll', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('payroll-list.pdf');
    }

    // return clients pdf
    public function clientsPDF()
    {
        // retreive all records from db
        $data = Client::latest()->get()->toArray();
        // share data to view
        view()->share('clients', $data);
        $pdf = PDF::loadView('pdf.clients', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('client-list.pdf');
    }

    // public function clients_excel()
    // {
    //     // retreive all records from db
    //     $data = Client::latest()->get()->toArray();
    //     // share data to view
    //     view()->share('clients', $data);
    //     $excel = Excel::store('excel.clients', $data);
    //     // download PDF file with download method
    //     return $excel->download('client-list.pdf');
    // }





    public function hotel_excel()
    { {
            // Get the data from the database
            $data = Hotel::latest()->get();
            $newdata = [];
            $allnewdata = [];
            if ($data->isNotEmpty()) {
                // Create a new Excel file
                $excelFileName = 'shops-list.xlsx';
                foreach ($data as $det) {
                    $newdata['image'] = !empty($det->image_path) ? 'images/hotel/' . $det->image_path : '';
                    $newdata['name'] = $det->hotel_name ?? '';
                    $newdata['email'] = $det->hotel_email ?? '';
                    $newdata['phone'] = $det->contact_phone ?? '';
                    $newdata['rooms'] = $det->total_no_of_rooms ?? '';
                    $allnewdata[] = $newdata;
                }
                // Use the store method with the export class to generate Excel file
                Excel::store($allnewdata, "local/{$excelFileName}");

                // Return a response with a link to the stored Excel file
                return response()->download(storage_path("app/local/{$excelFileName}"));
            } else {
                // Handle the case where there is no data
                return response()->json(['message' => 'No data to export'], 404);
            }
        }
    }



    public function clients_excel()
    { {
            // Get the data from the database
            $data = Client::latest()->get();

            if ($data->isNotEmpty()) {
                // Create a new Excel file
                $excelFileName = 'client-list.xlsx';

                // Use the store method with the export class to generate Excel file
                Excel::store(new ClientExport($data), "local/{$excelFileName}");

                // Return a response with a link to the stored Excel file
                return response()->download(storage_path("app/local/{$excelFileName}"));
            } else {
                // Handle the case where there is no data
                return response()->json(['message' => 'No data to export'], 404);
            }
        }


        // public function productsExcel()
        // {

        //     // Get the data from the database
        //     $data = Product::with(
        //         'proSubCategory.category',
        //         'productUnit',
        //         'productTax',
        //         'productBrand'
        //     )->latest()->get();

        //     if ($data->isNotEmpty()) {
        //         $excelFileName = 'product-list.xlsx';

        //         // Use the store method with the export class to generate Excel file
        //         Excel::store(new ProductExport($data), "local/{$excelFileName}");

        //         // Return a response with a link to the stored Excel file
        //         return response()->download(storage_path("app/local/{$excelFileName}"));
        //     } else {
        //         // Handle the case where there is no data
        //         return response()->json(['message' => 'No data to export'], 404);
        //     }
        // }
    }


    // return suppliers pdf
    public function suppliersPDF()
    {
        // retreive all records from db
        $data = Supplier::latest()->get()->toArray();
        // share data to view   
        view()->share('suppliers', $data);
        $pdf = PDF::loadView('pdf.suppliers', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('supplier-list.pdf');
    }

    // return departments pdf
    public function departmentsPDF()
    {
        // retreive all records from db
        $data = Department::latest()->get()->toArray();
        // share data to view
        view()->share('departments', $data);
        $pdf = PDF::loadView('pdf.departments', $data);
        // download PDF file with download method
        return $pdf->download('department-list.pdf');
    }

    // return employees pdf
    public function employeesPDF()
    {
        // retreive all records from db
        $data = Employee::with('department', 'user')->latest()->get()->toArray();
        // share data to view
        view()->share('employees', $data);
        $pdf = PDF::loadView('pdf.employees', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('employee-list.pdf');
    }

    // return increments pdf
    public function incrementsPDF()
    {
        // retreive all records from db
        $data = SalaryIncrement::with('employee.department')->latest()->get()->toArray();
        // share data to view
        view()->share('salIncrements', $data);
        $pdf = PDF::loadView('pdf.increments', $data)->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('increment-list.pdf');
    }

    // return product categories pdf
    public function productCategoriesPDF()
    {
        // retreive all records from db
        $data = ProductCategory::latest()->get()->toArray();
        // share data to view
        view()->share('productCategories', $data);
        $pdf = PDF::loadView('pdf.product-categories', $data);
        // download PDF file with download method
        return $pdf->download('product-category-list.pdf');
    }

    // return product sub categories pdf
    public function productSubCategoriesPDF()
    {
        // retreive all records from db
        $data = ProductSubCategory::with('category')->latest()->get()->toArray();
        // share data to view
        view()->share('productsubCategories', $data);
        $pdf = PDF::loadView('pdf.product-sub-categories', $data);
        // download PDF file with download method
        return $pdf->download('product-sub-category-list.pdf');
    }

    // return product sub categories pdf
    public function productsPDF()
    {
        // retreive all records from db
        $data = Product::with('proSubCategory.category', 'productUnit')->latest()->get();
        // share data to view
        view()->share('products', $data);
        $pdf = PDF::loadView('pdf.products', compact('data'))->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->download('product-list.pdf');
    }

    // return inventoryAdjustments PDF
    // public function inventoryAdjustmentsPDF()
    // {
    //     // retreive all records from db
    //     $data = InventoryAdjustment::latest()->get();
    //     // share data to view
    //     view()->share('adjustments', $data);
    //     $pdf = PDF::loadView('pdf.adjustments', compact('data'))->setPaper('a4', 'landscape');
    //     // download PDF file with download method
    //     return $pdf->download('inventory-adjustment-list.pdf');
    // }


    public function inventoryAdjustmentsExcel()
    {
        $data = AdjustmentListResource::collection(InventoryAdjustment::latest()->get());

        if ($data->isNotEmpty()) {
            // Create a new Excel file
            $excelFileName = 'InventoryAdjustmentsExcel-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new AdjustInventryList($data), "local/{$excelFileName}");


            // Return a response with a link to the stored Excel file
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }
    // return non zero inventory products
    public function nonZeroInventoryPDF()
    {
        // retreive all records from db
        $data = [];
        Product::where('inventory_count', '>', 0)
            ->with('proSubCategory.category', 'productUnit')
            ->orderBy('code', 'ASC')
            ->chunk(200, function ($products) use (&$data) {
                foreach ($products as $product) {
                    $data[] = $product;
                }
            });

        // share data to view
        view()->share('products', $data);
        $pdf = PDF::loadView('pdf.non-zero-inventory', compact('data'))->setPaper('a4', 'landscape');
        // download PDF file with download method

        return $pdf->stream('non-zero-inventory-list.pdf');
    }

    // return inventoryAdjustments PDF
    public function bookedHotelPDF($id)
    {
        // retreive all records from db
        $data = Booking::where("id", $id)->with('Customer', 'Source', 'Hotel', 'BookingDetails.room', 'BookingDetails.mealPlanDetails.mealname', 'BookingDetails.room.Roomcategory', 'BookingDetails.room.Bed', 'BookingDetails.complementrays.paidFacility')->first();

        // share data to view
        view()->share('booking', $data);
        $pdf = PDF::loadView('pdf.booking', compact('data'))->setPaper('a4', 'landscape');
        // download PDF file with download method
        return $pdf->stream('booking.pdf');
        return $pdf->download('booking.pdf');
    }

    // Product Excel

    public function productsExcel()
    {

        // Get the data from the database
        $data = Product::with(
            'proSubCategory.category',
            'productUnit',
            'productTax',
            'productBrand'
        )->latest()->get();

        if ($data->isNotEmpty()) {
            // Create a new Excel file
            $excelFileName = 'product-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new ProductExport($data), "local/{$excelFileName}");

            // Return a response with a link to the stored Excel file
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }

    // Product Category Excel
    public function TransferBalancesExcel()
    {
        $data = BalanceTransferResource::collection(BalanceTansfer::with('debitTransaction.cashbookAccount', 'creditTransaction.cashbookAccount', 'user')->latest()->get());
        if ($data->isNotEmpty()) {
            $excelFileName = 'BankTransfer-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new BankTransferList($data), "local/{$excelFileName}");

            // Return a response with a link to the stored Excel file
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }

    public function TransactionExcel()
    {
        $data = PlutusEntries::with(
            'hotel',
            'balanceTransactions.ledgerAccount.ledgerCategory',
            'balanceTransactions.ledgerAccount.ledger',
            'balanceTransactions.user'
        )->latest()->get();

        $setTableData = collect();

        $data->each(function ($plutusEntry) use ($setTableData) {
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
                        'hotel' => $transaction->hotel->hotel_name,
                        'debit' => ($type == 0) ? $transaction->amount : 0,
                        'credit' => ($type == 1) ? $transaction->amount : 0,
                        'date' => $transaction->transaction_date,
                        'user_name' => $transaction->user->name,
                    ];
                }),
            ]);
        });

        if ($data->isNotEmpty()) {
            $excelFileName = 'Transaction-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new TranscationHistory($setTableData), "local/{$excelFileName}");
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }
    //     public function TransactionExcel(){
//         $data = AccountTransactionResource::collection(AccountTransaction::with('cashbookAccount', 'user')->latest()->get());

    //         if ($data->isNotEmpty()) {
//             $excelFileName = 'Transcation-list.xlsx';

    //             // Use the store method with the export class to generate Excel file
//             Excel::store(new TranscationHistory($data), "local/{$excelFileName}");

    //             // Return a response with a link to the stored Excel file
//             return response()->download(storage_path("app/local/{$excelFileName}"));
//         } else {
//             // Handle the case where there is no data
//             return response()->json(['message' => 'No data to export'], 404);
//         }
//     }
    public function productsCategoriesExcel()
    {

        // Get the data from the database
        $data = ProductCategory::latest()->get();

        if ($data->isNotEmpty()) {
            // Create a new Excel file
            $excelFileName = 'product-category-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new ProductCategoryExport($data), "local/{$excelFileName}");

            // Return a response with a link to the stored Excel file
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }

    // Product Sub Category Excel

    public function productSubCategoriesExcel()
    {

        // Get the data from the database
        $data = ProductSubCategory::with('category')->latest()->get();

        if ($data->isNotEmpty()) {
            // Create a new Excel file
            $excelFileName = 'product-sub-category-list.xlsx';

            // Use the store method with the export class to generate Excel file
            Excel::store(new ProductSubCategoryExport($data), "local/{$excelFileName}");

            // Return a response with a link to the stored Excel file
            return response()->download(storage_path("app/local/{$excelFileName}"));
        } else {
            // Handle the case where there is no data
            return response()->json(['message' => 'No data to export'], 404);
        }
    }

}


