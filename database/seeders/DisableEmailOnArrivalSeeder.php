<?php

namespace Database\Seeders;

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisableEmailOnArrivalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //Permission and Activity Trail
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 978, 'name' => 'Disable Email On Arrival Status - View', 'module_id' => 14),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 783, 'screen_name' => 'Disable Email On Arrival Status', 'action' => 'View'),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 784, 'screen_name' => 'Disable Email On Arrival Status', 'action' => 'Update'),
        ));
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Disable Email On Arrival Status', 'url'=>'admin.settings.disable_email_on_arrival.index', 'permission_id' => 978),
        ));


        //Records in Global Settings Table
        GlobalSettings::create(
            [
            'setting_value' => 0,
            'type' => 'disable_email_on_arrival_all_shippers_except',
            'text' => ''
            ]
        );
        
        GlobalSettings::create(
            [
            'setting_value' => 1,
            'type' => 'disable_email_on_arrival_only_shippers',
            'text' => ''
            ]
        );
    }
}