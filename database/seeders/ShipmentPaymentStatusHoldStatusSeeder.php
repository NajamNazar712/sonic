<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentPaymentStatusHoldStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_payment_status')->insert([
            'id' => 13,
            'code' => 'P-H',
            'name' => 'Payment - Hold',
            'description' => 'Payment for the shipment is held by TRAX'
        ]);
    }

}
