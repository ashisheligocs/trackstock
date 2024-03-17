<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('products')->count() == 0) {
            DB::table('products')->insert([ 
                [
                 'name' => 'BP',
                 'code' => '0001',
                 'model' => '',
                 'barcode_symbology' => 'CODE128',
                 'sub_cat_id' => 7,
                 'brand_id' => 1,
                 'unit_id' => 8,
                 'tax_id' => null,
                 'tax_type' => '',
                 'regular_price' => 456.00 ,
                 'discount' => 0,
                 'note' => '',
                 'alert_qty' => 1,
                 'status' => 1,
                ], 
                [
                 'name' => 'RS',
                 'code' => '0001',
                 'model' => '',
                 'barcode_symbology' => 'CODE128',
                 'sub_cat_id' => 7,
                 'brand_id' => 1,
                 'unit_id' => 8,
                 'tax_id' => null,
                 'tax_type' => '',
                 'regular_price' => 550.00 ,
                 'discount' => 0,
                 'note' => '',
                 'alert_qty' => 1,
                 'status' => 1,
                ], 
            ]);
        }
        if (DB::table('product_categories')->count() == 0) {
            DB::table('product_categories')->insert([ 
                [
                    'name' => 'Liquor',
                    'slug' => 'liquor',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                ], 
            ]);
        }

        if (DB::table('product_sub_categories')->count() == 0) {
            DB::table('product_sub_categories')->insert([
                [
                    'name' => 'Wine',
                    'slug' => 'wine',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                [
                    'name' => 'Vodka',
                    'slug' => 'vodka',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                [
                    'name' => 'Rum',
                    'slug' => 'rum',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                [
                    'name' => 'Wiskey',
                    'slug' => 'wisky',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                [
                    'name' => 'Beer',
                    'slug' => 'beer',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                
            ]);
        }
    }
}
