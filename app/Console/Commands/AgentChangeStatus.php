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
            RvShipmentAssignAgent::where('rv_assign_agent_status_id', 6)
            ->where('rv_state_id', 2)
            ->where('unresponsive_attempt_time', '<', Carbon::today()) // if current day has passed
            ->update(['rv_state_id' => 3]);
        } catch (\Throwable $th) {
            $this->createRvCronLog($th->getMessage());
        }
    }
}
