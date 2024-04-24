<?php

use Illuminate\Database\Seeder;

class logistic_booking_batch_setting_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time_stamp =\Carbon\Carbon::now();
        DB::table('trax_booking_batch_statuses')->insert(array(
            array('name'=>'Pending','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('name'=>'In-Process','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('name'=>'Complete','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
        ));
    }
}
