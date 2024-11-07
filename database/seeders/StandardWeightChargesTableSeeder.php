<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\StandardWeightCharge;

class StandardWeightChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_weight_charges')->truncate();
        DB::table('standard_weight_charges')->insert(array(
            array('shipping_mode_id'=>1,'range_up'=>0.01,'range_down'=>0.5,'local_or_6hr'=>165,'national_or_sameday'=>180),
            array('shipping_mode_id'=>1,'range_up'=>0.51,'range_down'=>1,'local_or_6hr'=>180,'national_or_sameday'=>220),
            array('shipping_mode_id'=>2,'range_up'=>0,'range_down'=>10,'local_or_6hr'=>500,'national_or_sameday'=>500),
            array('shipping_mode_id'=>3,'range_up'=>0,'range_down'=>10,'local_or_6hr'=>450,'national_or_sameday'=>450),
            array('shipping_mode_id'=>4,'range_up'=>0.01,'range_down'=>0.25,'local_or_6hr'=>250,'national_or_sameday'=>250),
            array('shipping_mode_id'=>4,'range_up'=>0.251,'range_down'=>0.5,'local_or_6hr'=>275,'national_or_sameday'=>275),
            array('shipping_mode_id'=>4,'range_up'=>0.51,'range_down'=>1,'local_or_6hr'=>300,'national_or_sameday'=>300),

        ));
    }
}
