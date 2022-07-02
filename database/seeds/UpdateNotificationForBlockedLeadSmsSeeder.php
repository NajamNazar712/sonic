<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForBlockedLeadSmsSeeder extends Seeder
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
            array('id' => 181, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Blocked Lead Sms', 'type_id' => 2,'body' => 'Dear [name],'. PHP_EOL .'Your lead is blocked due to the following reason.'.PHP_EOL .PHP_EOL.'[reason].', 'updated_by' => 7, 'status' => 1)
        ));
    }
}
