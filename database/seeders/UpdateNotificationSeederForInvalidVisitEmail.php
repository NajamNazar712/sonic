<?php

use Illuminate\Database\Seeder;

class UpdateNotificationSeederForInvalidVisitEmail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->insert(array(
            array('id' => 215, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Invalid Visits Salesperson', 'type_id' => 1, 'subject' => 'Invalid Visits Salesperson', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
