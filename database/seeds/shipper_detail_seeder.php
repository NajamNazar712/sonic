<?php

use Illuminate\Database\Seeder;
use  Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class shipper_detail_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time_stamp =  Carbon::now();
       DB::table('trax_shipper_details')->insert(array(
           array('id'=>1,'user_id'=>117,'trax_product_id'=>1,'trax_service_id'=>1,'rider_id'=>1,'route_id'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
           array('id'=>2,'user_id'=>119,'trax_product_id'=>2,'trax_service_id'=>3,'rider_id'=>1,'route_id'=>7,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
           array('id'=>3,'user_id'=>120,'trax_product_id'=>5,'trax_service_id'=>5,'rider_id'=>2,'route_id'=>10,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
           array('id'=>4,'user_id'=>137,'trax_product_id'=>2,'trax_service_id'=>4,'rider_id'=>2,'route_id'=>14,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
           array('id'=>5,'user_id'=>138,'trax_product_id'=>1,'trax_service_id'=>2,'rider_id'=>3,'route_id'=>15,'created_at'=>$time_stamp,'updated_at'=>$time_stamp)
       ));
    }
}
