<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // check if table is empty
        if (DB::table('roles')->count() == 0) {
            DB::table('roles')->insert([
                [
                    'name' => 'Super Admin',
                    'slug' => 'super-admin',
                ],
                [
                    'name' => 'Store',
                    'slug' => 'store',
                ],
                [
                    'name' => 'Sale',
                    'slug' => 'sale',
                ],
                [
                    'name' => 'Incharge',
                    'slug' => 'incharge',
                ],
            ]);
        }
    }
}