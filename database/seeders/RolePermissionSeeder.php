<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // check if table is empty
        if (DB::table('role_permission')->count() == 0) {
            $roles = DB::table('roles')->get();
            $permissions = DB::table('permissions')->get();
            foreach ($roles as $role) {
                foreach ($permissions as $permission) {
                    if($role->slug == 'incharge'){
                        if($permission->guard_name == 'shop Management' || 
                           $permission->guard_name == 'Balance Transfer Management' || 
                           $permission->guard_name == 'Ledger Account Management' || 
                           $permission->guard_name == 'Ledger Group Management' ||
                           $permission->guard_name == 'Report View' ||
                           $permission->guard_name == 'Setup') {
                                DB::table('role_permission')->insert([
                                    'role_id' => $role->id,
                                    'permission_id' => $permission->id,
                                ]);    
                           }
                    } else {
                        DB::table('role_permission')->insert([
                            'role_id' => $role->id,
                            'permission_id' => $permission->id,
                        ]);
                    }
                    
                }
            }
        }
    }
}
