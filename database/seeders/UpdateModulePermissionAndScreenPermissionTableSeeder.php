<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionAndScreenPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 692, 'name' => 'Red Alert Shippers - View', 'module_id' => 31),
            array('id' => 693, 'name' => 'Red Alert Shippers - Add', 'module_id' => 31),
            array('id' => 694, 'name' => 'Red Alert Shippers - Remove', 'module_id' => 31),
            array('id' => 695, 'name' => 'Red Alert Shippers - Edit', 'module_id' => 31),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 516, 'screen_name' => 'Red Alert Shippers', 'action'=> 'View'),
            array('id' => 517, 'screen_name' => 'Red Alert Shippers', 'action'=> 'Excel Download'),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > Red Alert Shippers', 'url'=>'admin.qa.high_alert.shippers.index', 'permission_id' => 692));
    }
}
