<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForBOTSMSSeeder extends Seeder
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
            array('id' => 145, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'BOT SMS Undelivered', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Consignee, your order having tracking number [tracking_number] is undelivered due to [status] [reason]. Kindly click on the link if this is incorrect: [link]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
