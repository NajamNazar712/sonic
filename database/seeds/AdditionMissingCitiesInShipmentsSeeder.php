<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionMissingCitiesInShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //For Shipments Update Cities
        DB::table('shipments')
            ->whereIn('consignee_city_id', [
                4662, 4375, 4715, 4427, 4612, 4822, 4401, 3746, 3730, 4404,
                4237, 4219, 4908, 4573, 4395, 4872, 4614, 4885, 4827, 4214,
                4878, 4967, 4626
            ])
            ->update([
                'consignee_city_id' => DB::raw("
            CASE 
                WHEN consignee_city_id = 4662 THEN 6559
                WHEN consignee_city_id = 4375 THEN 6272
                WHEN consignee_city_id = 4715 THEN 6612
                WHEN consignee_city_id = 4427 THEN 6512
                WHEN consignee_city_id = 4612 THEN 6509
                WHEN consignee_city_id = 4822 THEN 6303
                WHEN consignee_city_id = 4401 THEN 6825
                WHEN consignee_city_id = 3746 THEN 6042
                WHEN consignee_city_id = 3730 THEN 6471
                WHEN consignee_city_id = 4404 THEN 6301
                WHEN consignee_city_id = 4237 THEN 6134
                WHEN consignee_city_id = 4219 THEN 6116
                WHEN consignee_city_id = 4908 THEN 6805
                WHEN consignee_city_id = 4573 THEN 6322
                WHEN consignee_city_id = 4395 THEN 6292
                WHEN consignee_city_id = 4872 THEN 6769
                WHEN consignee_city_id = 4614 THEN 6698
                WHEN consignee_city_id = 4885 THEN 6782
                WHEN consignee_city_id = 4827 THEN 6724
                WHEN consignee_city_id = 4214 THEN 6111
                WHEN consignee_city_id = 4878 THEN 6775
                WHEN consignee_city_id = 4967 THEN 6864
                WHEN consignee_city_id = 4626 THEN 6523
            END
        ")
            ]);

        //For Shipments journey Update Cities
        DB::table('shipments_journey')
            ->whereIn('city_id', [
                4662, 4375, 4715, 4427, 4612, 4822, 4401, 3746, 3730, 4404,
                4237, 4219, 4908, 4573, 4395, 4872, 4614, 4885, 4827, 4214,
                4878, 4967, 4626
            ])
            ->update([
                'city_id' =>DB::raw("
            CASE 
                WHEN city_id = 4662 THEN 6559
                WHEN city_id = 4375 THEN 6272
                WHEN city_id = 4715 THEN 6612
                WHEN city_id = 4427 THEN 6512
                WHEN city_id = 4612 THEN 6509
                WHEN city_id = 4822 THEN 6303
                WHEN city_id = 4401 THEN 6825
                WHEN city_id = 3746 THEN 6042
                WHEN city_id = 3730 THEN 6471
                WHEN city_id = 4404 THEN 6301
                WHEN city_id = 4237 THEN 6134
                WHEN city_id = 4219 THEN 6116
                WHEN city_id = 4908 THEN 6805
                WHEN city_id = 4573 THEN 6322
                WHEN city_id = 4395 THEN 6292
                WHEN city_id = 4872 THEN 6769
                WHEN city_id = 4614 THEN 6698
                WHEN city_id = 4885 THEN 6782
                WHEN city_id = 4827 THEN 6724
                WHEN city_id = 4214 THEN 6111
                WHEN city_id = 4878 THEN 6775
                WHEN city_id = 4967 THEN 6864
                WHEN city_id = 4626 THEN 6523
            END
        ")
            ]);
    }
}
