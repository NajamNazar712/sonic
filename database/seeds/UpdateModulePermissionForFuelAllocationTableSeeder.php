<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForFuelAllocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 831, 'name' => 'Rider Fuel Allocation - View', 'module_id' => 28),
            array('id' => 832, 'name' => 'Rider Fuel Allocation - Allocate Fuel', 'module_id' => 28)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 632, 'screen_name' => 'Rider Fuel Allocation', 'action'=> 'View'),
            array('id' => 633, 'screen_name' => 'Rider Fuel Allocation', 'action'=> 'Excel Download'),
            array('id' => 634, 'screen_name' => 'Rider Fuel Allocation', 'action'=> 'Allocate Fuel'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
            DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Rider Fuel Allocation', 'url'=>'admin.human_resource.fuel_allocation.index', 'permission_id' => 831)
        );
    }
}
