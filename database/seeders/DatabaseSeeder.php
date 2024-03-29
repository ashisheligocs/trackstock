<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            AccountSeeder::class,
            GeneralSettingsSeeder::class,
            CurrencySeeder::class,
            UserRoleSeeder::class,
            PermissionSeeder::class,
            UserPermissionSeeder::class,
            RolePermissionSeeder::class,
            ClientSeeder::class,
            VatRateSeeder::class,
            UnitSeeder::class,
            DepartmentSeeder::class,
            ExpenseCategorySeeder::class,
            ExpenseSubCategorySeeder::class,
            ProductSeeder::class,
            DemoSuppliersTableSeeder::class
            // for demo
            //DemoDatabaseSeeder::class,
        ]);
    }
}
