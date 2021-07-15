<?php

use Illuminate\Database\Seeder;
use carbon\Carbon;
class CreateInactiveRiderReportTableSeeder extends Seeder
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
            array('id' => 140, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Inactive Rider Report', 'type_id' => 1, 'subject' => ' Inactive Rider Report', 'body' => '[preview]', 'updated_by' => 3, 'status' => 1)
        ));
    }
}
