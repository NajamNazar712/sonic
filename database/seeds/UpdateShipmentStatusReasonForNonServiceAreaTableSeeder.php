<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonForNonServiceAreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = \App\Http\Models\ShipmentStatus::find(10);
        if($status){
            $status->status = 0;
            $status->save();
        }
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 34, 'name' => 'Non-Service Area')
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 34)
        ));
    }
}
