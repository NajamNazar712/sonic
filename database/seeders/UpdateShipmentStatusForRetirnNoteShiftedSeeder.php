<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusForRetirnNoteShiftedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 57, 'code' => 'RN-S', 'name' => 'Return Note Shifted', 'description' => 'Shipment is being shifted to another return note')
        ));
    }
}
