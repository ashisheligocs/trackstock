<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\Scopes\selectedHotelResource;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Shops\Entities\Shop;

class PurchaseList implements FromCollection, WithHeadings 
{
    protected $data;

    public function __construct($data)
    {   
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Hotel',
            'Purchase No',
            'Date',
            'Supplier',
            'Subtotal',
            'Discount',
            'GST',
            'Net Total',
            'Total Paid',
            'Total Due',
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
                $purchaseTotal = round($product->purchaseTotal(), 2);
                $subTotal = round($product->sub_total, 2);
                $due = round($product->totalDue() > 0 ? $product->totalDue() : 0, 2);
                $columns [] = array(
                    $product->hotel->hotel_name,
                    $product->purchase_no,
                    $product->purchase_date,
                    $product->supplier->name,
                    $product->sub_total,
                    $product->discount ?? '0',
                    $product->tax_amount,
                    $purchaseTotal ?? '0',
                    $subTotal ?? '0',
                    $due ?? '0',
                    // $product->totalDue,

                );
            }
        }
        return collect($columns);
    }
}
