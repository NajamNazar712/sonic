<?php

use Illuminate\Database\Seeder;

class PickupReminderPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 583, 'name' => 'Pickup Reminder Permission', 'module_id' => 3),
        ));
    }
}
