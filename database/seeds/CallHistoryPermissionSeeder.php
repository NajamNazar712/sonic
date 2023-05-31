<?php

use Illuminate\Database\Seeder;

class CallHistoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 867, 'name' => 'Tracking Call History', 'module_id' => 19),
        ));
    }
}
