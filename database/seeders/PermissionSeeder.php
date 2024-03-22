<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // check if table is empty
        if (DB::table('permissions')->count() == 0) {
            DB::table('permissions')->insert([
              
                [
                    'name' => 'Create',
                    'guard_name' => 'shop Management',
                    'slug' => 'shop-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'shop Management',
                    'slug' => 'shop-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'shop Management',
                    'slug' => 'shop-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'shop Management',
                    'slug' => 'shop-delete',
                ],

                // Restaurant booking permission

                [
                    'name' => 'Create',
                    'guard_name' => 'Restaurant Management',
                    'slug' => 'restaurant-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Restaurant Management',
                    'slug' => 'restaurant-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Restaurant Management',
                    'slug' => 'restaurant-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Restaurant Management',
                    'slug' => 'restaurant-delete',
                ],
                [
                    'name' => 'Create Orders',
                    'guard_name' => 'Restaurant Management',
                    'slug' => 'restaurant-order',
                ],

                // expense category permission
                // [
                //     'name' => 'Create',
                //     'guard_name' => 'Expense Category Management',
                //     'slug' => 'expense-category-create',
                // ],
                // [
                //     'name' => 'List',
                //     'guard_name' => 'Expense Category Management',
                //     'slug' => 'expense-category-list',
                // ],
                // [
                //     'name' => 'Edit',
                //     'guard_name' => 'Expense Category Management',
                //     'slug' => 'expense-category-edit',
                // ],
                // [
                //     'name' => 'Delete',
                //     'guard_name' => 'Expense Category Management',
                //     'slug' => 'expense-category-delete',
                // ],

                // expense sub category permission
                // [
                //     'name' => 'Create',
                //     'guard_name' => 'Expense Subcategory Management',
                //     'slug' => 'expense-sub-category-create',
                // ],
                // [
                //     'name' => 'List',
                //     'guard_name' => 'Expense Subcategory Management',
                //     'slug' => 'expense-sub-category-list',
                // ],
                // [
                //     'name' => 'Edit',
                //     'guard_name' => 'Expense Subcategory Management',
                //     'slug' => 'expense-sub-category-edit',
                // ],
                // [
                //     'name' => 'Delete',
                //     'guard_name' => 'Expense Subcategory Management',
                //     'slug' => 'expense-sub-category-delete',
                // ],

                // expense permission
                // [
                //     'name' => 'Create',
                //     'guard_name' => 'Expense Management',
                //     'slug' => 'expense-create',
                // ],
                // [
                //     'name' => 'List',
                //     'guard_name' => 'Expense Management',
                //     'slug' => 'expense-list',
                // ],
                // [
                //     'name' => 'Edit',
                //     'guard_name' => 'Expense Management',
                //     'slug' => 'expense-edit',
                // ],
                // [
                //     'name' => 'View',
                //     'guard_name' => 'Expense Management',
                //     'slug' => 'expense-view',
                // ],
                // [
                //     'name' => 'Expense Delete',
                //     'guard_name' => 'Expense Management',
                //     'slug' => 'expense-delete',
                // ],

                // purchase permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Purchase Management',
                    'slug' => 'purchase-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Purchase Management',
                    'slug' => 'purchase-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Purchase Management',
                    'slug' => 'purchase-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Purchase Management',
                    'slug' => 'purchase-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Purchase Management',
                    'slug' => 'purchase-delete',
                ],

                // purchase return permission
                // [
                //     'name' => 'Create',
                //     'guard_name' => 'Purchase Return Management',
                //     'slug' => 'purchase-return-create',
                // ],
                // [
                //     'name' => 'List',
                //     'guard_name' => 'Purchase Return Management',
                //     'slug' => 'purchase-return-list',
                // ],
                // [
                //     'name' => 'Edit',
                //     'guard_name' => 'Purchase Return Management',
                //     'slug' => 'purchase-return-edit',
                // ],
                // [
                //     'name' => 'View',
                //     'guard_name' => 'Purchase Return Management',
                //     'slug' => 'purchase-return-view',
                // ],
                // [
                //     'name' => 'Delete',
                //     'guard_name' => 'Purchase Return Management',
                //     'slug' => 'purchase-return-delete',
                // ],

                // quotation permission
//                [
//                    'name' => 'Create',
//                    'guard_name' => 'Quotation Management',
//                    'slug' => 'quotation-create',
//                ],
//                [
//                    'name' => 'List',
//                    'guard_name' => 'Quotation Management',
//                    'slug' => 'quotation-list',
//                ],
//                [
//                    'name' => 'Edit',
//                    'guard_name' => 'Quotation Management',
//                    'slug' => 'quotation-edit',
//                ],
//                [
//                    'name' => 'View',
//                    'guard_name' => 'Quotation Management',
//                    'slug' => 'quotation-view',
//                ],
//                [
//                    'name' => 'Delete',
//                    'guard_name' => 'Quotation Management',
//                    'slug' => 'quotation-delete',
//                ],
//                [
//                    'name' => 'Quotation to Invoice',
//                    'guard_name' => 'Quotation Management',
//                    'slug' => 'quotation-to-invoice',
//                ],

                // invoice permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Invoice Management',
                    'slug' => 'invoice-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Invoice Management',
                    'slug' => 'invoice-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Invoice Management',
                    'slug' => 'invoice-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Invoice Management',
                    'slug' => 'invoice-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Invoice Management',
                    'slug' => 'invoice-delete',
                ],

                // account permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Account Management',
                    'slug' => 'account-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Account Management',
                    'slug' => 'account-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Account Management',
                    'slug' => 'account-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Account Management',
                    'slug' => 'account-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Account Management',
                    'slug' => 'account-delete',
                ],

                // account balance permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Account Balance Management',
                    'slug' => 'account-balance-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Account Balance Management',
                    'slug' => 'account-balance-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Account Balance Management',
                    'slug' => 'account-balance-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Account Balance Management',
                    'slug' => 'account-balance-delete',
                ],

                // balance transfer permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Balance Transfer Management',
                    'slug' => 'account-transfer-balance-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Balance Transfer Management',
                    'slug' => 'account-transfer-balance-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Balance Transfer Management',
                    'slug' => 'account-transfer-balance-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Balance Transfer Management',
                    'slug' => 'account-transfer-balance-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Balance Transfer Management',
                    'slug' => 'account-transfer-balance-delete',
                ],

                // non purchase payment permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Non Purchase Payment Management',
                    'slug' => 'non-purchase-payment-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Non Purchase Payment Management',
                    'slug' => 'non-purchase-payment-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Non Purchase Payment Management',
                    'slug' => 'non-purchase-payment-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Non Purchase Payment Management',
                    'slug' => 'non-purchase-payment-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Non Purchase Payment Management',
                    'slug' => 'non-purchase-payment-delete',
                ],

                // purchase payment permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Purchase Payment Management',
                    'slug' => 'purchase-payment-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Purchase Payment Management',
                    'slug' => 'purchase-payment-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Purchase Payment Management',
                    'slug' => 'purchase-payment-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Purchase Payment Management',
                    'slug' => 'purchase-payment-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Purchase Payment Management',
                    'slug' => 'purchase-payment-delete',
                ],

                // non invoice payment permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Non Invoice Payment Management',
                    'slug' => 'non-invoice-payment-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Non Invoice Payment Management',
                    'slug' => 'non-invoice-payment-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Non Invoice Payment Management',
                    'slug' => 'non-invoice-payment-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Non Invoice Payment Management',
                    'slug' => 'non-invoice-payment-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Non Invoice Payment Management',
                    'slug' => 'non-invoice-payment-delete',
                ],

                // invoice payment permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Invoice Payment Management',
                    'slug' => 'invoice-payment-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Invoice Payment Management',
                    'slug' => 'invoice-payment-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Invoice Payment Management',
                    'slug' => 'invoice-payment-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Invoice Payment Management',
                    'slug' => 'invoice-payment-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Invoice Payment Management',
                    'slug' => 'invoice-payment-delete',
                ],


                // asset type permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Asset Type Management',
                    'slug' => 'asset-type-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Asset Type Management',
                    'slug' => 'asset-type-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Asset Type Management',
                    'slug' => 'asset-type-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Asset Type Management',
                    'slug' => 'asset-type-delete',
                ],

                // asset permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Asset Management',
                    'slug' => 'asset-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Asset Management',
                    'slug' => 'asset-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Asset Management',
                    'slug' => 'asset-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Asset Management',
                    'slug' => 'asset-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Asset Management',
                    'slug' => 'asset-delete',
                ],

                // payroll permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Payroll Management',
                    'slug' => 'payroll-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Payroll Management',
                    'slug' => 'payroll-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Payroll Management',
                    'slug' => 'payroll-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Payroll Management',
                    'slug' => 'payroll-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Payroll Management',
                    'slug' => 'payroll-delete',
                ],

                // client permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Client Management',
                    'slug' => 'client-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Client Management',
                    'slug' => 'client-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Client Management',
                    'slug' => 'client-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Client Management',
                    'slug' => 'client-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Client Management',
                    'slug' => 'client-delete',
                ],

                // supplier permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Supplier Management',
                    'slug' => 'supplier-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Supplier Management',
                    'slug' => 'supplier-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Supplier Management',
                    'slug' => 'supplier-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Supplier Management',
                    'slug' => 'supplier-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Supplier Management',
                    'slug' => 'supplier-delete',
                ],

                // department permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Department Management',
                    'slug' => 'department-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Department Management',
                    'slug' => 'department-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Department Management',
                    'slug' => 'department-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Department Management',
                    'slug' => 'department-delete',
                ],

                // employee permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Employee Management',
                    'slug' => 'employee-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Employee Management',
                    'slug' => 'employee-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Employee Management',
                    'slug' => 'employee-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Employee Management',
                    'slug' => 'employee-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Employee Management',
                    'slug' => 'employee-delete',
                ],


                // product category permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Product Category Management',
                    'slug' => 'product-category-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Product Category Management',
                    'slug' => 'product-category-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Product Category Management',
                    'slug' => 'product-category-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Product Category Management',
                    'slug' => 'product-category-delete',
                ],

                // product sub category permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Product Subcategory Management',
                    'slug' => 'product-sub-category-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Product Subcategory Management',
                    'slug' => 'product-sub-category-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Product Subcategory Management',
                    'slug' => 'product-sub-category-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Product Subcategory Management',
                    'slug' => 'product-sub-category-delete',
                ],

                // product permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Product Management',
                    'slug' => 'product-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Product Management',
                    'slug' => 'product-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Product Management',
                    'slug' => 'product-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Product Management',
                    'slug' => 'product-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Product Management',
                    'slug' => 'product-delete',
                ],

                // inventory permission
                [
                    'name' => 'History',
                    'guard_name' => 'Inventory Management',
                    'slug' => 'inventory-history',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Inventory Management',
                    'slug' => 'inventory-view',
                ],

                // inventory adjustment permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Inventory Adjustment Management',
                    'slug' => 'adjustment-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Inventory Adjustment Management',
                    'slug' => 'adjustment-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Inventory Adjustment Management',
                    'slug' => 'adjustment-edit',
                ],
                [
                    'name' => 'View',
                    'guard_name' => 'Inventory Adjustment Management',
                    'slug' => 'adjustment-view',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Inventory Adjustment Management',
                    'slug' => 'adjustment-delete',
                ],

                // other permission
//                [
//                    'name' => 'Print Barcode',
//                    'guard_name' => 'Extra Management',
//                    'slug' => 'print-barcode',
//                ],
                [
                    'name' => 'Transaction History',
                    'guard_name' => 'Extra Management',
                    'slug' => 'transaction-history',
                ],
//                [
//                    'name' => 'Database Backup',
//                    'guard_name' => 'Extra Management',
//                    'slug' => 'database-backup',
//                ],
//                [
//                    'name' => 'Update Profile',
//                    'guard_name' => 'Extra Management',
//                    'slug' => 'update-profile',
//                ],

                // report permissions
                // [
                //     'name' => 'Balance Sheet',
                //     'guard_name' => 'Report View',
                //     'slug' => 'balance-sheet',
                // ],

                // [
                //     'name' => 'Summary Report',
                //     'guard_name' => 'Report View',
                //     'slug' => 'summary-report',
                // ],
                // [
                //     'name' => 'Profit/Loss',
                //     'guard_name' => 'Report View',
                //     'slug' => 'profit-loss',
                // ],
                // [
                //     'name' => 'Expense Report',
                //     'guard_name' => 'Report View',
                //     'slug' => 'expense-report',
                // ],
                [
                    'name' => 'Item Report',
                    'guard_name' => 'Report View',
                    'slug' => 'item-report',
                ],
                [
                    'name' => 'Inventory Report',
                    'guard_name' => 'Report View',
                    'slug' => 'inventory-report',
                ],

                // setup permissions
                [
                    'name' => 'General settings',
                    'guard_name' => 'Setup',
                    'slug' => 'general-settings',
                ],
                // [
                //     'name' => 'Mail Configuration',
                //     'guard_name' => 'Setup',
                //     'slug' => 'mail-configuration',
                // ],
//                [
//                    'name' => 'SMS Configuration',
//                    'guard_name' => 'Setup',
//                    'slug' => 'sms-configuration',
//                ],
                [
                    'name' => 'Permissions management',
                    'guard_name' => 'Setup',
                    'slug' => 'user-permission',
                ],
                [
                    'name' => 'Roles Management',
                    'guard_name' => 'Setup',
                    'slug' => 'user-role',
                ],
                [
                    'name' => 'Units management',
                    'guard_name' => 'Setup',
                    'slug' => 'units-management',
                ],
                [
                    'name' => 'Currencies Management',
                    'guard_name' => 'Setup',
                    'slug' => 'currencies-management',
                ],
                [
                    'name' => 'Brands Management',
                    'guard_name' => 'Setup',
                    'slug' => 'brands-management',
                ],
//                [
//                    'name' => 'Payment Method Management',
//                    'guard_name' => 'Setup',
//                    'slug' => 'payment-method-management',
//                ],
                [
                    'name' => 'GST Rate Management',
                    'guard_name' => 'Setup',
                    'slug' => 'vat-rate-management',
                ],

                // dashboard permissions
                [
                    'name' => 'Account Summery',
                    'guard_name' => 'Dashboard View',
                    'slug' => 'account-summery',
                ],
                [
                    'name' => 'Top Selling Products',
                    'guard_name' => 'Dashboard View',
                    'slug' => 'top-selling-products',
                ],
                [
                    'name' => 'Recent Activities',
                    'guard_name' => 'Dashboard View',
                    'slug' => 'recent-activities',
                ],
                [
                    'name' => 'Sales Activities',
                    'guard_name' => 'Dashboard View',
                    'slug' => 'sales-activities',
                ],
                [
                    'name' => 'Incharge Activities',
                    'guard_name' => 'Dashboard View',
                    'slug' => 'incharge-activities',
                ],
                // [
                //     'name' => 'Payment Sent vs Payment Received',
                //     'guard_name' => 'Dashboard View',
                //     'slug' => 'payment-sent-vs-payment-received',
                // ],
                // [
                //     'name' => 'Top Clients',
                //     'guard_name' => 'Dashboard View',
                //     'slug' => 'top-clients',
                // ],
                // [
                //     'name' => 'Stock Alert',
                //     'guard_name' => 'Dashboard View',
                //     'slug' => 'stock-alert',
                // ],
                // [
                //     'name' => 'Sales vs Purchases',
                //     'guard_name' => 'Dashboard View',
                //     'slug' => 'sales-vs-purchases',
                // ],

                //Insert Coupon Data Into Permission

                // [
                //     'name' => 'Create',
                //     'guard_name' => 'Coupon Management',
                //     'slug' => 'coupon-create',
                // ],
                // [
                //     'name' => 'List',
                //     'guard_name' => 'Coupon Management',
                //     'slug' => 'coupon-list',
                // ],
                // [
                //     'name' => 'Edit',
                //     'guard_name' => 'Coupon Management',
                //     'slug' => 'coupon-edit',
                // ],
                // [
                //     'name' => 'Delete',
                //     'guard_name' => 'Coupon Management',
                //     'slug' => 'coupon-delete',
                // ],

                // Insert Journal Entry Data Into Permission
                // [
                //     'name' => 'Create',
                //     'guard_name' => 'Journal Entry Management',
                //     'slug' => 'journal-entry-create',
                // ],
                // [
                //     'name' => 'List',
                //     'guard_name' => 'Journal Entry Management',
                //     'slug' => 'journal-entry-list',
                // ],
                // [
                //     'name' => 'Edit',
                //     'guard_name' => 'Journal Entry Management',
                //     'slug' => 'journal-entry-edit',
                // ],
                // [
                //     'name' => 'Delete',
                //     'guard_name' => 'Journal Entry Management',
                //     'slug' => 'journal-entry-delete',
                // ],

                // Insert Ledger Group Data Into Permission

                [
                    'name' => 'Create',
                    'guard_name' => 'Ledger Group Management',
                    'slug' => 'ledger-group-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Ledger Group Management',
                    'slug' => 'ledger-group-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Ledger Group Management',
                    'slug' => 'ledger-group-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Ledger Group Management',
                    'slug' => 'ledger-group-delete',
                ],

                // Insert Ledger Account Data Into Permission 

                [
                    'name' => 'Create',
                    'guard_name' => 'Ledger Account Management',
                    'slug' => 'ledger-account-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Ledger Account Management',
                    'slug' => 'ledger-account-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Ledger Account Management',
                    'slug' => 'ledger-account-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Ledger Account Management',
                    'slug' => 'ledger-account-delete',
                ],
                [
                    'name' => 'Ledger Transaction',
                    'guard_name' => 'Ledger Account Management',
                    'slug' => 'ledger-account-transaction',
                ],

            ]);
        }
    }
}
