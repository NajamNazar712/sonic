<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailsFromSetting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->where('id', 236)->delete();
        DB::table('notifications')->insert(array(
            array(
                    'id' => 236, 
                    'created_at' => $timestamp, 
                    'updated_at' => $timestamp, 
                    'name' => 'Receive Deliveries Report Email', 
                    'type_id' => 1, 
                    'subject' => 'Daily Received Deliveries Report', 
                    'body' => 'Dear Concern, '. PHP_EOL .'Please downlaod the report from the following link: [link]' ,
                    'updated_by' => 3, 
                    'status' => 1
                )
        ));

        DB::table('notifications')->where('id', 237)->delete();
        DB::table('notifications')->insert(array(
            array(
                    'id' => 237, 
                    'created_at' => $timestamp, 
                    'updated_at' => $timestamp, 
                    'name' => 'Receive Return Deliveries Report Email', 
                    'type_id' => 1, 
                    'subject' => 'Daily Receive Return Deliveries Report', 
                    'body' => 'Dear Concern, '. PHP_EOL .'Please downlaod the report from the following link: [link]' ,
                    'updated_by' => 3, 
                    'status' => 1
                )
        ));

        DB::table('notifications')->where('id', 238)->delete();
        DB::table('notifications')->insert(array(
            array(
                    'id' => 238, 
                    'created_at' => $timestamp, 
                    'updated_at' => $timestamp, 
                    'name' => 'Delivery Note History Email', 
                    'type_id' => 1, 
                    'subject' => 'Daily Delivery Note History Report', 
                    'body' => 'Dear Concern, '. PHP_EOL .'Please downlaod the report from the following link: [link]' ,
                    'updated_by' => 3, 
                    'status' => 1
                )
        ));

        DB::table('notifications')->where('id', 239)->delete();
        DB::table('notifications')->insert(array(
            array(
                    'id' => 239, 
                    'created_at' => $timestamp, 
                    'updated_at' => $timestamp, 
                    'name' => 'Weight QC Report Email', 
                    'type_id' => 1, 
                    'subject' => 'Daily Weight QC Report', 
                    'body' => 'Dear Concern, '. PHP_EOL .'Please downlaod the report from the following link: [link]' ,
                    'updated_by' => 3, 
                    'status' => 1
                )
        ));

        DB::table('notifications')->where('id', 240)->delete();
        DB::table('notifications')->insert(array(
            array(
                    'id' => 240, 
                    'created_at' => $timestamp, 
                    'updated_at' => $timestamp, 
                    'name' => 'Overall Sales Report Email', 
                    'type_id' => 1, 
                    'subject' => 'Daily Overall Sales Report', 
                    'body' => 'Dear Concern, '. PHP_EOL .'Please downlaod the report from the following link: [link]' ,
                    'updated_by' => 3, 
                    'status' => 1
                )
        ));
    }
}
