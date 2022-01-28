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
            array('id' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Welcome Email', 'type_id' => 1, 'subject' => 'CONGRATULATIONS, YOU\'VE REGISTERED SUCCESSFULLY!', 'body' => 'Dear Customer [shipper_name],' . PHP_EOL . ' We welcome you to experience the craziest journey of your business with TRAX, have an adventure of tech-enabled operations, fastest payments and smooth deliveries on your way to this road of success and growth! Here\'s your salesperson\'s name - [tagged_salesperson_name], contact number - [tagged_salesperson_number] and email - [tagged_salesperson_email].' . PHP_EOL . ' For further information you can visit www.trax.pk ' . PHP_EOL . 'Regards,' . PHP_EOL . 'Team Trax', 'updated_by' => 3, 'status' => 0),
            array('id' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Welcome SMS', 'type_id' => 2, 'subject' => 'CONGRATULATIONS, YOU\'VE REGISTERED SUCCESSFULLY!', 'body' => 'Congratulations  [shipper_name],' . PHP_EOL . ' You\'ve registered successfully! TRAX is very excited to have you onboard and we look forward to serve you exceptionally. Here\'s your sales person\'sname - [tagged_salesperson_name], contact number - [tagged_salesperson_number] and email - [tagged_salesperson_email].', 'updated_by' => 3, 'status' => 0),
            array('id' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Unresponsive Email', 'type_id' => 1, 'subject' => 'REGISTRATION PROCESS PENDING!', 'body' => 'Hey,' . PHP_EOL . 'We tried contacting you regarding your account opening request with TRAX but we were unable to have your attention.' . PHP_EOL . ' Please contact us at 021-111-11-8729 for smooth registration process.' . PHP_EOL . ' We\'re looking forward to serve you with Pakistan\'s best tech-enabled logistic solutions', 'updated_by' => 3, 'status' => 0),
            array('id' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Unresponsive SMS', 'type_id' => 2, 'subject' => 'REGISTRATION PROCESS PENDING!', 'body' => 'Dear [shipper_name],' . PHP_EOL . 'We tried contacting you regarding your account opening request with TRAX but we were unable to have your attention.' . PHP_EOL . ' Please contact us at 021-111-11-8729 for smooth registration process.', 'updated_by' => 3, 'status' => 0),
        ));
    }
}
