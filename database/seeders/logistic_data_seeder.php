<?php

use Illuminate\Database\Seeder;

class logistic_data_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $time_stamp =\Carbon\Carbon::now();
        DB::table('trax_parent_products')->insert(array(
            array('id' => 1,'parent_code'=> 'GL' ,'parent_name' => 'General Logistic','created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 2,'parent_code'=> 'EC' ,'parent_name' => 'E-Commerce','created_at'=>$time_stamp,'updated_at'=>$time_stamp)
        ));

        DB::table('trax_products')->insert(array(
            array('id' => 1,'product_code'=> 'L' ,'product_name' => 'Logistic','parent_id'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 2,'product_code'=> 'E' ,'product_name' => 'Express','parent_id'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 3,'product_code'=> 'O' ,'product_name' => 'Overland','parent_id'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 4,'product_code'=> 'C' ,'product_name' => 'COD','parent_id'=>2,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 5,'product_code'=> 'EL' ,'product_name' => 'E-commerce Logistic','parent_id'=>2,'created_at'=>$time_stamp,'updated_at'=>$time_stamp)
        ));

        DB::table('trax_services')->insert(array(
            array('id' => 1,'service_code'=> 'R' ,'service_name' => 'Rush','product_id'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 2,'service_code'=> 'SP' ,'service_name' => 'Saver Plus','product_id'=>1,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 3,'service_code'=> 'S' ,'service_name' => 'Swit','product_id'=>2,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 4,'service_code'=> 'SD' ,'service_name' => 'Same Day','product_id'=>2,'created_at'=>$time_stamp,'updated_at'=>$time_stamp),
            array('id' => 5,'service_code'=> 'R' ,'service_name' => 'Rush','product_id'=>5,'created_at'=>$time_stamp,'updated_at'=>$time_stamp)
        ));



    }
}
