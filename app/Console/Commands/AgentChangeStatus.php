<?php

namespace App\Console\Commands;

use App\Http\Models\RvShipmentAssignAgent;
use App\RvCronLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AgentChangeStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:changeStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To open status RVR shipment for the agents to get ticket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    // create Rv Cron Log
    public function createRvCronLog($message)
    {
        RvCronLog::create([
            'message' => $message,
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            RvShipmentAssignAgent::where('rv_assign_agent_status_id', 6) // when assign agent status is unresponsive
            ->where('rv_state_id', 2) // when assign agent state is unassigned
            ->where('unresponsive_attempt_time', '<', Carbon::now()->subHours(6)->toDateTimeString()) // when last state unassigned 3 hours has been passed
            ->update(['rv_state_id' => 3]);// update state to open so any agent can get the ticket
        } catch (\Throwable $th) {
            $this->createRvCronLog($th->getMessage());
        }
    }
}
