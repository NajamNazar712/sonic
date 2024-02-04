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
        DB::table('bolt_undelivered_reason_maps')->truncate();

        $data = [
            ['reason_id' => 23, 'status_attempt_count_1' => 7, 'status_attempt_count_2' => 7],
            ['reason_id' => 14, 'status_attempt_count_1' => 7, 'status_attempt_count_2' => 7],
            ['reason_id' => 25, 'status_attempt_count_1' => 7, 'status_attempt_count_2' => 7],
            ['reason_id' => 1, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12],
            ['reason_id' => 3, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12],
            ['reason_id' => 4, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12],
            ['reason_id' => 6, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12],
            ['reason_id' => 28, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12],
            ['reason_id' => 60, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12],
            ['reason_id' => 63, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12],
            ['reason_id' => 17, 'status_attempt_count_1' => 9, 'status_attempt_count_2' => 9],
            ['reason_id' => 18, 'status_attempt_count_1' => 9, 'status_attempt_count_2' => 9],
            ['reason_id' => 5, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12],
            ['reason_id' => 7, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12],
            ['reason_id' => 8, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12],
            ['reason_id' => 12, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12],
            ['reason_id' => 19, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12],
            ['reason_id' => 27, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12],
            ['reason_id' => 34, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12],
            ['reason_id' => 35, 'status_attempt_count_1' => 12, 'status_attempt_count_2' => 12],
            ['reason_id' => 40, 'status_attempt_count_1' =>8, 'status_attempt_count_2' =>8],
            ['reason_id' => 45, 'status_attempt_count_1' =>8, 'status_attempt_count_2' => 12],
            ['reason_id' => 31, 'status_attempt_count_1' => 56, 'status_attempt_count_2' => 56],
            ['reason_id' => 32, 'status_attempt_count_1' => 56, 'status_attempt_count_2' => 56],
            ['reason_id' => 33, 'status_attempt_count_1' => 56, 'status_attempt_count_2' => 56],
            ['reason_id' => 77, 'status_attempt_count_1' => 56, 'status_attempt_count_2' => 56],
        ];
        
        DB::table('bolt_undelivered_reason_maps')->insert($data);
    }
}
