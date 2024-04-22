<?php

use Illuminate\Database\Seeder;

class DeliveryRevertAccessPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 949, 'name' => 'Delivery Revert Access - Update', 'module_id' => 8),
        ));
    }
}
