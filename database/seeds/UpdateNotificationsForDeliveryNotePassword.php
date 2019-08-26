<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNotificationsForDeliveryNotePassword extends Seeder
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
            array('id' => 40, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Delivery Note Password', 'type_id' => 2, 'subject' => 'Delivery Note Password', 'body' => 'Dear [rider_name],' . PHP_EOL . 'Delivery Note No. [delivery_note_id]'. PHP_EOL .'Password: [password]', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
