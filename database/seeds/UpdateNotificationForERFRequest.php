<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForERFRequest extends Seeder
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
            array('id' => 133, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'ERF Request', 'type_id' => 1, 'subject' => 'ERF  [erf_id]', 'body' => 'Dear Concerns,' . PHP_EOL . 'This is an automated email to inform about the request for employee requisition ([erf_id]), submitted   by [admin] at [date].' .PHP_EOL. '[link]', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
