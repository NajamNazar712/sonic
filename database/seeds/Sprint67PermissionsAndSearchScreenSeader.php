<?php

use Illuminate\Database\Seeder;

class Sprint67PermissionsAndSearchScreenSeader extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > International > International Rate Excel Upload', 'url'=>'admin.settings.international_rates.upload.index', 'permission_id' => 477),
        ));

        DB::table('modules')->insert(array(
            array('id' =>28, 'name' => 'Human Resource'),
        ));

        DB::table('module_permissions')->whereIn('id',[449,465,467,468,469,478,479,480,481,482,483,484,485])->update(["module_id"=>28]);
    }
}
