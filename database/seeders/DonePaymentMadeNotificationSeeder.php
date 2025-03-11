<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DonePaymentMadeNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp =  \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->insert(array(
            array(
                'id' => 235, 
                'created_at' => $timestamp, 
                'updated_at' => $timestamp, 
                'name' => 'Done Payment Email', 
                'type_id' => 1, 
                'subject' => 'Download link for Done Payment Report', 
                'body' => 'Done Payment Report' . PHP_EOL . '[link]', 
                'updated_by' => 1,
                'status' => 1
            ),
        ));
    }
}
