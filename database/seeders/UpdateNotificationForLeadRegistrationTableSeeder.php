<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForLeadRegistrationTableSeeder extends Seeder
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
            array('id' => 113, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Account Activation Lead Management', 'type_id' => 1, 'subject' => 'Account Registration' , 'body' => 'Dear [contact_person],'. PHP_EOL .PHP_EOL.'Please click below to proceed for Account Registration.'.PHP_EOL .PHP_EOL.'[link]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
