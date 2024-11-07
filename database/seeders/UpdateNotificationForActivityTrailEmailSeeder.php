<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForActivityTrailEmailSeeder extends Seeder
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
            array('id' => 127, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Activity Trail Sonic', 'type_id' => 1, 'subject' => ' Activity Trail  | [date]' , 'body' => 'Dear [contact_person],'. PHP_EOL .PHP_EOL.'Please find below the recent activity of your team member(s).'.PHP_EOL .PHP_EOL.'[preview]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
