<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        
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
                    'cat_id' => 1
                ],
                [
                    'name' => 'Vodka',
                    'slug' => 'vodka',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Rum',
                    'slug' => 'rum',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Whiskey',
                    'slug' => 'wisky',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Beer',
                    'slug' => 'beer',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Gin',
                    'slug' => 'gin',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Breezer',
                    'slug' => 'breezer',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Tequila',
                    'slug' => 'tequila',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Brandy',
                    'slug' => 'brandy',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                
            ]);
        }

        \DB::table('brands')->insert([
            [
                'name' => 'Wine',
                'slug' => 'wine',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
            [
                'name' => 'Vodka',
                'slug' => 'vodka',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
            [
                'name' => 'Rum',
                'slug' => 'rum',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
            [
                'name' => 'Wiskey',
                'slug' => 'wisky',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
            [
                'name' => 'Beer',
                'slug' => 'beer',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
            [
                'name' => 'Gin',
                'slug' => 'gin',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
            [
                'name' => 'Breezer',
                'slug' => 'breezer',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
            [
                'name' => 'Tequila',
                'slug' => 'tequila',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
            [
                'name' => 'Brandy',
                'slug' => 'brandy',
                'code' => '1',
                'note' => null,
                'status' => 1, 
            ],
        ]);

        // if (DB::table('products')->count() == 0) {
        //     DB::table('products')->insert([ 
        //         [
        //             'name' => 'BP', 
        //             'model' => '', 
        //             'code' => '000001',
        //             'model' => 'BP',
        //             'barcode_symbology' => 'CODE128',
        //             'tax_type' => 'Exclusive',
        //             'purchase_price' => 877.85,
        //             'regular_price' => 1000.0,
        //             'discount' => 5.0,
        //             'inventory_count' => 22.0,
        //             'alert_qty' => 10,
        //             'note' => '',
        //             'status' => 1,
        //             'image_path' => 'product-01.jpeg',
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //             'sub_cat_id' => 1,
        //             'brand_id' => 2,
        //             'unit_id' => 1,
        //             'tax_id' => null,
        //         ], 
        //     ]);
        // }
    }
}
