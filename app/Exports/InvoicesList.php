<?php

namespace App\Exports;

// use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesList implements FromCollection, WithHeadings
{
    /** 
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Invoice Date',
            'Client',
            'Subtotal',
            'Transport',
            'Discount',
            'Tax',
            'Net Total',
            'Total Paid',
            'Total Due',
        ];
    }

   /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $columns = [];
        if(!empty($this->data)){
            foreach($this->data as $client){
                $columns [] = array(
                    $client->invoice_no,
                    $client->invoice_date,
                    $client->client->name,
                    $client->sub_total,
                    $client->transport,
                    $client->discount,
                    $client->taxAmount,
                    $client->NetTotal,
                    $client->invoiceTotalPaid,
                    $client->totalDue,
                );
            }
        }
        return collect($columns);

    }
}
