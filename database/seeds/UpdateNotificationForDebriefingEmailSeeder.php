<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForDebriefingEmailSeeder extends Seeder
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
            array('id' => 44, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Debriefing Hub Report Email', 'type_id' => 1, 'subject' => 'Daily Hub Report [hub] of [date]', 'body' => 'Please check daily report dated [date]' . PHP_EOL . PHP_EOL . PHP_EOL .'[link]', 'updated_by' => 6, 'status' => 0),
            array('id' => 45, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Debriefing Zone Report Email', 'type_id' => 1, 'subject' => 'Daily Report [zone] Zone of [date]', 'body' => 'Please check daily report dated [date]' . PHP_EOL . PHP_EOL . PHP_EOL .'[link]', 'updated_by' => 6, 'status' => 0),
            array('id' => 46, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Debriefing Overall Report Email', 'type_id' => 1, 'subject' => '[date] Work Sheet & Report', 'body' => 'Please check daily report dated [date]' . PHP_EOL . PHP_EOL . PHP_EOL .'[link]', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
