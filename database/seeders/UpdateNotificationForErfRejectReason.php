<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForErfRejectReason extends Seeder
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
            array('id' => 173, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reject ERF Request', 'type_id' => 1, 'subject' => 'Reject ERF Request' , 'body' => 'The [erf_id] request has been rejected by Muhammad Hassan Khan (CEO)', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
