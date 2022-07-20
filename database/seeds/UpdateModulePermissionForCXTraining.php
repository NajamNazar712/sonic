<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCXTraining extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 777, 'name' => 'CX Training - View', 'module_id' => 31),
            array('id' => 778, 'name' => 'CX Training - Add', 'module_id' => 31),
            array('id' => 779, 'name' => 'CX Training - Update Status', 'module_id' => 31),
        ));

        DB::table('cx_training_units')->insert(array(
            array('id' => 1, 'name' => 'RCP'),
            array('id' => 2, 'name' => 'Incoming'),
            array('id' => 3, 'name' => 'CMU'),
            array('id' => 4, 'name' => 'Outreach'),
        ));
    }
}
