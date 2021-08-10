<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForRetailDonePaymentReportTableSeeder extends Seeder
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
            array('id' => 141, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Retail Done Payment Report Email', 'type_id' => 1, 'subject' => 'Retail Done Payment Report [date]', 'body' => '[preview]' . PHP_EOL . PHP_EOL .'Please download the report from the following link: [link].', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
