<?php

use Illuminate\Database\Seeder;

class InvoiceStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('invoice_statuses')->truncate();

		DB::table('invoice_statuses')->insert(array(
			array('id' => 1, 'name' => 'Pending'),
			array('id' => 2, 'name' => 'Reminded'),
			array('id' => 3, 'name' => 'Received')
		));
    }
}
