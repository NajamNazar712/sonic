<?php

use Illuminate\Database\Seeder;

class ModulePermissionForRiderRemarks extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 862, 'name' => 'Rider Remarks - View', 'module_id' => 33),
        )); 

        DB::table('module_permissions')->insert(array(
            array('id' => 863, 'name' => 'Rider Remarks (Initial Response) - Action', 'module_id' => 33),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 864, 'name' => 'Rider Remarks (In Process) - Action', 'module_id' => 33),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 865, 'name' => 'Rider Remarks (Final Response) - Action', 'module_id' => 33),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 866, 'name' => 'Rider Remarks (Resolved) - Action', 'module_id' => 33),
        ));

    
    }
}
