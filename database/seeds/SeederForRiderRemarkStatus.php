<?php

use Illuminate\Database\Seeder;

class SeederForRiderRemarkStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rider_remark_statuses')->insert(array(
            array('id' => 1, 'name' => 'In Process'),
            array('id' => 2, 'name' => 'Resolved'),
        )); 
    }
}
