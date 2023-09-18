<?php

use Illuminate\Database\Seeder;
use DB;
class UpdateMissingShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tracking_numbers = [15822328455044, 15827128455051];

        foreach ($tracking_numbers as $tracking_number){
            $shipment = DB::connection('gcp')->table('shipments')->where('tracking_number', $tracking_number)->first();
            dd($shipment);
        }

    }
}
