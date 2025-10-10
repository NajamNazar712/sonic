<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class BAHLSeparateMsgNotification extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $timestamp = \Carbon\Carbon::now();

        $timestamp = now(); // Or any valid timestamp

        DB::table('notifications')->insert(array(
            array(
                'id' => 244 ,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Shipment Out for Delivery for Consignee - Bank Al Habib LHE & KHI (Cards)',
                'type_id' => 2,
                'subject' => null,
                'body' => 'Dear BAHL Customer,' . PHP_EOL . PHP_EOL .'Your Debit Card is on the way through SLGTrax Courier. Please keep CNIC ready for due verification with rider.' . PHP_EOL .  'Consignment # [tracking_number]' . PHP_EOL . 'Rider: [rider_number]' . PHP_EOL . 'Trax Code: [otp]' . PHP_EOL . 'UAN 021111-118-729',
                'updated_by' => 3756,
                'status' => 1
            )
        ));

        DB::table('notifications')->insert(array(
            array(
                'id' => 245 ,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Arrival of shipment for Consignee - Bank Al Habib LHE & KHI (Cards)',
                'type_id' => 2,
                'subject' => null,
                'body' => 'Dear BAHL Customer,' . PHP_EOL . PHP_EOL .'Your Debit Card has been dispatched through SLGTrax Courier against consignment # [tracking_number]. Expected delivery time is 7 days.' ,
                'updated_by' => 3756,
                'status' => 1
            )
        ));
    }
}
