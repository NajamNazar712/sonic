<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationForIncompleteDocumentsUsersTableSeeder extends Seeder
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
            array('id' => 60, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Active Accounts with incomplete documents', 'type_id' => 1, 'subject' => 'Active Accounts with incomplete documents [date]', 'body' => 'Please check these accounts are active with incomplete documents, please upload complete documents otherwise delivered shipments could not be paid.' . PHP_EOL . '[preview].', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
