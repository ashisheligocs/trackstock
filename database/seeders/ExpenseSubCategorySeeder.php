<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\ExpenseSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Entities\LedgerAccount;

class ExpenseSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('expense_sub_categories')->count() == 0) {
            DB::table('expense_sub_categories')->insert([
                [
                    'name'       => 'Office Supplies',
                    'code'       => 1,
                    'slug'       => 'office-supplies',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 1,
                ],
                [
                    'name'       => 'Stationery',
                    'code'       => 2,
                    'slug'       => 'stationery',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                     'exp_id' => 1,
                ],
                [
                    'name'       => 'Electricity',
                    'code'       => 3,
                    'slug'       => 'electricity',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                     'exp_id' => 1,
                ],
                [
                    'name'       => 'Water',
                    'code'       => 4,
                    'slug'       => 'water',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                     'exp_id' => 1,
                ],
                [
                    'name'       => 'Gas',
                    'code'       => 5,
                    'slug'       => 'gas',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 1,
                ],
                [
                    'name'       => 'Internet and Phone',
                    'code'       => 6,
                    'slug'       => 'internet-and-phone',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 1,
                ],
                [
                    'name'       => 'Property Rent',
                    'code'       => 7,
                    'slug'       => 'property-rent',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 1,
                ],
                [
                    'name'       => 'Health Department Permit',
                    'code'       => 8,
                    'slug'       => 'health-department-permit',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 1,
                ],
                [
                    'name'       => 'Routine Maintenance',
                    'code'       => 9,
                    'slug'       => 'routine-maintenance',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 3,
                ],
                [
                    'name'       => 'Equipment Maintenance',
                    'code'       => 10,
                    'slug'       => 'equipment-maintenance',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 3,
                ],
                [
                    'name'       => 'Advertising and Promotion',
                    'code'       => 11,
                    'slug'       => 'advertising-and-promotion',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 2,
                ],
                [
                    'name'       => 'Website and Online Presence',
                    'code'       => 12,
                    'slug'       => 'website-and-online-presence',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 2,
                ],
                [
                    'name'       => 'Professional Fees (Consultants, Legal, Accounting)',
                    'code'       => 13,
                    'slug'       => 'professional-fees',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 4,
                ],
                [
                    'name'       => 'Office Expense',
                    'code'       => 14,
                    'slug'       => 'office-expense',
                    'note'       => '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'exp_id' => 4,
                ],
            ]);
        }
    }
}
