<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuplierList implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Supplier ID',
            'Name',
            'Contact Number',
            'Email',
            'Company Name',
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
            foreach($this->data as $product){
                $columns [] = array(
                    $product->supplier_id,
                    $product->name,
                    $product->phone,
                    $product->email,
                    $product->company_name,
                    ($product->status == 1) ? 'Active' : 'Inactive',
                );
            }
        }
        return collect($columns);
    }
}
