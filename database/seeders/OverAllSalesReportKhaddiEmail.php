<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class OverAllSalesReportKhaddiEmail extends Seeder
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
            array(
                'id' => 254,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Overall Sales Report Email Khaddi',
                'type_id' => 1,
                'subject' => 'Daily Overall Sales Report Khaddi',
                'body' => 'Dear Concern, '. PHP_EOL .'Please downlaod the report from the following link: [link]',
                'updated_by' => 3756,
                'status' => 1,
            )
        ));
    }
    
}
