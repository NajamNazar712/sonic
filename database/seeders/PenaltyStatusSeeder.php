<?php

use Illuminate\Database\Seeder;

class PenaltyStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('penalty_statuses')->truncate();

        DB::table('penalty_statuses')->insert(array(
            array('id' => 1, 'name' => 'Pending'),
            array('id' => 2, 'name' => 'Reject'),
            array('id' => 3, 'name' => 'Deduct'),
        ));
    }
}
