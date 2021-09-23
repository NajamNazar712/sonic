<?php

use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\StandardReturnCharge;
use Illuminate\Database\Seeder;

class UpdateStandardReimbursementRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_weight_charges')->truncate();
        DB::table('standard_cash_handling_charges')->truncate();


        DB::table('standard_weight_charges')->insert(array(
            array('id' => 1, 'shipping_mode_id' => 1, 'range_up' => 0.01,'range_down' => 0.5,'weight_addition' => 0,'kg_range' => 0.5,'local_or_6hr' => 70,'national_charges_class_0' => 95,'national_charges_class_1' => 95,'national_charges_class_2' => 100 , 'national_charges_class_3' => 100),
            array('id' => 2, 'shipping_mode_id' => 1, 'range_up' => 0.51,'range_down' => 1,'weight_addition' => 0,'kg_range' => 0.5,'local_or_6hr' => 80,'national_charges_class_0' => 110,'national_charges_class_1' => 110,'national_charges_class_2' => 120 , 'national_charges_class_3' => 120),
            array('id' => 3, 'shipping_mode_id' => 1, 'range_up' => 1.01,'range_down' => 100,'weight_addition' => 1,'kg_range' => 1,'local_or_6hr' => 50,'national_charges_class_0' => 80,'national_charges_class_1' => 80,'national_charges_class_2' => 80 , 'national_charges_class_3' => 80),

            array('id' => 4, 'shipping_mode_id' => 2, 'range_up' => 0.01,'range_down' => 5,'weight_addition' => 0,'kg_range' => 5,'local_or_6hr' => 250,'national_charges_class_0' => 250,'national_charges_class_1' => 275,'national_charges_class_2' => 300 , 'national_charges_class_3' => 350),
            array('id' => 5, 'shipping_mode_id' => 2, 'range_up' => 5.01,'range_down' => 10,'weight_addition' => 0,'kg_range' => 10,'local_or_6hr' => 300,'national_charges_class_0' => 300,'national_charges_class_1' => 350,'national_charges_class_2' => 400 , 'national_charges_class_3' => 450),
            array('id' => 6, 'shipping_mode_id' => 2, 'range_up' => 10.01,'range_down' => 100,'weight_addition' => 1,'kg_range' => 1,'local_or_6hr' => 30,'national_charges_class_0' => 30,'national_charges_class_1' => 30,'national_charges_class_2' => 30 , 'national_charges_class_3' => 30),

            array('id' => 7, 'shipping_mode_id' => 3, 'range_up' => 0.01,'range_down' => 3,'weight_addition' => 0,'kg_range' => 3,'local_or_6hr' => 180,'national_charges_class_0' => 180,'national_charges_class_1' => 180,'national_charges_class_2' => 185 , 'national_charges_class_3' => 195),
            array('id' => 8, 'shipping_mode_id' => 3, 'range_up' => 3.01,'range_down' => 100,'weight_addition' => 1,'kg_range' => 1,'local_or_6hr' => 60,'national_charges_class_0' => 60,'national_charges_class_1' => 60,'national_charges_class_2' => 60 , 'national_charges_class_3' => 60),

            //sameday

            array('id' => 9, 'shipping_mode_id' => 4, 'range_up' => 0.01,'range_down' => 0.50,'weight_addition' => 0,'kg_range' => 0.5,'local_or_6hr' => 250,'national_charges_class_0' => 250,'national_charges_class_1' => 250,'national_charges_class_2' => 250 , 'national_charges_class_3' => 250),
            array('id' => 10, 'shipping_mode_id' => 4, 'range_up' => 0.51,'range_down' => 1,'weight_addition' => 0,'kg_range' => 0.5,'local_or_6hr' => 275,'national_charges_class_0' => 275,'national_charges_class_1' => 275,'national_charges_class_2' => 275 , 'national_charges_class_3' => 275),
            array('id' => 11, 'shipping_mode_id' => 4, 'range_up' => 1.01,'range_down' => 100,'weight_addition' => 0,'kg_range' => 0.5,'local_or_6hr' => 300,'national_charges_class_0' => 300,'national_charges_class_1' => 300,'national_charges_class_2' => 300 , 'national_charges_class_3' => 300),

        ));

        StandardCashHandlingCharge::create(['shipping_mode_id' => 1,'range_up' => 0,'range_down' => 3000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 1,'range_up' => 3001,'range_down' => 5000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 1,'range_up' => 5001,'range_down' => 10000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 1,'range_up' => 10001,'range_down' => 30000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 1,'range_up' => 30001,'range_down' => 1000000,'charges' => '1%']);

        StandardCashHandlingCharge::create(['shipping_mode_id' => 2,'range_up' => 0,'range_down' => 3000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 2,'range_up' => 3001,'range_down' => 5000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 2,'range_up' => 5001,'range_down' => 10000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 2,'range_up' => 10001,'range_down' => 30000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 2,'range_up' => 30001,'range_down' => 1000000,'charges' => '1%']);

        StandardCashHandlingCharge::create(['shipping_mode_id' => 3,'range_up' => 0,'range_down' => 3000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 3,'range_up' => 3001,'range_down' => 5000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 3,'range_up' => 5001,'range_down' => 10000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 3,'range_up' => 10001,'range_down' => 30000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 3,'range_up' => 30001,'range_down' => 1000000,'charges' => '1%']);

        StandardCashHandlingCharge::create(['shipping_mode_id' => 4,'range_up' => 0,'range_down' => 3000,'charges' => 0]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 4,'range_up' => 3001,'range_down' => 5000,'charges' => 100]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 4,'range_up' => 5001,'range_down' => 10000,'charges' => 200]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 4,'range_up' => 10001,'range_down' => 30000,'charges' => 300]);
        StandardCashHandlingCharge::create(['shipping_mode_id' => 4,'range_up' => 30001,'range_down' => 500000,'charges' => '1%']);

        StandardReturnCharge::whereIn('shipping_mode_id',[1,2,3])->update(['local' => 0,'national_charges_class_0' => '0%','national_charges_class_1' => '0%','national_charges_class_2' => '0%','national_charges_class_3' => '0%']);

        StandardBookingTypeCharge::whereIn('shipping_mode_id',[1,2,3])->update(['replacement_charges' => '100%' , 'try_and_buy_charges' => '150%']);


    }
}
