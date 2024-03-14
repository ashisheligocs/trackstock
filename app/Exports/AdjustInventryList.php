<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class AdjustInventryList implements FromCollection, WithHeadings 
{
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Adjustment No',
            'Reason',
            'Date',
            'Status',
        ];
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $columns = [];
        if(!empty($this->data)){
            foreach($this->data as $product){
                $columns [] = array(
                    $product->code,
                    $product->reason ?? '0',
                    $product->date,
                    ($product->status == 1) ? 'Active' : 'Inactive',

                );
            }
        }
        return collect($columns);
    }
}
