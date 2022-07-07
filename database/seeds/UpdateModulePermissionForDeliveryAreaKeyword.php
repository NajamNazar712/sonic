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
        DB::table('module_permissions')->insert(array(
            array('id' => 747, 'name' => 'Delivery Area Keyword - View', 'module_id' => 14),
            array('id' => 748, 'name' => 'Delivery Area Keyword - Add', 'module_id' => 14),
            array('id' => 749, 'name' => 'Delivery Area Keyword - Edit/Disable', 'module_id' => 14)
        ));
        
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 561, 'screen_name' => 'Delivery Area Keyword', 'action'=> 'View')
        ));
    }
}
