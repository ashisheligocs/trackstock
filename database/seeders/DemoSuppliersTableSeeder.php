<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class DemoSuppliersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('suppliers')->delete();
        if (DB::table('shops')->count() == 0) {
            DB::table('shops')->insert([
                [
                    'shop_name' => 'Shop 1',
                    'shop_address' => 'Test address',
                    'shop_phone' => '9878456510',
                    'shop_phone1' => '',
                    'shop_email' => 'shopdemo@1.com',
                    'shop_prefix' => 'SHD',
                    'shop_Status' => 0,
                    'contact_phone' => '9878456510',
                    'contact_name' => 'Test contact name',
                    'del_status' => 0,
                ]
            ]);
            DB::table('suppliers')->insert([
                0 => [
                    'id' => 1,
                    'name' => 'Warehouse',
                    'slug' => 'warehouse',
                    'supplier_id' => '1',
                    'email' => null,
                    'phone' => '1111111111',
                    'company_name' => '',
                    'address' => '',
                    'status' => 1,
                    'image_path' => 'avatar.png',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'shop_id' => 1,
                ], 
            ]);
        }
    }
}