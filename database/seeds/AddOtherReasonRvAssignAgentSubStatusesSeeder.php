<?php
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AddOtherReasonRvAssignAgentSubStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $timestamp = Carbon::now()->format('Y-m-d H:i:s');
       DB::table('rv_assign_agent_sub_statuses')->insert(array(
        array('name' => 'Others', 'rv_assign_agent_status_id' => 6, 'is_active' => 1,'created_at'=> $timestamp,'updated_at'=>$timestamp)
       ));
    }
}
