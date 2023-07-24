<?php

namespace App\Console\Commands;

use App\RvCronLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Models\RvShipmentAssignAgent;

class AgentUnassignedTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:unassignedTicket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check If No Activity Is Being Performed For 30 Minutes Then Change State To 3 (Open)';

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
            //Check If State Is 1 (Assigned)
            RvShipmentAssignAgent::where('rv_state_id', 1)
            //Check If 30 Minutes Had Passed
            ->where('created_at','>', Carbon::now()->subMinutes(30)->toDateTimeString())
            //If All Condition Are Being Met Then Update Rv State To 3 (Open), Any Agent Can Now Get This Ticket
            ->update(['rv_state_id', 3]);
   
        } catch (\Throwable $th) {
            $this->createRvCronLog($th->getMessage());
        }
    }
}
