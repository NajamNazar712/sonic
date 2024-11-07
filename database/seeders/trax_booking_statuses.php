<?php

use Illuminate\Database\Seeder;

class trax_booking_statuses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time_stamp =\Carbon\Carbon::now();
        DB::table('trax_booking_statuses')->insert(array(
            array('id'=>1,'name'=>'Booking Pending','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>2,'name'=>'Booking Modified ','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
//            array('name'=>'Complete','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
        ));
    }
}
