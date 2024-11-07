<?php

use Illuminate\Database\Seeder;

class ActivityTrailForCrmAutoAssigning extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 872, 'name' => 'CRM - Auto Assigning Global Permission', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 661, 'screen_name' => 'CRM - Auto Assigning - ADD', 'action'=> 'View'),
          
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > CRM > Auto Assigning > Add', 'url'=>'admin.settings.auto_assigning.add', 'permission_id' => 617),           
        ));

    }
}
