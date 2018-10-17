<?php

use App\Http\Models\Admin\ModulePermission;

use Illuminate\Database\Seeder;

class UpdateModulePermissionMarkedtoConfirmationPendingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module_permission = ModulePermission::find(44);

        $module_permission->name = 'Confirmation Pending - View';

        $module_permission->save();


        $module_permission = ModulePermission::find(45);

        $module_permission->name = 'Confirmation Pending - Confirm';

        $module_permission->save();


        $module_permission = ModulePermission::find(46);

        $module_permission->name = 'Confirmation Pending - Re-Attempt';

        $module_permission->save();
    }
}
