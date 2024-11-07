<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateModulePermissionsForRetailCompletedDeliveries extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 424, 'name' => 'Completed Deliveries Retail - View', 'module_id' => 26)
        ));
    }
}
