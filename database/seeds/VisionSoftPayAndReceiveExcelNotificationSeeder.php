<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisionSoftPayAndReceiveExcelNotificationSeeder extends Seeder
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
            array('id' => 213, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Vision Soft Payable and Receivable Excel', 'type_id' => 1, 'subject' => 'Vision Soft Payable and Receivable Excel', 'body' => 'Dear Concern,' . PHP_EOL . 'Please find below the link to download Vision Soft Payable and Receivable Excel Report.' . PHP_EOL .'[link]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
