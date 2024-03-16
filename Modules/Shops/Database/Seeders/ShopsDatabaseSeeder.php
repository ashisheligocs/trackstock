<?php

namespace Modules\Shops\Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class ShopsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
        }
    }
}