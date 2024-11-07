<?php

use Illuminate\Database\Seeder;

class rider_child_cn_seeder extends Seeder
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

        \Illuminate\Support\Facades\DB::table('trax_child_cn_receive_admin_stores')->insert(array(
            array('id' => 1,'receive_date'=>$date,'company_code'=> 'Logistic' ,'area_code'=>202,'cn_from'=>5000,'cn_to'=>5005,'quantity'=>5,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
        ));


        \Illuminate\Support\Facades\DB::table('trax_child_cn_issue_to_riders')->insert(array(
            array('id' => 2,'child_admin_store_id'=>1,'company_code'=> 'Logistic' ,'issue_date' =>$time_stamp ,'rider_id'=>1,'cn_from'=>5000,'cn_to'=>5005,'quantity'=>5,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
        ));

        \Illuminate\Support\Facades\DB::table('trax_rider_child_cn_details')->insert(array(
            array('id'=>1,'child_cn_issue_id'=>2,'cn_number'=>5001,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>2,'child_cn_issue_id'=>2,'cn_number'=>5002,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>3,'child_cn_issue_id'=>2,'cn_number'=>5003,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>4,'child_cn_issue_id'=>2,'cn_number'=>5004,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id'=>5,'child_cn_issue_id'=>2,'cn_number'=>5005,'created_at'=>$time_stamp,'updated_at'=>$time_stamp)

        ));
    }
}
