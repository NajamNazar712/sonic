<?php

use Illuminate\Database\Seeder;

class V2DisputeStatusAndReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v2_dispute_reasons')->truncate();

        DB::table('v2_dispute_reasons')->insert(array(
            array('id' => 1, 'name' => 'AWB N/A'),
            array('id' => 2, 'name' => 'Improper AWB'),
            array('id' => 3, 'name' => 'Packaging Issues'),
            array('id' => 4, 'name' => 'Open receive at origin'),
            array('id' => 5, 'name' => 'Open received at destination'),
            array('id' => 6, 'name' => 'Open return'),
            array('id' => 7, 'name' => 'Others'),
        ));

        DB::table('v2_dispute_statuses')->truncate();

        DB::table('v2_dispute_statuses')->insert(array(
            array('id' => 1, 'name' => 'Launched'),
            array('id' => 2, 'name' => 'In Process'),
            array('id' => 3, 'name' => 'Resolved/Closed'),
        ));
    }
}
