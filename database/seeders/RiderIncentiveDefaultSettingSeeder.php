<?php

use Illuminate\Database\Seeder;

class RiderIncentiveDefaultSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('riders_incentive_settings')->truncate();

        DB::table('riders_incentive_settings')->insert(array(
            array('id' => 1, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 1, 'rider_shipment_weight_range_id' => 1, 'value' => 19 ,'added_by' => 6),
            array('id' => 2, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 1, 'rider_shipment_weight_range_id' => 2, 'value' => 24 ,'added_by' => 6),
            array('id' => 3, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 1, 'rider_shipment_weight_range_id' => 3, 'value' => 19 ,'added_by' => 6),
            array('id' => 4, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 2, 'rider_shipment_weight_range_id' => 1, 'value' => 9 ,'added_by' => 6),
            array('id' => 5, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 2, 'rider_shipment_weight_range_id' => 2, 'value' => 14 ,'added_by' => 6),
            array('id' => 6, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 2, 'rider_shipment_weight_range_id' => 3, 'value' => 2 ,'added_by' => 6),
            array('id' => 7, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 3, 'rider_shipment_weight_range_id' => 1, 'value' => 0 ,'added_by' => 6),
            array('id' => 8, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 3, 'rider_shipment_weight_range_id' => 2, 'value' => 0 ,'added_by' => 6),
            array('id' => 9, 'rider_category_id' => 1, 'rider_shipment_payment_type_id' => 3, 'rider_shipment_weight_range_id' => 3, 'value' => 9 ,'added_by' => 6),
            array('id' => 10, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 1, 'rider_shipment_weight_range_id' => 4, 'value' => 19 ,'added_by' => 6),
            array('id' => 11, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 1, 'rider_shipment_weight_range_id' => 5, 'value' => 29 ,'added_by' => 6),
            array('id' => 12, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 1, 'rider_shipment_weight_range_id' => 3, 'value' => 0 ,'added_by' => 6),
            array('id' => 13, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 2, 'rider_shipment_weight_range_id' => 4, 'value' => 9 ,'added_by' => 6),
            array('id' => 14, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 2, 'rider_shipment_weight_range_id' => 5, 'value' => 19 ,'added_by' => 6),
            array('id' => 15, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 2, 'rider_shipment_weight_range_id' => 3, 'value' => 2 ,'added_by' => 6),
            array('id' => 16, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 3, 'rider_shipment_weight_range_id' => 4, 'value' => 0 ,'added_by' => 6),
            array('id' => 17, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 3, 'rider_shipment_weight_range_id' => 5, 'value' => 0 ,'added_by' => 6),
            array('id' => 18, 'rider_category_id' => 2, 'rider_shipment_payment_type_id' => 3, 'rider_shipment_weight_range_id' => 3, 'value' => 9 ,'added_by' => 6),

        ));
    }
}
