<?php

use Illuminate\Database\Seeder;
use \App\Http\Models\Admin\AdminRoleModulePermission;

class RemovePermissionsFromOperations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_ids = [85,84,78,76,72,65,55,46,33,25,23,19,18,3,8,9,10,11,15];
        $permission_ids = [531, 533, 538];
        AdminRoleModulePermission::whereIn('role_id',$role_ids)->whereIn('permission_id',$permission_ids)->delete();
    }
}
