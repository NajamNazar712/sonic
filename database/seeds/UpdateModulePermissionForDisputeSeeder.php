<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDisputeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 702, 'name' => 'Dispute - View', 'module_id' => 31),
            array('id' => 703, 'name' => 'Dispute Update- Action', 'module_id' => 31),
            array('id' => 704, 'name' => 'Add Dispute - Action', 'module_id' => 31),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 521, 'screen_name' => 'V2 Dispute', 'action'=> 'View'),
            array('id' => 522, 'screen_name' => 'V2 Dispute', 'action'=> 'Excel Download')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > Dispute', 'url'=>'admin.human_resource.leave.index', 'permission_id' => 702)
        );
    }
}
