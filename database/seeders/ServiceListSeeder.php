<?php

use Illuminate\Database\Seeder;

class ServiceListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('service_list')->truncate();
        DB::table('service_list')->insert(array(
            array('id' => 1, 'name' => "International Logistics", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 2, 'name' => "Warehousing", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 3, 'name' => "Bulk Movements", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 4, 'name' => "E-Commerce (COD)", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 5, 'name' => "E-Commerce (Corporate)", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 6, 'name' => "Document Deliveries", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 7, 'name' => "Gift Deliveries", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 8, 'name' => "Quick Commerce", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 9, 'name' => "Moving and Packing", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
        ));
    }
}
