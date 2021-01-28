<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForUserCreationStepTableSeeder extends Seeder
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
            array('id' => 200, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Admin User Request Received', 'type_id' => 1, 'subject' => 'New User Request', 'body' => 'Dear [admin],'. PHP_EOL . PHP_EOL .'New user request for [admin_user_name] has been received.'. PHP_EOL . PHP_EOL .'[full_name]'. PHP_EOL .'[department]'. PHP_EOL .'[designation]' , 'updated_by' => 7, 'status' => 0),
            array('id' => 201, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Admin User Request Forwarded', 'type_id' => 1, 'subject' => 'New user request has been forwarded to you', 'body' => 'Dear [admin],'. PHP_EOL . PHP_EOL .'New user request for [admin_user_name] has been forwarded to you.'. PHP_EOL . PHP_EOL .'[full_name]'. PHP_EOL .'[department]'. PHP_EOL .'[designation]' , 'updated_by' => 7, 'status' => 0),
            array('id' => 202, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Admin User Request Completed', 'type_id' => 1, 'subject' => '[admin_user_name] successfully created', 'body' => 'Dear [admin],'. PHP_EOL . PHP_EOL .'Your request of creating a new user is completed for [admin_user_name], Kindly find the credentials.'. PHP_EOL . PHP_EOL .'[trax_id]'. PHP_EOL .'[full_name]'. PHP_EOL .'[email]'. PHP_EOL .'[sonic_password]'. PHP_EOL .'[outlook_password]' , 'updated_by' => 7, 'status' => 0)
        ));
    }
}
