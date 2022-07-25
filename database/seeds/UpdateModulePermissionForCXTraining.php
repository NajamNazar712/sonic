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

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

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

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 568, 'screen_name' => 'CX Training', 'action'=> 'View'),
            array('id' => 569, 'screen_name' => 'CX Training', 'action'=> 'Excel Download'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > CX Training', 'url'=>'admin.qa.cx_training.index', 'permission_id' => 777),
        ));
    }
}
