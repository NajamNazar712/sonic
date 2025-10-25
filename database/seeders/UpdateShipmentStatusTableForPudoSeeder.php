<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateShipmentStatusTableForPudoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 153, 'name' => 'Shipment - Generated Transfer Note','code'=>'S-GTN','description'=>'Shipment - Generated Transfer Note','status'=>'1','created_at'=>now(),'updated_at'=>now()),
            array('id' => 154, 'name' => 'Shipment - Collect From Center','code'=>'S-CFC','description'=>'Shipment - Collected From Center','status'=>'1','created_at'=>now(),'updated_at'=>now()),
            array('id' => 155, 'name' => 'Return - Shipment Generated Transfer Note','code'=>'R-SGTN','description'=>'Return - Shipment Generated Transfer Note','status'=>'1','created_at'=>now(),'updated_at'=>now()),
            array('id' => 156, 'name' => 'Return - Shipment Collect From Center','code'=>'R-SCFC','description'=>'Return - Shipment Collect From Center','status'=>'1','created_at'=>now(),'updated_at'=>now()),

        ));
    }
}
