<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForCRMEmailTableSeeder extends Seeder
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
            array('id' => 31, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM Notification Request', 'type_id' => 1, 'subject' => '[request_id] [tracking_number]', 'body' => '[shipper_name][email][phone][destination][channel][case_nature][case_nature_type][tracking_number][details]'. PHP_EOL . PHP_EOL . PHP_EOL .'', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
