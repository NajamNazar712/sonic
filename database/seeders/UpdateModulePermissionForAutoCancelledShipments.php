<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAutoCancelledShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module = \App\Http\Models\Admin\ModulePermission::where('id',660)->first();
        $module->module_id = 14;
        $module->update();
    }
}
