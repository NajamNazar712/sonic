<?php

use Illuminate\Database\Seeder;

class UpdateCargoConsignmentStatusTableAddLost extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_consignment_status')->insert(array(
            array('id' => 8, 'name' => 'Lost')
        ));
    }
}
