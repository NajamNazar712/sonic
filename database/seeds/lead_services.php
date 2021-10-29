<?php

use Illuminate\Database\Seeder;

class lead_services extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('service_list')->insert(array(
            array('id' => 1, 'name' => "International", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 2, 'name' => "Warehousing", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 3, 'name' => "Bulk Movements (Overland)", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 4, 'name' => "Cash On Delivery", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 5, 'name' => "Corporate Account (Non COD)", 'status' => 1, 'created_at'=>$timestamp, 'updated_at'=>$timestamp)
        ));
    }
}
