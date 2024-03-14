<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('departments')->count() == 0) {
            DB::table('departments')->insert([
                [
                    'name' => 'Room Service Department',
                    'slug' => 'room-service-departments',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Accounting departments',
                    'slug' => 'account-departments',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Maintenance Department',
                    'slug' => 'maintenance-department',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Sales and Marketing Department',
                    'slug' => 'sales-and-marketing-department',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Food and Beverage Department',
                    'slug' => 'food-and-beverage-department',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Housekeeping Department',
                    'slug' => 'housekeeping-department',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Front Office Department',
                    'slug' => 'front-office-department',
                    'note' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
