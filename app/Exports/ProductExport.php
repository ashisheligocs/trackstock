<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
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
            'Code',
            'Name',
            'Item Model',
            'Unit',
            'Selling Price'
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
                    $product->proSubCategory->name,
                    $product->code,
                    $product->name,
                    $product->model,
                    $product->productUnit->code,
                    $product->regular_price,
                );
            }
        }
        return collect($columns);
    }
}
