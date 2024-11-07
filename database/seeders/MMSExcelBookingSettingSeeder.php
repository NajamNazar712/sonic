<?php

use Illuminate\Database\Seeder;

class MMSExcelBookingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('general_settings')->insert(array(
            array('type' => "mms_excel_booking_setting", 'created_at'=> \Carbon\Carbon::now()),
        ));
    }
}
