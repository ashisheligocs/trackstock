<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Entities\LedgerCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('expense_categories')->count() == 0) {
            DB::table('expense_categories')->insert([
                [
                    'name'       => 'Operating Expenses',
                    'code'       => 1,
                    'slug'       => 'operating-expenses',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name'       => 'Marketing and Advertising',
                    'code'       => 2,
                    'slug'       => 'marketing-and-advertising',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name'       => 'Maintenance and Repairs',
                    'code'       => 3,
                    'slug'       => 'maintenance-and-repairs',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name'       => 'Miscellaneous Expenses',
                    'code'       => 4,
                    'slug'       => 'miscellaneous-expenses',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
