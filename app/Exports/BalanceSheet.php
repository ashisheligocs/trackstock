<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class BalanceSheet implements FromView
{
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.balancesheet', [
            'asset' => $this->data['assest'],
            'liability' => $this->data['liability'],
        ]);
    }


    // public function headings(): array
    // {
    //     return [
    //         'Liability',
    //     ];
    // }

    // http://127.0.0.1:8000/api/account/ledger/liability
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     $columns = [];
    //     if(!empty($this->data)){
    //         foreach($this->data as $list){
    //             $columns [] = array(
    //                 $list->date,
    //                 $list->hotel->hotel_name,
    //                 $list->debitTransaction->cashbookAccount->account_number,
    //                 $list->creditTransaction->cashbookAccount->account_number,
    //                 $list->amount,
    //                 ($list->status == 1) ? 'Active' : 'Inactive',
    //             );
    //         }
    //     }
    //     return collect($columns);
    // }
}
