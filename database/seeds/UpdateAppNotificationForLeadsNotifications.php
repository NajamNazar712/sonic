<?php

use Illuminate\Database\Seeder;

class UpdateAppNotificationForLeadsNotifications extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('app_notifications')->insert(array(
            array('id' => 14, 'name' => 'New Lead Tagged', 'title' => 'New Lead Tagged [date]', 'body' => 'Dear [sale_person]'.PHP_EOL.'Following Lead has been tagged to you [lead_id].', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 15, 'name' => 'New Remark - Lead', 'title' => 'New Remark Added', 'body' => 'Dear [sale_person]'.PHP_EOL.'A new remark is added to the Lead [lead_id].', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
