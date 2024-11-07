<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDailyPickupCronTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 151, 'name' => 'Daily Pickup & Sales Cron Time', 'module_id' => 14)
        ));
    }
}
