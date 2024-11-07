<?php

use Illuminate\Database\Seeder;

class AddRiderDeliveryNoteRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
        DB::table('rider_delivery_note_request_statuses')->insert(array(
            array('id' => 1, 'name' => 'Approve', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 2, 'name' => 'Reject', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 3, 'name' => 'Already Created', 'created_at' => $now, 'updated_at' => $now),
        ));

        DB::table('rider_delivery_note_request_shipment_statuses')->insert(array(
            array('id' => 1, 'name' => 'Created', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 2, 'name' => 'Reject', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 3, 'name' => 'Already Created', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 4, 'name' => 'Added by Staff', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 5, 'name' => 'Removed by Staff', 'created_at' => $now, 'updated_at' => $now),
        ));
    }
}
