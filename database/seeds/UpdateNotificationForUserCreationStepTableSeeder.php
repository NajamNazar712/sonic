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
            array('id' => 200, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Admin User Request Created', 'type_id' => 1, 'subject' => 'New user request generated', 'body' => 'Dear [admin],'. PHP_EOL . PHP_EOL .'New email request generated for [admin_user_name].'. PHP_EOL .'[preview]' , 'updated_by' => 7, 'status' => 0),
            array('id' => 201, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Admin User Request Verified', 'type_id' => 1, 'subject' => 'New user request verified by HR', 'body' => 'Dear [admin],'. PHP_EOL . PHP_EOL .'New email request generated for [admin_user_name].'. PHP_EOL .'[preview]' , 'updated_by' => 7, 'status' => 0),
            array('id' => 202, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Admin User Request Completed', 'type_id' => 1, 'subject' => '[admin_user_name] successfully created', 'body' => 'Dear [admin],'. PHP_EOL . PHP_EOL .'New email request generated for [admin_user_name].'. PHP_EOL .'[preview]' , 'updated_by' => 7, 'status' => 0)
        ));
    }
}
