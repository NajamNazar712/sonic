<?php

use Illuminate\Database\Seeder;

class Seeder4617ForPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 669, 'name' => 'Packers & Movers Lead - View', 'module_id' => 25),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 489, 'screen_name' => 'Packers & Movers Lead', 'action'=> 'View'),
            array('id' => 490, 'screen_name' => 'Packers & Movers Lead', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Packers & Movers Lead', 'url'=>'admin.pam_leads.index', 'permission_id' => 669));
    }
}
