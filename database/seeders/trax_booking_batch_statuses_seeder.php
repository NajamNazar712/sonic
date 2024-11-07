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
        DB::table('trax_booking_batch_statuses')->insert(array(
            array('id'=>1,'name'=>'Pending','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>2,'name'=>'In-Process','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>3,'name'=>'Complete','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
        ));
    }
}
