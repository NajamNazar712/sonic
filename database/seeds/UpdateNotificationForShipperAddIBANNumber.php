<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForShipperAddIBANNumber extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->insert(array(
            array('id' => 91, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Add IBN Number', 'type_id' => 2, 'subject' => NULL, 'body' => 'Dear Concern,' . PHP_EOL . 'Your account [PIN] has been changed .', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
