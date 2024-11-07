<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNotificationForRiderPinTableSeeder extends Seeder
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
            array('id' => 61, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Rider New Pin', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [rider_name],' . PHP_EOL . 'Your Pin for Bolt is: [pin]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
