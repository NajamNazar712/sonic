<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForZeroChargesReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('notifications')->insert(array(
            array('id' => 67, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Zero Charges Report', 'type_id' => 1, 'subject' => 'Zero Charges Report', 'body' => 'Dear Finance Team, Please note the following tracking numbers have zero charges:' . PHP_EOL . PHP_EOL .'[zero_report]', 'updated_by' => 3, 'status' => 1)
        ));
    }
}
