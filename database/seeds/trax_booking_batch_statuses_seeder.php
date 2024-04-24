<?php

use Illuminate\Database\Seeder;

class trax_booking_batch_statuses_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time_stamp =\Carbon\Carbon::now();
        DB::table('general_settings')->insert(array(
            array('type'=>'booking_batch_length','setting_value'=>3,'description'=>'Logistic Booking Batches','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
        ));
    }
}
