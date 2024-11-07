<?php

use Illuminate\Database\Seeder;

class CorporateAccountRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('corporate_standard_min_chargeable_weights')->truncate();
        DB::table('corporate_standard_min_chargeable_weights')->insert(array(
            array('shipping_mode_id'=>1, 'delivery_type_id' => 1, 'min_chargeable_weight'=>10),
            array('shipping_mode_id'=>1, 'delivery_type_id' => 2, 'min_chargeable_weight'=>10),
            array('shipping_mode_id'=>2, 'delivery_type_id' => 1, 'min_chargeable_weight'=>10),
            array('shipping_mode_id'=>2, 'delivery_type_id' => 2, 'min_chargeable_weight'=>10),
            array('shipping_mode_id'=>3, 'delivery_type_id' => 1, 'min_chargeable_weight'=>10),
            array('shipping_mode_id'=>3, 'delivery_type_id' => 2, 'min_chargeable_weight'=>10),
            array('shipping_mode_id'=>4, 'delivery_type_id' => 1, 'min_chargeable_weight'=>10),
            array('shipping_mode_id'=>4, 'delivery_type_id' => 2, 'min_chargeable_weight'=>10),

        ));
        DB::table('corporate_standard_weight_charges')->truncate();
        DB::table('corporate_standard_weight_charges')->insert(array(
            array('shipping_mode_id'=>1,'delivery_type_id' => 1,'range_up'=>0.01,'range_down'=>10,'local_or_6hr'=>50,'national_charges_class_0'=>50,'national_charges_class_1'=>50,'national_charges_class_2'=>50,'national_charges_class_3'=>50),
            array('shipping_mode_id'=>1,'delivery_type_id' => 1,'range_up'=>10.01,'range_down'=>1000,'local_or_6hr'=>50,'national_charges_class_0'=>50,'national_charges_class_1'=>50,'national_charges_class_2'=>50,'national_charges_class_3'=>50),
            array('shipping_mode_id'=>1,'delivery_type_id' => 2,'range_up'=>0.01,'range_down'=>10,'local_or_6hr'=>20,'national_charges_class_0'=>20,'national_charges_class_1'=>20,'national_charges_class_2'=>20,'national_charges_class_3'=>20),
            array('shipping_mode_id'=>1,'delivery_type_id' => 2,'range_up'=>10.01,'range_down'=>1000,'local_or_6hr'=>20,'national_charges_class_0'=>20,'national_charges_class_1'=>20,'national_charges_class_2'=>20,'national_charges_class_3'=>20),
            array('shipping_mode_id'=>2,'delivery_type_id' => 1,'range_up'=>0.01,'range_down'=>10,'local_or_6hr'=>50,'national_charges_class_0'=>50,'national_charges_class_1'=>50,'national_charges_class_2'=>50,'national_charges_class_3'=>50),
            array('shipping_mode_id'=>2,'delivery_type_id' => 1,'range_up'=>10.01,'range_down'=>1000,'local_or_6hr'=>50,'national_charges_class_0'=>50,'national_charges_class_1'=>50,'national_charges_class_2'=>50,'national_charges_class_3'=>50),
            array('shipping_mode_id'=>2,'delivery_type_id' => 2,'range_up'=>0.01,'range_down'=>10,'local_or_6hr'=>20,'national_charges_class_0'=>20,'national_charges_class_1'=>20,'national_charges_class_2'=>20,'national_charges_class_3'=>20),
            array('shipping_mode_id'=>2,'delivery_type_id' => 2,'range_up'=>10.01,'range_down'=>1000,'local_or_6hr'=>20,'national_charges_class_0'=>20,'national_charges_class_1'=>20,'national_charges_class_2'=>20,'national_charges_class_3'=>20),
            array('shipping_mode_id'=>3,'delivery_type_id' => 1,'range_up'=>0.01,'range_down'=>10,'local_or_6hr'=>50,'national_charges_class_0'=>50,'national_charges_class_1'=>50,'national_charges_class_2'=>50,'national_charges_class_3'=>50),
            array('shipping_mode_id'=>3,'delivery_type_id' => 1,'range_up'=>10.01,'range_down'=>1000,'local_or_6hr'=>50,'national_charges_class_0'=>50,'national_charges_class_1'=>50,'national_charges_class_2'=>50,'national_charges_class_3'=>50),
            array('shipping_mode_id'=>3,'delivery_type_id' => 2,'range_up'=>0.01,'range_down'=>10,'local_or_6hr'=>20,'national_charges_class_0'=>20,'national_charges_class_1'=>20,'national_charges_class_2'=>20,'national_charges_class_3'=>20),
            array('shipping_mode_id'=>3,'delivery_type_id' => 2,'range_up'=>10.01,'range_down'=>1000,'local_or_6hr'=>20,'national_charges_class_0'=>20,'national_charges_class_1'=>20,'national_charges_class_2'=>20,'national_charges_class_3'=>20),
            array('shipping_mode_id'=>4,'delivery_type_id' => 1,'range_up'=>0.01,'range_down'=>10,'local_or_6hr'=>50,'national_charges_class_0'=>50,'national_charges_class_1'=>0,'national_charges_class_2'=>0,'national_charges_class_3'=>0),
            array('shipping_mode_id'=>4,'delivery_type_id' => 1,'range_up'=>10.01,'range_down'=>1000,'local_or_6hr'=>50,'national_charges_class_0'=>50,'national_charges_class_1'=>0,'national_charges_class_2'=>0,'national_charges_class_3'=>0),
            array('shipping_mode_id'=>4,'delivery_type_id' => 2,'range_up'=>0.01,'range_down'=>10,'local_or_6hr'=>20,'national_charges_class_0'=>20,'national_charges_class_1'=>0,'national_charges_class_2'=>0,'national_charges_class_3'=>0),
            array('shipping_mode_id'=>4,'delivery_type_id' => 2,'range_up'=>10.01,'range_down'=>1000,'local_or_6hr'=>20,'national_charges_class_0'=>20,'national_charges_class_1'=>0,'national_charges_class_2'=>0,'national_charges_class_3'=>0),


        ));

        DB::table('corporate_standard_booking_type_charges')->truncate();
        DB::table('corporate_standard_booking_type_charges')->insert(array(
            array('shipping_mode_id'=>1,'replacement_charges'=>100,'try_and_buy_charges'=>150),
            array('shipping_mode_id'=>2,'replacement_charges'=>100,'try_and_buy_charges'=>150),
            array('shipping_mode_id'=>3,'replacement_charges'=>100,'try_and_buy_charges'=>150),
            array('shipping_mode_id'=>4,'replacement_charges'=>100,'try_and_buy_charges'=>150),
        ));

        DB::table('corporate_standard_cash_handling_charges')->truncate();
        DB::table('corporate_standard_cash_handling_charges')->insert(array(
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


        DB::table('corporate_standard_insurance_charges')->truncate();
        DB::table('corporate_standard_insurance_charges')->insert(array(
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

        DB::table('corporate_standard_return_charges')->truncate();
        DB::table('corporate_standard_return_charges')->insert(array(
            array('shipping_mode_id'=>1,'local'=>100,'national'=>100),
            array('shipping_mode_id'=>2,'local'=>100,'national'=>100),
            array('shipping_mode_id'=>3,'local'=>100,'national'=>100),
            array('shipping_mode_id'=>4,'local'=>100,'national'=>100),

        ));
        DB::table('corporate_standard_fuel_surcharges')->truncate();
        DB::table('corporate_standard_fuel_surcharges')->insert(array(
            array('shipping_mode_id'=>1,'fuel_surcharge'=>10),
            array('shipping_mode_id'=>2,'fuel_surcharge'=>10),
            array('shipping_mode_id'=>3,'fuel_surcharge'=>10),
            array('shipping_mode_id'=>4,'fuel_surcharge'=>10),
        ));


    }
}
