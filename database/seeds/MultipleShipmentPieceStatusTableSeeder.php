<?php

use Illuminate\Database\Seeder;

class MultipleShipmentPieceStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_multiple_piece_statuses')->truncate();
        DB::table('shipment_multiple_piece_statuses')->insert(array(
            array('id' => 1,'name' => 'Switch to Single Piece'),
            array('id' => 2,'name' => 'Wait for Remaining Pieces'),
            array('id' => 3,'name' => 'Return Back to the Shipper'),
        ));
    }
}
