<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderTracking extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('module_permissions')->insert(array(
            array('id' => 447, 'name' => 'Rider Request - View', 'module_id' => 3),
        ));
    }
}
