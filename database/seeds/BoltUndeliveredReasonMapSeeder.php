<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoltUndeliveredReasonMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['reason_id' => 23, 'status_attempt_count_1' => 7, 'status_attempt_count_2' => 7, 'remarks' => ''],
            ['reason_id' => 14, 'status_attempt_count_1' => 7, 'status_attempt_count_2' => 7, 'remarks' => ''],
            ['reason_id' => 25, 'status_attempt_count_1' => 7, 'status_attempt_count_2' => 7, 'remarks' => ''],
            ['reason_id' => 1, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 3, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 4, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 6, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 28, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 60, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 63, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 17, 'status_attempt_count_1' => 9, 'status_attempt_count_2' => 9, 'remarks' => 'Rename to Hold on consignee request'],
            ['reason_id' => 18, 'status_attempt_count_1' => 9, 'status_attempt_count_2' => 9, 'remarks' => 'Rename to Hold On Shipper\'s Request'],
            ['reason_id' => 5, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 7, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 8, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 12, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 19, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 27, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12, 'remarks' => 'When marked as RCP, the ticket will not be visible to the agent. It will be updated to ID 20 once the delivery note is closed (verified)'],
            ['reason_id' => 34, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12, 'remarks' => ''],
            ['reason_id' => 35, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12, 'remarks' => 'When marked as RCP, the ticket will not be visible to the agent. It will be updated to ID 20 once the delivery note is closed (verified)'],
            ['reason_id' => 40, 'status_attempt_count_1' =>8, 'status_attempt_count_2' =>8, 'remarks' => ''],
            ['reason_id' => 45, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12, 'remarks' => ''],
        ];
        
        DB::table('bolt_undelivered_reason_maps')->insert($data);
    }
}
