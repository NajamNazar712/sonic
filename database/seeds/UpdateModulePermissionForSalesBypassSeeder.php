<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSalesBypassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 681, 'name' => 'Sales User Restriction Bypass', 'module_id' => 14),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Setting > Sales > User Restriction', 'url'=>'admin.settings.sales.user_restriction.index', 'permission_id' => 681),
        ));
    }
}
