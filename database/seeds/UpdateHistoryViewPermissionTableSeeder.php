<?php

use Illuminate\Database\Seeder;

class UpdateHistoryViewPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 123, 'name' => 'Pickup History - View', 'module_id' => 3),
            array('id' => 124, 'name' => 'Cargo History - View', 'module_id' => 4),
            array('id' => 125, 'name' => 'Delivery History - View', 'module_id' => 6),
            array('id' => 126, 'name' => 'Return History - View', 'module_id' => 7),
        ));
    }
}
