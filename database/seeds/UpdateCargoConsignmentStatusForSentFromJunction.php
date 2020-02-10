<?php

use Illuminate\Database\Seeder;

class UpdateCargoConsignmentStatusForSentFromJunction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_consignment_status')->insert(array(
            array('id' => 9, 'name' => 'Sent From Junction')
        ));
    }
}
