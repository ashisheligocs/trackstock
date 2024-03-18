<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        if (DB::table('units')->count() == 0) {
            DB::table('units')->insert([
                [
                    'name' => 'Nos.',
                    'slug' => 'nos',
                    'code' => 'Nos.',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
