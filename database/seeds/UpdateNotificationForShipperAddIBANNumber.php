<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForShipperAddIBANNumber extends Seeder
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
            array('id' => 91, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipper Verification IBAN Number Pin Code', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Shipper,' . PHP_EOL . PHP_EOL . 'Your request for new Bank Information has been received. Kindly find below and verify your 4 digit PIN through portal' . PHP_EOL . PHP_EOL . 'Your Pin for Add Bank is: [pin]' . PHP_EOL . PHP_EOL . 'Regards' . PHP_EOL . PHP_EOL . 'Team TRAX', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
