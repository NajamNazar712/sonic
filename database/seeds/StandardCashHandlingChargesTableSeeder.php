<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\StandardCashHandlingCharge;
class StandardCashHandlingChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_cash_handling_charges')->insert(array(
            array('shipping_mode_id'=>1,'range_up'=>0,'range_down'=>3000,'charges'=>0),
            array('shipping_mode_id'=>1,'range_up'=>3001,'range_down'=>5000,'charges'=>100),
            array('shipping_mode_id'=>1,'range_up'=>5001,'range_down'=>10000,'charges'=>200),
            array('shipping_mode_id'=>1,'range_up'=>10001,'range_down'=>30000,'charges'=>300),
            array('shipping_mode_id'=>1,'range_up'=>30001,'range_down'=>500000,'charges'=>'1%'),
            array('shipping_mode_id'=>2,'range_up'=>0,'range_down'=>3000,'charges'=>0),
            array('shipping_mode_id'=>2,'range_up'=>3001,'range_down'=>5000,'charges'=>100),
            array('shipping_mode_id'=>2,'range_up'=>5001,'range_down'=>10000,'charges'=>200),
            array('shipping_mode_id'=>2,'range_up'=>10001,'range_down'=>30000,'charges'=>300),
            array('shipping_mode_id'=>2,'range_up'=>30001,'range_down'=>500000,'charges'=>'1%'),
            array('shipping_mode_id'=>3,'range_up'=>0,'range_down'=>3000,'charges'=>0),
            array('shipping_mode_id'=>3,'range_up'=>3001,'range_down'=>5000,'charges'=>100),
            array('shipping_mode_id'=>3,'range_up'=>5001,'range_down'=>10000,'charges'=>200),
            array('shipping_mode_id'=>3,'range_up'=>10001,'range_down'=>30000,'charges'=>300),
            array('shipping_mode_id'=>3,'range_up'=>30001,'range_down'=>500000,'charges'=>'1%'),
            array('shipping_mode_id'=>4,'range_up'=>0,'range_down'=>3000,'charges'=>0),
            array('shipping_mode_id'=>4,'range_up'=>3001,'range_down'=>5000,'charges'=>100),
            array('shipping_mode_id'=>4,'range_up'=>5001,'range_down'=>10000,'charges'=>200),
            array('shipping_mode_id'=>4,'range_up'=>10001,'range_down'=>30000,'charges'=>300),
            array('shipping_mode_id'=>4,'range_up'=>30001,'range_down'=>500000,'charges'=>'1%'),
        ));
    }
}
