<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceAdjustmentReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('invoice_adjustment_reasons')->insert(array(
            array('id' => 1, 'name' => 'WHT Tax Deduction'),
            array('id' => 2, 'name' => 'Sales Tax Withholding'),
            array('id' => 3, 'name' => 'Weight Issue'),
            array('id' => 4, 'name' => 'Shipment Issue'),
            array('id' => 5, 'name' => 'Operational Disorder'),
            array('id' => 6, 'name' => 'Rate Issue'),
            array('id' => 7, 'name' => 'Fuel surcharge charges'),
            array('id' => 8, 'name' => 'Dispute'),

        ));
    }
}
