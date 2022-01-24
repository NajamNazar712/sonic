<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeadNotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

    	DB::table('lead_notifications')->truncate();

        DB::table('lead_notifications')->insert(array(
            array('id' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Welcome Email', 'type_id' => 1, 'subject' => 'Welcome [shipper_name]', 'body' => 'Dear [shipper_name],' . PHP_EOL . 'This is to notify you that your account has been registered in TRAX Logistics. Please Contact you Sales Person [tagged_salesperson_name], [tagged_salesperson_number],[tagged_salesperson_email]', 'updated_by' => 3, 'status' => 0),
            array('id' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Welcome SMS', 'type_id' => 2, 'subject' => 'Welcome [shipper_name]', 'body' => 'Dear [shipper_name],' . PHP_EOL . 'This is to notify you that your account has been registered in TRAX Logistics. Please Contact you Sales Person [tagged_salesperson_name], [tagged_salesperson_number],[tagged_salesperson_email]', 'updated_by' => 3, 'status' => 0),
            array('id' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Unresponsive Email', 'type_id' => 1, 'subject' => 'Welcome [shipper_name]', 'body' => 'Dear [shipper_name],' . PHP_EOL . 'This is to notify you that your account has been registered in TRAX Logistics. Please Contact you Sales Person [tagged_salesperson_name], [tagged_salesperson_number],[tagged_salesperson_email]', 'updated_by' => 3, 'status' => 0),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Unresponsive SMS', 'type_id' => 2, 'subject' => 'Welcome [shipper_name]', 'body' => 'Dear [shipper_name],' . PHP_EOL . 'This is to notify you that your account has been registered in TRAX Logistics. Please Contact you Sales Person [tagged_salesperson_name], [tagged_salesperson_number],[tagged_salesperson_email]', 'updated_by' => 3, 'status' => 0),
        ));
    }
}
