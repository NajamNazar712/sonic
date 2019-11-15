<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonForConfirmationPending extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 40, 'name' => 'Wrong destination'),
            array('id' => 41, 'name' => 'Purchased from other vendor')
        ));
    }
}
