<?php

namespace App\Exports;

use App\Models\ProductSubCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductSubCategoryExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Category',
            'Sub Category Code',
            'Sub Category Name',
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
            foreach($this->data as $productSubCategory){
                $columns [] = array(
                    $productSubCategory->category->name .'[CAT-'.$productSubCategory->category->code.']',
                    'SCAT-'.$productSubCategory->code,
                    $productSubCategory->name,
                    ($productSubCategory->status == 1) ? 'Active' : 'Inactive'
                );
            }
        }
        return collect($columns);
    }
}
