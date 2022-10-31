<?php

use Illuminate\Database\Seeder;

class UpdateInvoiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('invoice_statuses')->insert(array(
          array('id' => 4, 'name' => 'Partial Received')
        ));

    }
}
