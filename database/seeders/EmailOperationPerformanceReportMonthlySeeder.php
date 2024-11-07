<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailOperationPerformanceReportMonthlySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->where('name', 'Monthly Operation Performance Report Email')->delete();

        DB::table('notifications')->insert(array(
            array(
                'id' => 225,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Monthly Operation Performance Report Email',
                'type_id' => 1,
                'subject' => 'Monthly Operation Performance Report',
                'body' =>  "[date_time]".PHP_EOL."[link]",
                'updated_by' => 615,
                'status' => 1,
            )
        ));
    }
}
