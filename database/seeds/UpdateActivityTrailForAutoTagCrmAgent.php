<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForAutoTagCrmAgent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 474, 'screen_name' => 'CRM - Auto Tagging', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( 
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > CRM > Auto Tagging', 'url'=>'admin.settings.auto_tagging.index', 'permission_id' => 639)
        );
    }
}
