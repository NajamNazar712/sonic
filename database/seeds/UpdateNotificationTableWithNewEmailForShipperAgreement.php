<?php

use Illuminate\Database\Seeder;

class UpdateNotificationTableWithNewEmailForShipperAgreement extends Seeder
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
            array('id' => 149, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Notification For Shipper Agreement Signed', 'type_id' => 1, 'subject' => 'CRF acknowledgement [Shipper]', 'body' => 'Dear [person_of_contact],
This is to notify that [Shipper name] have make an acknowledgement over Trax Online (Pvt) Ltd. service agreement.', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
