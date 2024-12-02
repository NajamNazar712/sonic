<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsAddFinSms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->where('id', 234)->delete();
        DB::table('notifications')->insert(array(
            array('id' => 234, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Finja Api Sms', 'type_id' => 2, 'subject' => 'Wallet Notification', 'body' => '', 'updated_by' => 3, 'status' => 1),
        ));
    }
}
