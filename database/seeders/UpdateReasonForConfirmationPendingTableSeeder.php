<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateReasonForConfirmationPendingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 42, 'name' => 'Refused after opening the shipment')
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 42)
        ));
    }
}
