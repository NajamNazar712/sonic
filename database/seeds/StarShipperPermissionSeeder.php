<?php

use Illuminate\Database\Seeder;

class StarShipperPermissionSeeder extends Seeder
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
            array('id' => 846, 'name' => 'Star Shippers - View', 'module_id' => 14),
            array('id' => 847, 'name' => 'Star Shipper (Add Request)', 'module_id' => 14),
            array('id' => 848, 'name' => 'Star Shipper (Enable/Disable Request) - Action', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 645, 'screen_name' => 'Star Shippers', 'action'=> 'View'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Setting > Shipment > Star Shipper', 'url'=>'admin.settings.star_shippers.index', 'permission_id' => 846),
        ));
       
    }
}
