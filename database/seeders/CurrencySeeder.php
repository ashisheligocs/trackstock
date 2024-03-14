<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // check if table is empty
        if (DB::table('currencies')->count() == 0) {
            DB::table('currencies')->insert([
                [
                    'name' => 'Indian National Rupees',
                    'slug' => 'indian-national-rupees',
                    'code' => 'INR',
                    'symbol' => '₹',
                    'position' => 'left',
                    'note' => 'This is default currency',
                    'status' => 1,
                ],
            ]);
        }
    }
}
