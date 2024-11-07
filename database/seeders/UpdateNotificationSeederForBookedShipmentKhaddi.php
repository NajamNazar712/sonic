<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationSeederForBookedShipmentKhaddi extends Seeder
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
        	array('id' => 134, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Email To Khaddi', 'type_id' => 1, 'subject' => 'Pending Booked Status', 'body' => 'Dear [company_name],' . PHP_EOL . 'This is to notify your following shipments have been booked, please take action.'. PHP_EOL . PHP_EOL .'[preview]'. PHP_EOL . PHP_EOL . 'Please contact at info@trax.pk or 0304-11-11-232 for further details.', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
