<?php

use Illuminate\Database\Seeder;

class ConsigneeRefusedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('consignee_refused_reasons')->truncate();

        $time = \Carbon\Carbon::now();
        DB::table('consignee_refused_reasons')->insert(array(
            array('id' =>1, 'reasons'=> 'Consignee Wants To Open The Shipment', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>2, 'reasons'=> 'Issue In The COD Amount/Product', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>3, 'reasons'=> 'No Such Order From Consignee', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>4, 'reasons'=> 'Refused After Opening The Shipment', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>5, 'reasons'=> 'Delay in Dispatched from Shipper', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>6, 'reasons'=> 'Delay in Delivery From TRAX', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>7, 'reasons'=> 'Duplicate order', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>8, 'reasons'=> 'Purchased From Outlet', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>9, 'reasons'=> 'Quality Issue', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>10, 'reasons'=> 'Change of Mind', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>11, 'reasons'=> 'Place another order', 'status' => '1', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>12, 'reasons'=> 'Other', 'status' => '1', 'created_at' => $time, 'updated_at' => $time)
        ));
    }
}
