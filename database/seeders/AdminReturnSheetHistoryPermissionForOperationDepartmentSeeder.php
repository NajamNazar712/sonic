<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\AdminRoleModulePermission;

class AdminReturnSheetHistoryPermissionForOperationDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_ids = [3, 8, 9, 10, 11, 12, 15, 18, 19, 23, 33, 46, 55, 65, 72, 76, 84, 89, 91, 93 ,95 ,96 ,125];

        foreach ($role_ids as $role_id ) {

            $departmentPermission = new AdminRoleModulePermission();

            $departmentPermission->role_id = $role_id;
            $departmentPermission->permission_id = 885;
            $departmentPermission->save();
        }

    }
}
