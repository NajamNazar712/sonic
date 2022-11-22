<?php

use Illuminate\Database\Seeder;

class UpdateModuleNotificationAndScreenSearchVigilanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 562, 'screen_name' => 'Vigilance Verification', 'action'=> 'View'),
            array('id' => 563, 'screen_name' => 'Vigilance Verification History', 'action'=> 'View'),
            array('id' => 564, 'screen_name' => 'Vigilance Verification History', 'action'=> 'Excel Download'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 770, 'name' => 'Vigilance Verification - View', 'module_id' => 33),
            array('id' => 771, 'name' => 'Vigilance Verification History - View', 'module_id' => 33),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        // DB::table('admins_screen_list')->insert(
        //     array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Vigilance Verification > Delivery Notes', 'url'=>'admin.vigilance.verification.index', 'permission_id' => 770),
        //     array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Vigilance Verification > History', 'url'=>'admin.vigilance.verification.history.index', 'permission_id' => 771)
        // );
    }
}
