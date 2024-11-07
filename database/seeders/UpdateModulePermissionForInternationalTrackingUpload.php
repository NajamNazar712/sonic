<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInternationalTrackingUpload extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 398, 'name' => 'International Tracking Upload - View', 'module_id' => 17),
        ));
    }
}
