<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDeliveryAreaKeyword extends Seeder
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
            array('id' => 747, 'name' => 'Delivery Area Keyword - View', 'module_id' => 14),
            array('id' => 748, 'name' => 'Delivery Area Keyword - Add', 'module_id' => 14),
            array('id' => 749, 'name' => 'Delivery Area Keyword - Edit/Disable', 'module_id' => 14)
        ));
        
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 561, 'screen_name' => 'Delivery Area Keyword', 'action'=> 'View')
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Delivery Area Keyword', 'url'=>'admin.settings.delivery_area_keyword.index', 'permission_id' => 747),
        ));
    }
}
