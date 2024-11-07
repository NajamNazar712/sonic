<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForApproveErfRequest extends Seeder
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
            array('id' => 174, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Approve ERF Request', 'type_id' => 1, 'subject' => 'Approve ERF Request' , 'body' => 'The [erf_id] request has been approved by [admin]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
