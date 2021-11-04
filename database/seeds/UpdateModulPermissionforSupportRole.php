<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\AdminRole;

class UpdateModulPermissionforSupportRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_roles = AdminRole::all();
        foreach($admin_roles as $admin_role)
        {
            DB::table('admin_role_module_permissions')->insert(array(
                array('role_id' => $admin_role->id, 'permission_id' => 621),
                array('role_id' => $admin_role->id, 'permission_id' => 622)
            ));
        }
    }
}
