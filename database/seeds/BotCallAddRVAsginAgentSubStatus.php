<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BotCallAddRVAsginAgentSubStatus extends Seeder
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
        DB::table('rv_assign_agent_sub_statuses')->insert(array(
            array('name' => 'Not Answer', 'rv_assign_agent_status_id' => 6, 'is_active' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Picked but no option selected', 'rv_assign_agent_status_id' => 6, 'is_active' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee OPT to Reattempt', 'rv_assign_agent_status_id' => 2, 'is_active' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee OPT to Return', 'rv_assign_agent_status_id' => 2, 'is_active' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee OPT to Manual Call', 'rv_assign_agent_status_id' => 1, 'is_active' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
