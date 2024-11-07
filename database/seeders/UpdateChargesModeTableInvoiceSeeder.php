<?php

use Illuminate\Database\Seeder;

class UpdateChargesModeTableInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('charges_modes')->insert(array(
            array('id'=>3, 'charges_mode'=>'Invoicing')
        ));
    }
}
