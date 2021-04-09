<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsSeederForPreAlertMasterCargo extends Seeder
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
            array('id' => 128, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pre Alert Master Cargo', 'type_id' => 1, 'subject' => 'Master Cargo Details', 'body' => 'The Master Cargo will arrive at the following destinations [preview]', 'updated_by' => 3, 'status' => 0),
        ));
    }
}
