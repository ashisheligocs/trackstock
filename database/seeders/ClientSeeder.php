<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // check if table is empty
        if (DB::table('clients')->count() == 0) {
            DB::table('clients')->insert([
                [
                    'name' => 'Walking Customer',
                    'client_id' => '1',
                    'slug' => 'walking-customer',
                    'email' => 'eligoCS@example.com',
                    'phone' => '017000000',
                    'company_name' => 'EligoCS',
                    'status' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}