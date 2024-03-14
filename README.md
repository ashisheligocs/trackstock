# Trackh v4.0.0

## Running Demo Seeder

```shell
php artisan migrate:fresh --seed --seeder=DemoDatabaseSeeder
```

## For Production Setup

1. Remove installed and verified files from storage folder
2. Remove the landing page route from web.php file
3. Remove the access from login page
4. Remove alert from dashboard page
5. Comment out database backup feature
6. Comment out profile update feature
7. Comment out setup general settings feature
8. Comment out setup Email settings feature
9. Comment out setup SMS settings feature
10. Comment out Role create & edit SMS settings feature
11. Comment out Email and SMS sending feature from Purchase show page, Invoice show page, Quotation show page
12. Remove disabled="true" from client create page, supplier create page, invoice create page, purchase create page, quotation create page, invoice payment page, purchase payment page, client create modal, supplier create modal, invoice payment modal, purchase payment modal

## For installer

1) Add "notVerified" middleware in package web routes
2) Add welcome path in package web routes
3) Add withSuccess in EnvironmentController "saveClassic" method
4) Add JWT secret in Environment Helper


### Please follow the below steps to run the application for import functionality


1. Create `demo-csv-file` folder in the `public` directory of the project
2. Create these empty files in the `demo-csv-file` folder
    * `brand.csv`
    * `sub-categories.csv`
    * `taxes.csv`
    * `units.csv`
3. Provide these two files in the `demo-csv-file` folder for examples
    * `products.csv` (name, model, barcode_symbology, sub_cat_id, brand_id, unit_id, tax_id, tax_type, regular_price, discount, note, alert_qty, status )
    * `demo.csv` (name, phone, email, company_name, address)
