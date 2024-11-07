<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForCargoManifestSlip extends Seeder
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
            array('id' => 148, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Cargo Manifest Slip', 'type_id' => 1, 'subject' => 'Cargo Manifest Slip [hub] of [date]', 'body' => 'Please download Cargo Manifest slip from the following link: [link].', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
