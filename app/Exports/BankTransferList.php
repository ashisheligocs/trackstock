<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BankTransferList implements FromCollection, WithHeadings
{
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
            'From Account',
            'To Account',
            'Amount',
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
            foreach($this->data as $list){
                $columns [] = array(
                    $list->date,
                    $list->hotel->hotel_name,
                    $list->debitTransaction->cashbookAccount->account_number,
                    $list->creditTransaction->cashbookAccount->account_number,
                    $list->amount,
                    ($list->status == 1) ? 'Active' : 'Inactive',
                );
            }
        }
        return collect($columns);
    }
}
