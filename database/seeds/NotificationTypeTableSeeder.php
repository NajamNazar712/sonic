<?php

use Illuminate\Database\Seeder;

class NotificationTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('notification_types')->truncate();

        DB::table('notification_types')->insert(array(
			array('id' => 1, 'name' => 'Email'),
			array('id' => 2, 'name' => 'SMS')
        ));
    }
}
