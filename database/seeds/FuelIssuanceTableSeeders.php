<?php

use Illuminate\Database\Seeder;

class FuelIssuanceTableSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('card_holder_types')->truncate();
        DB::table('card_holder_types')->insert(array(
            array('id' => 1 ,'name'=>'Staff'),
            array('id' => 2 ,'name'=>'Rider'),
            array('id' => 3 ,'name'=>'Fleet'),
        ));

        DB::table('fuel_card_request_types')->truncate();
        DB::table('fuel_card_request_types')->insert(array(
            array('id' => 1 ,'name'=>'New'),
            array('id' => 2 ,'name'=>'Reassign'),
            array('id' => 3 ,'name'=>'Block'),
            array('id' => 4 ,'name'=>'Unblock'),
        ));

        DB::table('fuel_types')->truncate();
        DB::table('fuel_types')->insert(array(
            array('id' => 1 ,'name'=>'Diesel'),
            array('id' => 2 ,'name'=>'Petrol'),
        ));

        DB::table('fuel_deduction_types')->truncate();
        DB::table('fuel_deduction_types')->insert(array(
            array('id' => 1 ,'name'=>'Amount'),
            array('id' => 2 ,'name'=>'Daily Litre'),
            array('id' => 3 ,'name'=>'Monthly Litre'),
        ));

        DB::table('fleet_vehicle_types')->truncate();
        DB::table('fleet_vehicle_types')->insert(array(
            array('id' => 1 ,'name'=>'Office'),
            array('id' => 2 ,'name'=>'External'),
            array('id' => 3 ,'name'=>'Internal'),
        ));
    }
}
