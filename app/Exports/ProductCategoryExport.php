<?php

namespace App\Exports;

use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductCategoryExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
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
            foreach($this->data as $productCategory){
                $columns [] = array(
                    'CAT-'.$productCategory->code,
                    $productCategory->name,
                    ($productCategory->status == 1) ? 'Active' : 'Inactive'
                );
            }
        }
        return collect($columns);
    }
}
