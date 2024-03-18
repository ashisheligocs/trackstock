<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSuppliersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('suppliers')->delete();

        \DB::table('suppliers')->insert([
            0 => [
                'id' => 1,
                'name' => 'Warehouse',
                'slug' => 'warehouse',
                'supplier_id' => '1',
                'email' => null,
                'phone' => null,
                'company_name' => '',
                'address' => '',
                'status' => 1,
                'image_path' => 'avatar.png',
                'created_at' => now(),
                'updated_at' => now(),
            ], 
        ]);
    }
}