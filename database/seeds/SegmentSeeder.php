<?php

use Illuminate\Database\Seeder;

class SegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*DB::table('segments')->insert(array(
            array('id' => 1, 'name' => 'General Logistics'),
            array('id' => 2, 'name' => 'E-comm')
        ));*/

        DB::table('segments')->truncate();
        DB::table('segments')->insert(array(
            array('id' => 1, 'name' => 'LOGISTICS'),
            array('id' => 2, 'name' => 'ECOM'),
            array('id' => 3, 'name' => 'BANKING'),
            array('id' => 4, 'name' => 'BULK (EXTENDED DAYS KPI)'),
            array('id' => 5, 'name' => 'PRINT'),
            array('id' => 6, 'name' => 'WAREHOUSING'),
            array('id' => 7, 'name' => 'FULFILLMENT'),
            array('id' => 8, 'name' => 'MOVIT'),
            array('id' => 9, 'name' => 'INTERNATIONAL'),
            array('id' => 10, 'name' => 'SENTIMENTS'),
            array('id' => 11, 'name' => 'SPECIAL PROJECTS'),
        ));

        DB::table('sub_category_segments')->truncate();
        DB::table('sub_category_segments')->insert(array(
            array('id' => 1, 'segment_id' => 1, 'name' => 'LTL', 'code' => 'LT'),
            array('id' => 2, 'segment_id' => 1, 'name' => 'FTL', 'code' => 'LF'),
            array('id' => 3, 'segment_id' => 1, 'name' => 'HUB 2 HUB', 'code' => 'LH'),
            array('id' => 4, 'segment_id' => 1, 'name' => 'CASH CARGO', 'code' => 'LC'),
            array('id' => 5, 'segment_id' => 2, 'name' => 'REGULAR COD', 'code' => 'CC'),
            array('id' => 6, 'segment_id' => 2, 'name' => 'REPLACEMENT', 'code' => 'CR'),
            array('id' => 7, 'segment_id' => 2, 'name' => 'OPEN BOX', 'code' => 'CO'),
            array('id' => 8, 'segment_id' => 2, 'name' => 'TRY & BUY', 'code' => 'CT'),
            array('id' => 9, 'segment_id' => 2, 'name' => 'INTERCEPT', 'code' => 'CN'),
            array('id' => 10, 'segment_id' => 2, 'name' => 'INSURANCE', 'code' => 'CI'),
            array('id' => 11, 'segment_id' => 2, 'name' => 'DIRECT RETURN TO VENDOR', 'code' => 'CD'),
            array('id' => 12, 'segment_id' => 2, 'name' => 'LENDING', 'code' => 'CL'),
            array('id' => 13, 'segment_id' => 3, 'name' => 'BANK TO BANK', 'code' => 'BB'),
            array('id' => 14, 'segment_id' => 3, 'name' => 'BANK TO GENERAL', 'code' => 'BG'),
            array('id' => 15, 'segment_id' => 4, 'name' => 'GENERAL BULK', 'code' => 'GK'),
            array('id' => 16, 'segment_id' => 4, 'name' => 'BANKING BULK', 'code' => 'BK'),
            array('id' => 17, 'segment_id' => 5, 'name' => 'PRINT BANKING', 'code' => 'PB'),
            array('id' => 18, 'segment_id' => 5, 'name' => 'PRINT GENERAL', 'code' => 'PG'),
            array('id' => 19, 'segment_id' => 5, 'name' => 'STATIONERY SUPPLIES', 'code' => 'SS'),
            array('id' => 20, 'segment_id' => 5, 'name' => 'STUFFING', 'code' => 'ST'),
            array('id' => 21, 'segment_id' => 6, 'name' => 'REGULAR WAREHOUSING', 'code' => 'RW'),
            array('id' => 22, 'segment_id' => 6, 'name' => 'TEMPERATURE CONTROLLED', 'code' => 'TW'),
            array('id' => 23, 'segment_id' => 6, 'name' => 'PACKAGING MATERIAL', 'code' => 'PM'),
            array('id' => 24, 'segment_id' => 7, 'name' => 'FULFILLMENT', 'code' => 'FF'),
            array('id' => 25, 'segment_id' => 8, 'name' => 'PACKING', 'code' => 'PC'),
            array('id' => 26, 'segment_id' => 8, 'name' => 'TRANSPORTATION', 'code' => 'TM'),
            array('id' => 27, 'segment_id' => 9, 'name' => 'EXPRESS', 'code' => 'IX'),
            array('id' => 28, 'segment_id' => 9, 'name' => 'ECONOMY', 'code' => 'IE'),
            array('id' => 29, 'segment_id' => 9, 'name' => 'INTERNATIONAL COD', 'code' => 'IC'),
            array('id' => 30, 'segment_id' => 9, 'name' => 'GENERAL CARGO / FRIEGHT', 'code' => 'IF'),
            array('id' => 31, 'segment_id' => 10, 'name' => 'SENTIMENTS', 'code' => 'SE'),
            array('id' => 32, 'segment_id' => 11, 'name' => 'CUSTOMIZED SOLUTIONS', 'code' => 'SP'),
        ));
    }
}
