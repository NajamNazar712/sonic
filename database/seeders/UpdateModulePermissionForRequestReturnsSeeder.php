<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRequestReturnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 635, 'screen_name' => 'Pending Return Note Requests', 'action'=> 'View'),
            array('id' => 636, 'screen_name' => 'Pending Return Note Requests', 'action'=> 'Excel Download')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Return > Pending Return Note Requests', 'url'=>'admin.return.rider_request.index', 'permission_id' => 48)
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 833, 'name' => 'Pending Return Note Requests - Approve', 'module_id' => 7),
            array('id' => 834, 'name' => 'Pending Return Note Requests - Reject', 'module_id' => 7),
            array('id' => 835, 'name' => 'Pending Return Note Requests - Edit', 'module_id' => 7),
        ));
    }
}
