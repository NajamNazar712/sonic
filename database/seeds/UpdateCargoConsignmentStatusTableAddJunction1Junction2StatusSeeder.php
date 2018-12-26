<?php

use Illuminate\Database\Seeder;

class UpdateCargoConsignmentStatusTableAddJunction1Junction2StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_consignment_status')->insert(array(
            array('id' => 6, 'name' => 'Received at Junction 1'),
            array('id' => 7, 'name' => 'Received at Junction 2')
        ));
    }
}
