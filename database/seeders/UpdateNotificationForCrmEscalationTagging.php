<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForCrmEscalationTagging extends Seeder
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
            array('id' => 65, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM Request Tagging and Escalation', 'type_id' => 1, 'subject' => 'Crm Request: [request_id] Tagging', 'body' => 'Dear User,'. PHP_EOL . 'Please note that you have been tagged against CRM Request [request_id] having Case Nature [case_nature] and type [case_nature_type]. Please resolve at your earliest.
', 'updated_by' => 3, 'status' => 0)
        ));
        DB::table('notifications')->insert(array(
            array('id' => 66, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM Request Escalation', 'type_id' => 1, 'subject' => 'Crm Request: [request_id] Escalation: [escalation]', 'body' => 'Please note that CRM Request [request_id] has been Escalated to [escalation].
', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
