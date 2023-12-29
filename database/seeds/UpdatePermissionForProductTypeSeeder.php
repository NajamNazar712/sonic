<?php

use Illuminate\Database\Seeder;

class UpdatePermissionForProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('module_permissions')->insert(array(
            array('id' => 912, 'name' => 'Product Type Screen', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 710, 'screen_name' => 'Product Type Screen', 'action'=> 'View'),
            array('id' => 711, 'screen_name' => 'Product Type Screen', 'action'=> 'Excel Download'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp,
             'name' => 'Settings > Support > Product Types', 
             'url'=>'admin.settings.product_type.index', 'permission_id' => 912),
        ));
    }
}
