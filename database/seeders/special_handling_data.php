<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class special_handling_data extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time_stamp =  \Carbon\Carbon::now();
        \Illuminate\Support\Facades\DB::table('trax_special_handling_list')->insert(array(
          array('handling_code'=>'Electronic','description'=>'Electronic Charges','rate'=>550,'pay_mode'=>'Cash','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('handling_code'=>'Other','description'=>'Other Charges','rate'=>0,'pay_mode'=>'Cash','created_at'=>$time_stamp,'updated_at'=>$time_stamp)

        ));
    }
}
