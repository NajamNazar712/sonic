<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class updateNotificationShipperEmail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 252, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'User Bank Account Email', 'type_id' => 1, 'subject' => 'User Bank Change Account', 'body' => 'This is to inform you that your bank account has been [status].' . PHP_EOL, 'updated_by' => 615, 'status' => 1)
        ));
        
        DB::table('notifications')->insert(array(
            array('id' => 253, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'User Bank Account Email', 'type_id' => 1, 'subject' => 'User Bank Change Account Notification', 'body' => 'This is to inform you that the following bank accounts list.' . PHP_EOL .  PHP_EOL . '[preview]', 'updated_by' => 615, 'status' => 1)
        ));
    }
}
