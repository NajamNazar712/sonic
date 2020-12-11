<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNotificationsForOverlandAgingReport extends Seeder
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
            array('id' => 112, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Overland Aging Report', 'type_id' => 1, 'subject' => 'Overland Aging Report', 'body' => '[preview]','updated_by' => 3, 'status' => 0)
        ));
    }
}
