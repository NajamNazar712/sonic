<?php

use Illuminate\Database\Seeder;

class RcpAssignedShipmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rcp_assigned_shipment_statuses')->truncate();
        $time = \Carbon\Carbon::now();
        DB::table('rcp_assigned_shipment_statuses')->insert(array(
			array('id' => 1, 'name' => 'Assigned', 'created_at' => $time, 'updated_at' => $time),
			array('id' => 2, 'name' => 'Un Assigned', 'created_at' => $time, 'updated_at' => $time),
			array('id' => 3, 'name' => 'Re-attempt', 'created_at' => $time, 'updated_at' => $time),
			array('id' => 4, 'name' => 'Return Confirm', 'created_at' => $time, 'updated_at' => $time),
			array('id' => 5, 'name' => 'On Hold For Self Collection', 'created_at' => $time, 'updated_at' => $time),
			array('id' => 6, 'name' => 'Unresponsive', 'created_at' => $time, 'updated_at' => $time),
			array('id' => 7, 'name' => 'Intercept Requested', 'created_at' => $time, 'updated_at' => $time),
			array('id' => 8, 'name' => 'Intercept Approved', 'created_at' => $time, 'updated_at' => $time),
			array('id' => 9, 'name' => 'Intercept Rejected', 'created_at' => $time, 'updated_at' => $time)
        ));
    }
}
