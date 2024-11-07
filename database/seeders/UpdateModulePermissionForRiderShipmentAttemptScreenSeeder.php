<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderShipmentAttemptScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 562, 'name' => 'Rider Shipments Attempt Setting - View', 'module_id' => 14),
        ));
    }
}
