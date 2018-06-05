<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\StandardInsuranceCharge;

class StandardInsuranceChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_insurance_charges')->truncate();
        DB::table('standard_insurance_charges')->insert(array(
            array('shipping_mode_id'=>1,'range_up'=>0,'range_down'=>1500,'charges'=>50),
            array('shipping_mode_id'=>1,'range_up'=>1501,'range_down'=>3000,'charges'=>100),
            array('shipping_mode_id'=>1,'range_up'=>3001,'range_down'=>10000,'charges'=>'3%'),
            array('shipping_mode_id'=>2,'range_up'=>0,'range_down'=>1500,'charges'=>50),
            array('shipping_mode_id'=>2,'range_up'=>1501,'range_down'=>3000,'charges'=>100),
            array('shipping_mode_id'=>2,'range_up'=>3001,'range_down'=>10000,'charges'=>'3%'),
            array('shipping_mode_id'=>3,'range_up'=>0,'range_down'=>1500,'charges'=>50),
            array('shipping_mode_id'=>3,'range_up'=>1501,'range_down'=>3000,'charges'=>100),
            array('shipping_mode_id'=>3,'range_up'=>3001,'range_down'=>10000,'charges'=>'3%'),
            array('shipping_mode_id'=>4,'range_up'=>0,'range_down'=>1500,'charges'=>50),
            array('shipping_mode_id'=>4,'range_up'=>1501,'range_down'=>3000,'charges'=>100),
            array('shipping_mode_id'=>4,'range_up'=>3001,'range_down'=>10000,'charges'=>'3%'),
        ));
    }
}
