<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UpdateNotificationForPettyCashQAReport extends Seeder
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
            array('id' => 89, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Petty Cash QA Report', 'type_id' => 1, 'subject' => 'Petty Cash QA Report', 'body' =>'Please find below the link to download Petty Cash QA Report' . PHP_EOL .'[link]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
