<?php

use Illuminate\Database\Seeder;

class UpdateCargoConsignmentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_consignment_status')->insert(array(
                array('id' => 5, 'name' => 'Cancelled'),
        ));
    }
}
