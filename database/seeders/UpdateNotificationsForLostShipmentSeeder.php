<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForLostShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->toDateTimeString();

        DB::table('notifications')->insert(array(
            array('id' => 150, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Lost Shipment', 'type_id' => 1,'Subject'=>'Lost Shipment' , 'body'=>'Following shipment has been marked as Lost. [preview]', 'updated_by'=> 6, 'status'=> 0)
        ));
    }
}
