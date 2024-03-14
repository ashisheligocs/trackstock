<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class PurchaseReturnList implements FromCollection, WithHeadings 
{
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Return No',
            'Purchase No',
            'Supplier',
            'Return Reason',
            'Cost of Return Products',
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
                    $product->purchase->purchase_no,
                    $product->purchase->supplier->name,
                    $product->reason,
                    $product->total_return,
                    $product->date,
                    ($product->status == 1) ? 'Active' : 'Inactive',

                );
            }
        }
        return collect($columns);
    }
}
