<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionWalkinBookingHistoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 209, 'name' => 'Walk-In Booking History - View', 'module_id' => 17),
        ));
    }
}
