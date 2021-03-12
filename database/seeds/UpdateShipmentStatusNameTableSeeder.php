<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusNameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->where('id', 56)->update(['name' => 'Replacement - Not Collected']);
        DB::table('shipment_status')->where('id', 12)->update(['name' => 'Shipment - Return Confirmation Pending']);
    }
}
