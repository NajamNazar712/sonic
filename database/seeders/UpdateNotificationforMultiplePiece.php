<?php

use Illuminate\Database\Seeder;

class UpdateNotificationforMultiplePiece extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->where('id', 154)->delete();
        DB::table('notifications')->insert(array(
            array('id' => 154, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Multiple Piece Update Email', 'type_id' => 1, 'subject' => 'Multiple Pieces Hold Shipment(s)', 'body' => 'Dear [Sales_Person],' . PHP_EOL . ' Following shipment [shipment_no] of shipper [shipper_name] is hold in operations.', 'updated_by' => 3, 'status' => 1),
        ));
    }
}
