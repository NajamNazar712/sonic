<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReturnDeliveriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 566, 'name' => 'Return Deliveries - View', 'module_id' => 7),
        ));
    }
}
