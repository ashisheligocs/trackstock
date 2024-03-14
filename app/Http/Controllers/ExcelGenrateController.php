<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Quotation;
use Illuminate\Http\Request;
use spatie\SimpleExcel\SimpleExcelWriter;
use Maatwebsite\Excel\Facades\Excel;


class ExcelGenrateController extends Controller
{
    public function generateInvoiceexcel($slug)
    {
        $invoice = Invoice::where('slug', $slug)->with('client', 'invoiceProducts.invoice', 'invoicePayments.invoicePaymentTransaction.cashbookAccount', 'invoiceProducts.product.productUnit', 'invoiceProducts.product.productTax', 'invoiceTax', 'user')->first();
        // generate and save excel

        $excelFileName = 'client-list.xlsx';
        $excel = Excel::create("app/local/{$excelFileName}")
            ->addRow([
                'first_name' => 'John',
                'last_name' => 'Doe',
            ])
            ->addRow([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
            ]);
        return $excel->streamDownload('Invoice-' . $invoice->slug . '.xlx');
    }
}
