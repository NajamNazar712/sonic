<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForCemShipperAndConsigneeSMSTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp =  \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 176, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM Comment SMS', 'type_id' => 2, 'subject' => '', 'body' => 'Dear [name],' . PHP_EOL . 'Comment against CRM Request ID [crm_request_id] '. PHP_EOL .' [comment].', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
