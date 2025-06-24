<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CxsEtdCitiesListViewUpdate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 1034, 'name' => 'Cx City List - View', 'module_id' => 14),
            array('id' => 1035, 'name' => 'Cx City List - Update', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 827, 'screen_name' => 'Cx City', 'action' => 'View'),
            array('id' => 828, 'screen_name' => 'Cx City ', 'action' => 'Update'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Network Management > Cx City List', 'url' => 'admin.managenemt.cx_city_list.index', 'permission_id' => 1034),
        ));
    }
}
