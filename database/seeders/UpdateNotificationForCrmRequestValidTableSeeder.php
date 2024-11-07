<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForCrmRequestValidTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {$timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 41, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM Request Validated', 'type_id' => 1, 'subject' => 'Request ID:[request_id] Validated', 'body' => 'Dear Customer,'. PHP_EOL . 'This message is to tell you that your request is under review. We\'re looking into how to get it resolved, Assigned agent will be in contact with you till resolution', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
