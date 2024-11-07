<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\ReturnController;
use App\Http\Models\RvShipmentAssignAgent;
use App\RvCronLog;
use App\RVDashboardDailyCount;
use App\RvShipmentTicket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\type;

class AgentChangeStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:changestatus';

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
            // RvShipmentAssignAgent::where('rv_assign_agent_status_id', 6)
            // ->where('rv_state_id', 2)
            // ->where('unresponsive_attempt_time', '<', Carbon::today()) // if current day has passed
            // ->update(['rv_state_id' => 3]);

            //Query for Making Shipments Enable again in Get Tickets After their "Unresponsive" Status is Submitted.
            $shipments = RvShipmentTicket::where('in_progress', 1)->where('is_bot',0)->get();
            // ->update(['in_progress' => 0,'rv_shipment_tickets.updated_at'=> 'rv_shipment_tickets.created_ats']);
            foreach($shipments as $shipment){
                $shipment->in_progress = 0;
                $shipment->updated_at = $shipment->created_at;
                $shipment->save();
            }
            
            $upshipments = RvShipmentTicket::whereDate('created_at','>','2024-11-03')->where('in_progress', 0)->where('is_bot',0)->get();
            // ->update(['in_progress' => 0,'rv_shipment_tickets.updated_at'=> 'rv_shipment_tickets.created_ats']);
            foreach($upshipments as $shipment){
                $shipment->updated_at = $shipment->created_at;
                $shipment->save();
            }

            //Make record of return/dashboard cards count daily to mantain history
            if(RVDashboardDailyCount::whereDate('created_at', Carbon::today())->doesntExist()) //Max 1 record should be created each day
            {
                $data = new ReturnController();         //get same data which is shown at return/dashboard
                $response = $data->return_view_data();
                $response = json_decode(json_encode($response))->original->stats; //convert and filter json response
    
                $RvDailyCount = new RVDashboardDailyCount();
                $RvDailyCount->data = $response;
                $RvDailyCount->save();
            }


        } catch (\Throwable $th) {
            $this->createRvCronLog($th->getMessage());
        }
    }
}
