<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusMonthClosingToClosedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->where('id', 51)->update(['code' => 'S-CC', 'name' => 'Shipment - Case Closed']);
    }
}
