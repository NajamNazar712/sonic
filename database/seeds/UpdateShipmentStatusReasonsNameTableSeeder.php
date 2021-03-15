<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonsNameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('shipment_status_reason')->where('id', 31)->update(['name' => 'Mistake In Booking']);
        DB::table('shipment_status_reason')->where('id', 32)->update(['name' => 'Consignee Did Not Handover Due To Product or Shipper Issue']);
        DB::table('shipment_status_reason')->where('id', 33)->update(['name' => 'Consignee Wants To Receive Both Shipments']);
        DB::table('shipment_status_reason')->where('id', 14)->update(['name' => 'Delivery Area Blocked/Closed/Restricted']);
        DB::table('shipment_status_reason')->where('id', 28)->update(['name' => 'Replacement Not Handed Over']);
        DB::table('shipment_status_reason')->where('id', 36)->update(['name' => 'Snatched By Consignee']);
        DB::table('shipment_status_reason')->where('id', 37)->update(['name' => 'Misplaced By Rider']);
        DB::table('shipment_status_reason')->where('id', 45)->update(['name' => 'No One Came For Self-Collection']);
        DB::table('shipment_status_reason')->where('id', 5)->update(['name' => 'Consignee Out Of City']);
        DB::table('shipment_status_reason')->where('id', 19)->update(['name' => 'No Such Consignee Found']);
        DB::table('shipment_status_reason')->where('id', 12)->update(['name' => 'Out Of Service Area']);
        DB::table('shipment_status_reason')->where('id', 23)->update(['name' => 'Due To Accident']);
        DB::table('shipment_status_reason')->where('id', 25)->update(['name' => 'Due To Uncertain Weather']);
        DB::table('shipment_status_reason')->where('id', 40)->update(['name' => 'Wrong Destination']);

        DB::table('shipment_status_reason')->insert(array(
            array('id' => 60, 'name' => 'Cash Unavailable'),
            array('id' => 61, 'name' => 'Theft/Robbery'),
        ));

    }
}
