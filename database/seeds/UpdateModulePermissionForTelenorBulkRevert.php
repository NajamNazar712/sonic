<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForTelenorBulkRevert extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 595, 'name' => 'Telenor Bulk Revert - View', 'module_id' => 7),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 435, 'screen_name' => 'Telenor Bulk Revert', 'action'=> 'View'),
        ));
    }
}
