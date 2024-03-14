<?php

namespace App\Exports;

// use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DateTime;

class LedgerAccount implements FromCollection, WithHeadings
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
            'Date',
            'Hotel',
            'Particular',
            'Debit',
            'Credit',
            'Balance',
        ];
    }

   /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $columns = [];
        if (!empty($this->data)) {
            $balance = 0;
            foreach ($this->data as $client) {
                $transactionDate = (new DateTime($client->transaction_date))->format('Y-m-d');
    
                $columns[] = array(
                    $transactionDate,
                    $client->hotel->hotel_name,
                    $client->reason,
                    $client->amount ?? '0' ,
                    $client->balance  ?? '0',
                    $balance = ($client->type == 1) ? ($balance + $client->amount) : $balance ?? 0,
                );
            }
        }
        return collect($columns);
    }
}
