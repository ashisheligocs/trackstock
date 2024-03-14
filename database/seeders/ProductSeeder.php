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
                    'name' => 'Food and Beverage',
                    'slug' => 'food-and-beverage',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                ],
                [
                    'name' => 'Supplies',
                    'slug' => 'cleaning-supplies',
                    'code' => '2',
                    'note' => null,
                    'status' => 1,
                ],
                [
                    'name' => 'Office Supplies',
                    'slug' => 'office-supplies',
                    'code' => '3',
                    'note' => null,
                    'status' => 1,
                ],
                [
                    'name' => 'Print Materials',
                    'slug' => 'print-materials',
                    'code' => '4',
                    'note' => null,
                    'status' => 1,
                ],
                [
                    'name' => 'General Maintenance',
                    'slug' => 'general-maintenance',
                    'code' => '5',
                    'note' => null,
                    'status' => 1,
                ],
            ]);
        }

        if (DB::table('product_sub_categories')->count() == 0) {
            DB::table('product_sub_categories')->insert([
                [
                    'name' => 'Cleaning Supplies',
                    'slug' => 'cleaning-supplies',
                    'code' => '1',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                [
                    'name' => 'Linens and Bedding',
                    'slug' => 'linens-and-bedding',
                    'code' => '2',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                [
                    'name' => 'Toiletries',
                    'slug' => 'toiletries',
                    'code' => '3',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                [
                    'name' => 'Kitchen Supplies',
                    'slug' => 'kitchen-supplies',
                    'code' => '4',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 2
                ],
                [
                    'name' => 'Perishables',
                    'slug' => 'perishables',
                    'code' => '5',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],

                [
                    'name' => 'Non-Perishables',
                    'slug' => 'non-perishables',
                    'code' => '6',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Beverages',
                    'slug' => 'beverages',
                    'code' => '7',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
                [
                    'name' => 'Operating Supplies',
                    'slug' => 'operating-supplies',
                    'code' => '8',
                    'note' => null,
                    'status' => 1,
                    'cat_id' => 1
                ],
            ]);
        }
    }
}
