<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForClaimValidInvalidTableSeeder extends Seeder
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
            array('id' => 117, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Claim VALID/REJECTED/CLOSED', 'type_id' => 1, 'subject' => 'Claim Request Updated [date]', 'body' => 'Dear Sir,'.PHP_EOL.'Claim Request ([id]) has been marked as [status]', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
