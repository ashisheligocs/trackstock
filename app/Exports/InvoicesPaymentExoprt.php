<?php

namespace App\Exports;

// use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesPaymentExoprt implements FromCollection, WithHeadings
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
            'Client',
            'Total',
            'Paid Amount',
            'Account',
            'Payment Date',
            'Status'
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
                    $client->invoice->invoice_no,
                    $client->user->name,
                    $client->amount,   
                    $client->amount,  
                    $client->invoicePaymentTransaction->cashbookAccount->account_number,
                    $client->created_at,
                    ($client->status == 1) ? 'Active' : 'Inactive'
                );
            }
        }
        return collect($columns);

    }
}
