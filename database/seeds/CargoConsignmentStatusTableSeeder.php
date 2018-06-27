<?php

use Illuminate\Database\Seeder;

class CargoConsignmentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_consignment_status')->truncate();

        DB::table('cargo_consignment_status')->insert(array(
            array('id' => 1, 'name' => 'In Transit'),
            array('id' => 2, 'name' => 'Received at Junction'),
            array('id' => 3, 'name' => 'Received'),
            array('id' => 4, 'name' => 'Dispute')
        ));
    }
}
