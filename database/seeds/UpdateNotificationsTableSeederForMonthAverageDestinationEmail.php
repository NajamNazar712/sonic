<?php

use App\Http\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateNotificationsTableSeederForMonthAverageDestinationEmail extends Seeder
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
        	array('id' => 190, 
            'created_at' => $timestamp, 
            'updated_at' => $timestamp, 
            'name' => 'Daily Month Average Destination Report', 
            'type_id' => 1, 
            'subject' => 'Daily Month Average Destination Report', 
            'body' => 'Daily Month Average Destination [date]',
            'updated_by' => 7, 
            'status' => 0)
        ));
        
    }
}
