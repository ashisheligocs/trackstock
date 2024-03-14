<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VatRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('vat_rates')->count() == 0) {
            DB::table('vat_rates')->insert([
                [
                    'name' => 'SGST 0%',
                    'slug' => 'sgst-0',
                    'code' => 'SGST@0',
                    'rate' => '0',
                ],
                [
                    'name' => 'CGST 0%',
                    'slug' => 'cgst-0',
                    'code' => 'CGST@0',
                    'rate' => '0',
                ],
                [
                    'name' => 'CGST 14%',
                    'slug' => 'cgst-14',
                    'code' => 'CGST@14',
                    'rate' => '14',
                ],
                [
                    'name' => 'SGST 14%',
                    'slug' => 'sgst-14',
                    'code' => 'SGST@14',
                    'rate' => '14',
                ],
                [
                    'name' => 'CGST 9%',
                    'slug' => 'cgst-9',
                    'code' => 'CGST@9',
                    'rate' => '9',
                ],
                [
                    'name' => 'SGST 9%',
                    'slug' => 'sgst-9',
                    'code' => 'SGST@9',
                    'rate' => '9',
                ],
                [
                    'name' => 'SGST 6%',
                    'slug' => 'sgst-6',
                    'code' => 'SGST@6',
                    'rate' => '6',
                ],
                [
                    'name' => 'CGST 6%',
                    'slug' => 'cgst-6',
                    'code' => 'CGST@6',
                    'rate' => '6',
                ],
                [
                    'name' => 'CGST 2.5%',
                    'slug' => 'cgst-2-5',
                    'code' => 'CGST@2.5',
                    'rate' => '2.5',
                ],
                [
                    'name' => 'SGST 2.5%',
                    'slug' => 'sgst-2-5',
                    'code' => 'SGST@2.5',
                    'rate' => '2.5',
                ],
            ]);
        }
    }
}
