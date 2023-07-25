<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\RvShipmentAssignAgent;
use App\RvCronLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AgentSarNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:SarNotification ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email notifications to shippers for "Shipper Advise Requested" shipments & updates to "Return Confirm" status after 24 hours if no response';

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
            $currentDateTime = Carbon::now();
        
            // rv_assign_agent_status_id' 7 (Shipper Advised Request) and Check If State Is 2 (Unassign Assigned)
            $shipmentsToUpdate = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 7)
                ->where('rv_state_id', 2)
                ->where('updated_at', '<', $currentDateTime->subHours(4)); // Check if 4 hours have passed
        
            // If All Conditions Are Being Met and 4 hours Have Passed, then send Email Notification to shipper to take action on the shipment 
            if ($shipmentsToUpdate->exists()) {
                NotificationsController::send(220, 0);
            } 
            
            else {
                // Else If there is no response from the shipper within 24 hours of the "Shipper Advise Requested" status being set on the shipment, 
                // the system will automatically update the shipment status to "Return Confirm."
                $shipmentsToUpdate = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 7)
                    ->where('rv_state_id', 2)
                    ->where('updated_at', '<', $currentDateTime->subHours(24)); // Check if 24 hours have passed
        
                if ($shipmentsToUpdate->exists()) {
                    $shipmentsToUpdate->update(['rv_assign_agent_status_id' => 3]); // Update the status to "Return Confirm"
                }
            }
        } catch (\Throwable $th) {
            $this->createRvCronLog($th->getMessage());
        }
    }
}
