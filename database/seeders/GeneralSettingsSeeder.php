<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // check if table is empty
        if (DB::table('general_settings')->count() == 0) {

            // seed basic data to table
            $settingElements = [
                'company_name' => 'EligoCS',
                'company_tagline' => 'Ultimate Sales, Inventory, Accounting Management System',
                'email_address' => 'pawan@gmail.com',
                'phone_number' => '0170000000',
                'address' => '2nd Floor Heaven House, Near Petrol Pump, Vikasnagar, Shimla, 171009, Himachal Pradesh, India',
                'client_prefix' => 'CUST',     //'AC',
                'supplier_prefix' => 'SUPP',   //'AS',
                'employee_prefix' => 'EMP',    //'AE',
                'product_cat_prefix' => 'CAT', //'APC',
                'product_sub_cat_prefix' => 'SCAT', //'APS',
                'product_prefix' => 'PROD',     //'AP',
                'exp_cat_prefix' => 'EXPN',     //'AEC',
                'exp_sub_cat_prefix' => 'SEXP',  //'AES',
                'pur_prefix' => 'PUR',           //'APP',
                'pur_return_prefix' => 'PURR',    //'APR',
                'quotation_prefix' => 'APQ',
                'invoice_prefix' => 'INV',        //'AI',
                'invoice_return_prefix' => 'INVR', //'AIR',
                'adjustment_prefix' => 'ADJ',    //'AIA',
                'default_currency' => '1',
                'default_language' => 'en',
                'logo' => 'logo.png',
                'logo_black' => 'logo-black.png',
                'small_logo' => 'small-logo.png',
                'favicon' => 'favicon.png',
                'copyright' => '© Copyright 2023 EligoCS',
                'default_client_slug'=> 'walking-customer',
                'default_account_slug'=> 'cash-0001',
                'default_vat_rate_slug'=> 'vat-0',
                'selected_hotel' => '1',
                'booking_prefix' => 'BOOKED',
                'gst_number' => '',
            ];

            foreach ($settingElements as $key => $value) {
                DB::table('general_settings')->insert([
                    [
                        'key' => $key,
                        'display_name' => ucwords(str_replace('_', ' ', $key)),
                        'value' => $value,
                    ],
                ]);
            }
        }
    }
}