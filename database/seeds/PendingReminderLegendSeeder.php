<?php

use Illuminate\Database\Seeder;

class PendingReminderLegendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v2_pickup_request_legends')->insert(array(
            array('id' => 9, 'name' => 'Pending Reminder', 'color' => '#FFFDD0'),

        ));
    }
}
