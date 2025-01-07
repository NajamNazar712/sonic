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
                'name' => 'Done Payment SMS', 
                'type_id' => 2, 
                'subject' => NULL, 
                'body' => 'Done Payment Report', 
                'updated_by' => Auth::id(),
                'status' => 1
            ),
        ));
    }
}
