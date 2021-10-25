<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBulkSegment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 609, 'name' => 'Bulk Segment Tagging', 'module_id' => 2)
        ));
    }
}
