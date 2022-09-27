<?php

use Illuminate\Database\Seeder;

class UpdateNotificationSeederForBankAlfalahEmail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->insert(array(
            array('id' => 191, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Complaint Shippers CRM Close Reason Email', 'type_id' => 1, 'subject' => 'CRM Close Reasons', 'body' => 'Dear [Shipper name],' .PHP_EOL. 'Following are the CRM requests with Close Reasons.', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
