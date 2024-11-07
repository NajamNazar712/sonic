<?php

use Illuminate\Database\Seeder;

class UpdateNotificationSeederForApprovalRequest extends Seeder
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
            array('id' => 131, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Special Approval Request', 'type_id' => 1, 'subject' => 'Special Approval Request', 'body' => 'Dear Concern,' .PHP_EOL. 'A special approval is now associated with you for the request # [request_no]'. PHP_EOL .'Thanks', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
