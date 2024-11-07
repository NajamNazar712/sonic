<?php

use Illuminate\Database\Seeder;

class SackBagStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sack_bag_statuses')->insert(array(
            array('id' => 1, 'name' => 'IS',  'reference_id'  => 0,'reference_type' => null, 'desc' => null),
            array('id' => 2, 'name' => 'CB',  'reference_id'  => 1, 'reference_type' => 'BJS', 'desc' => 'Bag Journey Status'),
            array('id' => 3, 'name' => 'TM',  'reference_id'  => 2, 'reference_type' => 'BJS', 'desc' => 'Bag Journey Status'),
            array('id' => 4, 'name' => 'BR',  'reference_id'  => 7, 'reference_type' => 'BJS', 'desc' => 'Bag Journey Status'),
            array('id' => 5, 'name' => 'SDM', 'reference_id'  => 4, 'reference_type' => 'SJS', 'desc' => 'Shipment Journey Status')
        ));
    }
}
