<?php

use Illuminate\Database\Seeder;

class RiderTrackingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 446, 'name' => 'Rider Tracking - View', 'module_id' => 3)
        ));
    }
}
