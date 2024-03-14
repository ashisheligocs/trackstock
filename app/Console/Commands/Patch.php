<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Client;

class Patch extends Command{

    const PATCH_VERSION_0_1 = '0.1';
    const PATCH_VERSION_0_2 = '0.2';
    const PATCH_VERSION_0_3 = '0.3';
    const PATCH_VERSION_0_4 = '0.4';
    
    /**
     * The Name and Signature of the console command
     * 
     * @var string
    */

    protected $signature = 'apply:patch {ver} {--rollback}';
    /**
     * The console command description 
     * 
     * @var string
     */
    
    
    protected $description = 'Apply Patch';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute Console Command
     * @return int
    */

    public function handle(){
        switch($this->argument('ver')){
            case self::PATCH_VERSION_0_1:
                $this->patch01();
            break;
            case self::PATCH_VERSION_0_2:
                $this->patch02();
            break;
            case self::PATCH_VERSION_0_3:
                $this->patch03();
            break;
            case self::PATCH_VERSION_0_4:
                $this->patch04();
            break;
        }
    }

    public function patch01(){
        if($this->option('rollback')){
            /*Rollback Logic Put Here*/
            return;
        }

        if (DB::table('permissions')->count()) {
            DB::table('permissions')->insert([

                //Insert Coupon Data Into Permission

                [
                    'name' => 'Create',
                    'guard_name' => 'Coupon Management',
                    'slug' => 'coupon-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Coupon Management',
                    'slug' => 'coupon-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Coupon Management',
                    'slug' => 'coupon-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Coupon Management',
                    'slug' => 'coupon-delete',
                ],

                // Insert Journal Entry Data Into Permission
                [
                    'name' => 'Create',
                    'guard_name' => 'Journal Entry Management',
                    'slug' => 'journal-entry-create',
                ],
                [
                    'name' => 'List',
                    'guard_name' => 'Journal Entry Management',
                    'slug' => 'journal-entry-list',
                ],
                [
                    'name' => 'Edit',
                    'guard_name' => 'Journal Entry Management',
                    'slug' => 'journal-entry-edit',
                ],
                [
                    'name' => 'Delete',
                    'guard_name' => 'Journal Entry Management',
                    'slug' => 'journal-entry-delete',
                ],
            ]);
        }

        //Set Super Admin Permission for All
        
        if (DB::table('user_permission')->count()) {
            $permissions = DB::table('permissions')->get();
            foreach ($permissions as $permission) {
                DB::table('user_permission')->updateOrInsert([
                    'user_id' => 1,
                    'permission_id' => $permission->id,
                ]);

                DB::table('role_permission')->updateOrInsert(
                    [
                        'role_id' => 1,
                        'permission_id' => $permission->id,
                    ],
                    [
                        'role_id' => 2,
                        'permission_id' => $permission->id,
                    ],
            );
                
            }
        }
    }

    public function patch02(){
        if($this->option('rollback')){
            /*Rollback Logic Put Here*/
            return;
        }

        if (DB::table('permissions')->count()) {

            DB::table('permissions')->insert([
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

        //Set Super Admin Permission for All
        
        if (DB::table('user_permission')->count()) {
            $permissions = DB::table('permissions')->get();
            foreach ($permissions as $permission) {
                DB::table('user_permission')->updateOrInsert([
                    'user_id' => 1,
                    'permission_id' => $permission->id,
                ]);

                DB::table('role_permission')->updateOrInsert(
                    [
                        'role_id' => 1,
                        'permission_id' => $permission->id,
                    ],
                    [
                        'role_id' => 2,
                        'permission_id' => $permission->id,
                    ],
            );
                
            }
        }

    }

    /*Update User Id In Customer Table*/
    public function patch03(){
        $users = User::get();
        if(!empty($users)){
            foreach($users as $user){
                $getClient = Client::where('email',$user->email)->where('name',$user->name)->first();
                if(!empty($getClient)){
                    $getClient->user_id = $user->id;
                    $getClient->save();
                }
            }
        }
    }

    /*Insert GST*/

    public function patch04(){
        DB::table('general_settings')->insert([
            [
                'key' => 'gst_number',
                'display_name' => 'GST Number',
                'value' => '',
            ],
        ]);
    }
}
?>