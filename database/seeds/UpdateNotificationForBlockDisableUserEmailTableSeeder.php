<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateNotificationForBlockDisableUserEmailTableSeeder extends Seeder
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
            array('id' => 229, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'User Block/Disable Account Email' , 'type_id' => 1, 'subject' => 'User Block/Disable Account [date]', 'body' => 'This is to inform you that your acccount has been [status]' . PHP_EOL . PHP_EOL .'[preview]', 'updated_by' => 615, 'status' => 0)
        ));
    }
}
