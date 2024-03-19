<?php

namespace Modules\Accounts\Database\Seeders;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;
use Modules\Accounts\Entities\LedgerAccount;
use Modules\Accounts\Entities\LedgerCategory;

class AccountsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (DB::table('ledgers')->count() == 0) {
            DB::table('ledgers')->insert([
                [
                    'name' => 'Assets',
                    'position' => '1',
                ],
                [
                    'name' => 'Liabilities',
                    'position' => '2',
                ],
                [
                    'name' => 'Equity',
                    'position' => '3',
                ],
                [
                    'name' => 'Revenues',
                    'position' => '4',
                ],
                [
                    'name' => 'Expenses',
                    'position' => '5',
                ],
            ]);
        }

        if (DB::table('ledgers_categories')->count() == 0) {
            DB::table('ledgers_categories')->insert([
                [
                    'coa_id' => 1,
                    'name' => "Current Asset",
                    'parant_id' => null,
                    'system_name' => "Current-Asset",
                ],
                [
                    'coa_id' => 1,
                    'name' => "Non Current Assets",
                    'parant_id' => null,
                    'system_name' => "Non-Current-Assets",
                ],
                [
                    'coa_id' => 2,
                    'name' => "Current Liabilities",
                    'parant_id' => null,
                    'system_name' => "Current-Liabilities",
                ],
                [
                    'coa_id' => 2,
                    'name' => "Non Current Liabilities",
                    'parant_id' => null,
                    'system_name' => "Non-Current-Liabilities",
                ],
                [
                    'coa_id' => 3,
                    'name' => "Share Holders Equity",
                    'parant_id' => null,
                    'system_name' => "Share-Holders-Equity",
                ],
                [
                    'coa_id' => 3,
                    'name' => "Reserve & Surplus",
                    'parant_id' => null,
                    'system_name' => "Reserve-Surplus",
                ],
                [
                    'coa_id' => 4,
                    'name' => "Operating",
                    'parant_id' => null,
                    'system_name' => "Operating",
                ],
                [
                    'coa_id' => 4,
                    'name' => "Non-operating revenue ",
                    'parant_id' => null,
                    'system_name' => "Non-operating-revenue ",
                ],
                [
                    'coa_id' => 5,
                    'name' => "Other Expenses",
                    'parant_id' => null,
                    'system_name' => "Other Expenses",
                ],
                [
                    'coa_id' => 5,
                    'name' => "Store Expenses",
                    'parant_id' => null,
                    'system_name' => "Store Expenses",
                ],
                /*************************************Sub group start******************************************* */
                [
                    'coa_id' => 1,
                    'name' => "Account Receivable",
                    'parant_id' => 1,
                    'system_name' => "Account-Receivable",
                ],
                [
                    'coa_id' => 1,
                    'name' => "Advance, Deposit And Pre-payments",
                    'parant_id' => 1,
                    'system_name' => "Advance-Deposit-And-Pre-payments",
                ],
                [
                    'coa_id' => 1,
                    'name' => "Cash & Cash Equivalent",
                    'parant_id' => 1,
                    'system_name' => "Cash-Cash-Equivalent",
                ],
                [
                    'coa_id' => 1,
                    'name' => "Electrical Equipment",
                    'parant_id' => 2,
                    'system_name' => "Electrical Equipment",
                ],
                [
                    'coa_id' => 1,
                    'name' => "Furniture & Fixturers",
                    'parant_id' => 2,
                    'system_name' => "Furniture & Fixturers",
                ],
                [
                    'coa_id' => 1,
                    'name' => "Office Equipment",
                    'parant_id' => 2,
                    'system_name' => "Office Equipment",
                ],
                [
                    'coa_id' => 1,
                    'name' => "Others Assets",
                    'parant_id' => 2,
                    'system_name' => "Others Assets",
                ],
                [
                    'coa_id' => 2,
                    'name' => "Account Payable",
                    'parant_id' => 3,
                    'system_name' => "Account Payable",
                ],
                [
                    'coa_id' => 2,
                    'name' => "Liabilities for Expenses",
                    'parant_id' => 3,
                    'system_name' => "Liabilities for Expenses",
                ],
            ]);
        }

        $expCats = ExpenseCategory::get();

        foreach ($expCats as $cat) {
            LedgerCategory::updateOrCreate([
                'system_name' => $cat->slug,
                'expense_category_id' => $cat->id
            ],[
                'coa_id' => 5,
                'name' => $cat->name,
                'system_name' => $cat->slug,
                'expense_category_id' => $cat->id
            ]);
        }

        $expCats = ExpenseSubCategory::get();

        $lastAccountId = 0;
        foreach ($expCats as $index => $cat) {
            $account = LedgerAccount::updateOrCreate([
                'system_name' => $cat->slug,
                'expense_sub_category_id' => $cat->id
            ],[
                'ledger_type' => 5,
                'ledger_group' => @$cat->expCategory->ledgerCategory->id,
                'ledger_name' => $cat->name,
                'system_name' => $cat->slug,
                'code' => $cat->slug,
                'is_bank' => 0,
                'show_in_day_book' => 0,
                'expense_sub_category_id' => $cat->id
            ]);

            Account::updateOrCreate([
                'account_id' => $account->id,
            ],[
                'account_number' => $cat->slug,
                'slug' => $cat->slug,
                'created_by' => '1',
                'created_at' => now(),
                'updated_at' => now(),
                'date' => now(),
                'note' => null,
                'account_id' => $account->id
            ]);

            if ($index === (count($expCats) - 1)) {
                $lastAccountId = $account->id;
            }
        }

        if (DB::table('ledgers_accounts')->count() <= count($expCats)) {
            DB::table('ledgers_accounts')->insert([
                [
                    'ledger_type' => 1,
                    'ledger_group' => 13,
                    'ledger_name' => "Cash",
                    'system_name' => "CASH-0001",
                    'code' => "CASH-0001",
                    'is_bank' => 1,
                ],
                [
                    'ledger_type' => 2,
                    'ledger_group' => 18,
                    'ledger_name' => "Account Payable",
                    'system_name' => "ACCOUNT-PAYABLE",
                    'code' => "ACCOUNT-PAYABLE",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 1,
                    'ledger_group' => 11,
                    'ledger_name' => "Account Receivable",
                    'system_name' => "ACCOUNT-RECEIVABLE",
                    'code' => "ACCOUNT-RECEIVABLE",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 2,
                    'ledger_group' => 3,
                    'ledger_name' => "Input GST",
                    'system_name' => "GST-INPUT",
                    'code' => "GST-INPUT",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 2,
                    'ledger_group' => 3,
                    'ledger_name' => "Output GST",
                    'system_name' => "GST-OUTPUT",
                    'code' => "GST-OUTPUT",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 4,
                    'ledger_group' => 7,
                    'ledger_name' => "Operating Revenue",
                    'system_name' => "OPERATING_REVENUE",
                    'code' => "OPERATING_REVENUE",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 3,
                    'ledger_group' => 5,
                    'ledger_name' => "Equity",
                    'system_name' => "EQUITY",
                    'code' => "EQUITY",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 1,
                    'ledger_group' => 1,
                    'ledger_name' => "Inventory",
                    'system_name' => "INVENTORY",
                    'code' => "INVENTORY",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 4,
                    'ledger_group' => 7,
                    'ledger_name' => "Charges",
                    'system_name' => "CHARGES",
                    'code' => "CHARGES",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 4,
                    'ledger_group' => 7,
                    'ledger_name' => "Restaurant revenue",
                    'system_name' => "RESTAURANT-REVENUE",
                    'code' => "RESTAURANT-REVENUE",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 5,
                    'ledger_group' => 20,
                    'ledger_name' => "Salary",
                    'system_name' => "SALARY",
                    'code' => "SALARY",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 4,
                    'ledger_group' => 7,
                    'ledger_name' => "Round-off Revenue",
                    'system_name' => "ROUND-OFF-REVENUE",
                    'code' => "ROUND-OFF-REVENUE",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 5,
                    'ledger_group' => 20,
                    'ledger_name' => "Round-off Expense",
                    'system_name' => "ROUND-OFF-EXPENSE",
                    'code' => "ROUND-OFF-EXPENSE",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 2,
                    'ledger_group' => 3,
                    'ledger_name' => "Commission Payable",
                    'system_name' => "commission-payable",
                    'code' => "commission-payable",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 5,
                    'ledger_group' => 9,
                    'ledger_name' => "Bank Charges",
                    'system_name' => "bank-charges",
                    'code' => "bank-charges",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 4,
                    'ledger_group' => 7,
                    'ledger_name' => "Direct Sales",
                    'system_name' => "DIRECT-SALES",
                    'code' => "DIRECT-SALES",
                    'is_bank' => 0,
                ],
                [
                    'ledger_type' => 1,
                    'ledger_group' => 13,
                    'ledger_name' => "In Charge",
                    'system_name' => "in-charge",
                    'code' => "in-charge",
                    'is_bank' => 1,
                ],
                [
                    'ledger_type' => 1,
                    'ledger_group' => 13,
                    'ledger_name' => "Warehouse",
                    'system_name' => "warehouse",
                    'code' => "warehouse",
                    'is_bank' => 1,
                ],
                [
                    'ledger_type' => 1,
                    'ledger_group' => 13,
                    'ledger_name' => "Bank",
                    'system_name' => "bank",
                    'code' => "bank",
                    'is_bank' => 1,
                ],
                
            ]);
        }

        if (DB::table('accounts')->count() <= count($expCats)) {
            DB::table('accounts')->insert([
                [
                    'bank_name' => 'Cash',
                    'branch_name' => 'Office',
                    'account_number' => 'CASH-0001',
                    'slug' => 'cash-0001',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 1
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'ACCOUNT-PAYABLE',
                    'slug' => 'ACCOUNT-PAYABLE',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 2
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'ACCOUNT-RECEIVABLE',
                    'slug' => 'ACCOUNT-RECEIVABLE',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 3
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'GST-INPUT',
                    'slug' => 'GST-INPUT',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 4
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'GST-OUTPUT',
                    'slug' => 'GST-OUTPUT',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 5
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'OPERATING_REVENUE',
                    'slug' => 'OPERATING_REVENUE',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 6
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'EQUITY',
                    'slug' => 'EQUITY',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 7
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'INVENTORY',
                    'slug' => 'INVENTORY',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 8
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'CHARGES',
                    'slug' => 'CHARGES',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 9
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'RESTAURANT-REVENUE',
                    'slug' => 'RESTAURANT-REVENUE',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 10
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'SALARY',
                    'slug' => 'SALARY',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 11
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'ROUND-OFF-REVENUE',
                    'slug' => 'ROUND-OFF-REVENUE',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 12
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'ROUND-OFF-EXPENSE',
                    'slug' => 'ROUND-OFF-EXPENSE',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 13
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'commission-payable',
                    'slug' => 'commission-payable',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 14
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'Bank Charges',
                    'slug' => 'bank-charges',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 15
                ],
                [
                    'bank_name' => '',
                    'branch_name' => '',
                    'account_number' => 'Direct Sales',
                    'slug' => 'DIRECT-SALES',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 16
                ],
                [
                    'bank_name' => 'In Charge',
                    'branch_name' => 'Office',
                    'account_number' => 'in-charge',
                    'slug' => 'in-charge',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 17
                ],
                [
                    'bank_name' => 'Warehouse',
                    'branch_name' => 'Office',
                    'account_number' => 'warehouse',
                    'slug' => 'warehouse',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 18
                ],
                [
                    'bank_name' => 'Bank',
                    'branch_name' => 'Bank',
                    'account_number' => 'bank',
                    'slug' => 'bank',
                    'created_by' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now(),
                    'note' => null,
                    'account_id' => $lastAccountId + 19
                ],
            ]);
        }
    }
}