<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForClaimValid extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->insert(array(
            array('id' => 117, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Email on Claim Valid', 'type_id' => 1, 'subject' => 'Claim valid notification', 'body' => 'This is to inform you that Shipment have been marked as valid', 'updated_by' => 8, 'status' => 0),
        ));
    }
}
