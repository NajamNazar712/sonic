<?php

use Illuminate\Database\Seeder;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->truncate();

        DB::table('modules')->insert(array(
            array('id' => 1, 'name' => 'Dispute'),
            array('id' => 2, 'name' => 'Shipper Accounts'),
            array('id' => 3, 'name' => 'Pickups'),
            array('id' => 4, 'name' => 'Cargo'),
            array('id' => 5, 'name' => 'Same-Day Delivery'),
            array('id' => 6, 'name' => 'Delivery'),
            array('id' => 7, 'name' => 'Return'),
            array('id' => 8, 'name' => 'Finance'),
            array('id' => 9, 'name' => 'Reports'),
            array('id' => 10, 'name' => 'Packaging Material'),
            array('id' => 11, 'name' => 'User Management'),
            array('id' => 12, 'name' => 'Network Management'),
            array('id' => 13, 'name' => 'Notification'),
            array('id' => 14, 'name' => 'Settings'),
            array('id' => 15, 'name' => 'Support')
        ));
    }
}
