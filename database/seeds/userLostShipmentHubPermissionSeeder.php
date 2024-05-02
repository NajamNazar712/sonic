<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserLostShipmentHubPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 979, 'name' => 'User Lost Shipment Hub - Action', 'module_id' => 6),
        ));

    }
}
