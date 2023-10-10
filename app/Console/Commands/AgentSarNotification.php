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
    protected $signature = 'agent:SarNotification';

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
            $sendEmail = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 7)
            ->where('rv_state_id', 2)
            ->where('unresponsive_count', 2)
            ->where('updated_at', '<', $currentDateTime->subHours(12))
            ->where('unresponsive_email_count', '<', 1)
            ->get(); // Check if 4 hours have passed

            // If there are shipments that meet the conditions, send Email Notification to shipper for each shipment
            if ($sendEmail->isNotEmpty()) {
                NotificationsController::send(220, $sendEmail);

                foreach ($sendEmail as $shipment) {

                    // Increment the unresponsive_email_count for each shipment after sending the email
                    $shipment->increment('unresponsive_email_count');
                }

            }
            else {
                // Else If there is no response from the shipper within 24 hours of the "Shipper Advise Requested" status being set on the shipment, 
                // the system will automatically update the shipment status to "Return Confirm."
                $shipmentsToUpdate = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 7)
                    ->where('rv_state_id', 2)
                    ->where('unresponsive_count', 2)
                    ->where('unresponsive_email_count', '>', 0)
                    ->get(); // Check if unresponsive_email_count 6 which means that 24 hours have passed
        
                if ($shipmentsToUpdate->isNotEmpty()) {
                    foreach ($shipmentsToUpdate as $shipment) {
                        $shipment->update(['rv_assign_agent_status_id' => 1]);
                    }
                }
            }
        } catch (\Throwable $th) {
            $this->createRvCronLog($th->getMessage());
        }
    }
}
