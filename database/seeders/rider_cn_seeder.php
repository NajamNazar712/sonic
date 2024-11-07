<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class rider_cn_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time_stamp =\Carbon\Carbon::now();
        $date = \Carbon\Carbon::now()->toDateString();

        \Illuminate\Support\Facades\DB::table('trax_cn_receive_admin_stores')->insert(array(
            array('id' => 1,'receive_date'=>$date,'company_code'=> 'Logistic' ,'area_code'=>202,'product_id'=>1,'cn_from'=>100,'cn_to'=>110,'quantity'=>110,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
        ));


        \Illuminate\Support\Facades\DB::table('trax_cn_issue_to_riders')->insert(array(
            array('id' => 1,'admin_store_id'=>3,'company_code'=> 'Logistic' ,'issue_date' =>$time_stamp ,'rider_id'=>1,'product_id'=>1,'cn_from'=>100,'cn_to'=>105,'quantity'=>5,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
        ));

        \Illuminate\Support\Facades\DB::table('trax_rider_cn_details')->insert(array(
            array('id'=>1,'cn_issue_id'=>1,'cn_number'=>101,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>2,'cn_issue_id'=>1,'cn_number'=>102,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>3,'cn_issue_id'=>1,'cn_number'=>103,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>4,'cn_issue_id'=>1,'cn_number'=>104,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>5,'cn_issue_id'=>1,'cn_number'=>105,'created_at'=>$time_stamp,'updated_at'=>$time_stamp)

        ));
    }
}
