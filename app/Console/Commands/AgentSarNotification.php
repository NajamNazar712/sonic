<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Traits\RvTrait;
use App\RvCronLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AgentSarNotification extends Command
{
    use RvTrait;
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
            //selects older records, i.e., records that were updated more than 12 hours ago.            
            ->where('updated_at', '<', $currentDateTime->subHours(12))
            ->where('unresponsive_email_count', '<', 1)
            ->get();

            // If there are shipments that meet the conditions, send Email Notification to shipper for each shipment
            dd($sendEmail,$currentDateTime->subHours(12));
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
                    ->get();
        
                if ($shipmentsToUpdate->isNotEmpty()) {
                    foreach ($shipmentsToUpdate as $shipment) {
                        $shipment->update(['rv_assign_agent_status_id' => 1, 'rv_assign_agent_sub_status_id' => 4]);

                        $request = $shipment->request->add(['shipment_id' => $shipment->shipment_id, 'is_fake_status' => $shipment->is_fake_status, 'remarks' => $shipment->remarks, 
                        'call_to_id' => $shipment->call_to_id, 'rv_assign_agent_sub_status_id' => $shipment->rv_assign_agent_sub_status_id]);
                        $this->return_confirm($request);


                        $data = ['rv_shipment_assign_agent_id' => $shipment->id,
                        'agent_id' => $shipment->agent_id,
                        'shipments_journey_id' => $shipment->shipments_journey_id,
                        'last_shipments_journey_id' => $shipment->last_shipments_journey_id,
                        'shipment_id' => $shipment->shipment_id,
                        'rv_assign_agent_status_id' => $shipment->rv_assign_agent_status_id,
                        'rv_assign_agent_sub_status_id' => $shipment->rv_assign_agent_sub_status_id,
                        'rv_state_id' => $shipment->rv_state_id,
                        'updated_type_id' => 1,
                        'updated_by_id' =>  Null,
                        'is_fake_status' => $shipment->is_fake_status,
                        'rv_fake_status_id' => $shipment->rv_fake_status_id,
                        'remarks' => $shipment->remarks,
                        'call_to_id' => $shipment->call_to_id,
                    ];
                        $this->data_rv_shipment_assign_agent_details($data);
                    }
                }
            }
        } catch (\Throwable $th) {
            $this->createRvCronLog($th->getMessage());
        }
    }
}
