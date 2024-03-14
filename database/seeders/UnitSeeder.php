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
                    'name' => 'Set',
                    'slug' => 'set',
                    'code' => 'Set',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Roll',
                    'slug' => 'roll',
                    'code' => 'Roll',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Meter',
                    'slug' => 'meter',
                    'code' => 'M',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Packet',
                    'slug' => 'packet',
                    'code' => 'Pkt',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Dozen',
                    'slug' => 'dozen',
                    'code' => 'Doz',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Bottle',
                    'slug' => 'bottle',
                    'code' => 'Btl',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Number',
                    'slug' => 'number',
                    'code' => 'No.',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Litre',
                    'slug' => 'litre',
                    'code' => 'L',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Kilogram',
                    'slug' => 'kilogram',
                    'code' => 'KG',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
