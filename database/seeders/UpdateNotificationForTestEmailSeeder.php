<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForTestEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        array('id' => 219, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Test Email', 'type_id' => 1, 'subject' => 'Test Email', 'body' => 'Dear Concern' . PHP_EOL .'Please check this email to ensure email server is working.', 'updated_by' => 1, 'status' => 1);
    }
}
