<?php

use App\Http\Controllers\SpaController;
use App\Http\Controllers\TableExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SPA Routes
|--------------------------------------------------------------------------
|
| Here is where you can register SPA routes for your frontend. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "spa" middleware group.
|
*/
http://127.0.0.1:8000/api/accounts/transactions/cash-0001?from=2024-02-28&to=2024-03-06%2023:59:59.999&perPage=10

// pdf routes
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/setup/brands/pdf', [TableExportController::class, 'brandsPDF'])->name('brands.pdf');
    Route::get('/setup/currencies/pdf', [TableExportController::class, 'currenciesPDF'])->name('currencies.pdf');
    Route::get('/setup/units/pdf', [TableExportController::class, 'unitsPDF'])->name('units.pdf');
    Route::get('/setup/vat-rates/pdf', [TableExportController::class, 'vatRatesPDF'])->name('vatRates.pdf');
    Route::get('/setup/roles/pdf', [TableExportController::class, 'rolesPDF'])->name('roles.pdf');
    Route::get('/setup/payment-methods/pdf', [TableExportController::class, 'paymentMethodsPDF'])->name('paymentMethods.pdf');

    Route::get('/expense-categories/pdf', [TableExportController::class, 'expCategoriesPDF'])->name('expCategories.pdf');
    Route::get('/expense-sub-categories/pdf', [TableExportController::class, 'expSubCategoriesPDF'])->name('expSubCategories.pdf');
    Route::get('/expenses/pdf', [TableExportController::class, 'expensesPDF'])->name('expenses.pdf');

    // Route::get('/purchases/pdf', [TableExportController::class, 'purchasesPDF'])->name('purchases.pdf');
        Route::get('/purchases/excel', [TableExportController::class, 'purchasesExcel'])->name('purchases.pdf');
    Route::get('/purchase-returns/excel', [TableExportController::class, 'purchaseReturnsExcel'])->name('purchaseReturns.pdf');

    Route::get('/quotations/pdf', [TableExportController::class, 'quotationsPDF'])->name('quotations.pdf');
    // Route::get('/invoices/pdf', [TableExportController::class, 'invoicePDF'])->name('invoices.pdf');
    Route::get('/invoices/excel', [TableExportController::class, 'invoiceExcel'])->name('invoices.excel');
    Route::get('/invoice-returns/pdf', [TableExportController::class, 'invoiceReturnPDF'])->name('invoiceReturns.pdf');

    Route::get('/accounts/pdf', [TableExportController::class, 'accountsPDF'])->name('accounts.pdf');
    Route::get('/account-transactions/excel/{slug}', [TableExportController::class, 'accountTransactionsExcel'])->name('account.transactions.pdf');
    Route::get('/cashbook/balance-adjustments/pdf', [TableExportController::class, 'nonInvoiceBalancesPDF'])->name('account.balances.pdf');
    // Route::get('/cashbook/transfer-balances/pdf', [TableExportController::class, 'transferBalancesPDF'])->name('account.transferBalances.pdf');
    // Route for excel
    Route::get('/cashbook/transfer-balances/excel', [TableExportController::class, 'TransferBalancesExcel'])->name('account.transferBalances.excel');
    // Route::get('/cashbook/transactions/pdf', [TableExportController::class, 'transactionsPDF'])->name('transactions.pdf');
    // routes for excel
    Route::get('/balance-sheet/excel', [TableExportController::class, 'BalanceSheetExcel'])->name('balance-sheet.excel');
    Route::get('/cashbook/transactions/excel', [TableExportController::class, 'TransactionExcel'])->name('transactions.excel');
    Route::get('/payments/clients/non-invoice/pdf', [TableExportController::class, 'nonInvoicePaymentsPDF'])->name('nonInvoicePayments.pdf');
    Route::get('/payments/clients/invoice/pdf', [TableExportController::class, 'invoicePaymentsPDF'])->name('invoicePayments.pdf');
    // route for excel 
    Route::get('/payments/clients/invoice/excel', [TableExportController::class, 'invoicePaymentsexcel'])->name('invoicePayments.excel');
    Route::get('/payments/suppliers/non-purchase/pdf', [TableExportController::class, 'nonPurchasePaymentsPDF'])->name('nonPurchasePayments.pdf');
    Route::get('/payments/suppliers/purchase/pdf', [TableExportController::class, 'purchasePaymentsPDF'])->name('locSupplierPayments.pdf');

    Route::get('/loan-authorities/pdf', [TableExportController::class, 'loanAuthoritiesPDF'])->name('loanAuthorities.pdf');
    Route::get('/loans/pdf', [TableExportController::class, 'loansPDF'])->name('loans.pdf');
    Route::get('/loan-payments/pdf', [TableExportController::class, 'loanPaymentsPDF'])->name('loanPayments.pdf');

    Route::get('/asset-types/pdf', [TableExportController::class, 'assetTypesPDF'])->name('assetTypes.pdf');
    Route::get('/assets/pdf', [TableExportController::class, 'assetsPDF'])->name('assets.pdf');

    Route::get('/payroll/pdf', [TableExportController::class, 'payrollPDF'])->name('payroll.pdf');

    Route::get('/clients/pdf', [TableExportController::class, 'clientsPDF'])->name('clients.pdf');
    Route::get('/clients/excel', [TableExportController::class, 'clients_excel'])->name('excel.pdf'); 
    Route::get('suppliers/excel', [TableExportController::class, 'SuppliersExcel'])->name('suppliers.excel'); 
    Route::get('/hotel/excel', [TableExportController::class, 'hotel_excel'])->name('excel.pdf');
    Route::get('/suppliers/pdf', [TableExportController::class, 'suppliersPDF'])->name('suppliers.pdf'); 

    Route::get('/departments/pdf', [TableExportController::class, 'departmentsPDF'])->name('departments.pdf');
    Route::get('/employees/pdf', [TableExportController::class, 'employeesPDF'])->name('employees.pdf');
    Route::get('/increments/pdf', [TableExportController::class, 'incrementsPDF'])->name('increments.pdf');

    Route::get('/product-categories/pdf', [TableExportController::class, 'productCategoriesPDF'])->name('productCategories.pdf');
    Route::get('/product-sub-categories/pdf', [TableExportController::class, 'productSubCategoriesPDF'])->name('productSubCategories.pdf');
    Route::get('/products/pdf', [TableExportController::class, 'productsPDF'])->name('products.pdf');
    Route::get('/products/export', [TableExportController::class, 'productsExcel'])->name('products.excel');
    Route::get('/product-categories/export', [TableExportController::class, 'productsCategoriesExcel'])->name('productsCategories.excel');
    Route::get('/product-sub-categories/export', [TableExportController::class, 'productSubCategoriesExcel'])->name('productSubCategories.excel');   
    

    Route::get('/inventory-adjustments/excel', [TableExportController::class, 'inventoryAdjustmentsExcel'])->name('inventoryAdjustments.pdf');
    Route::get('/non-zero-inventory/pdf', [TableExportController::class, 'nonZeroInventoryPDF'])->name('nonZeroInventory.pdf');
    Route::get('/booking/pdf/{id}', [TableExportController::class, 'bookedHotelPDF'])->name('booked.hotel.pdf');
});

// SPA routes
if (file_exists(storage_path('installed'))) {
    // SPA routes
    Route::get('{path}', SpaController::class)->where('path', '(.*)');
} else {
    Route::get('/', function () {
        return redirect('/install');
    });
}
