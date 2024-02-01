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
    protected $signature = 'agent:sarnotification';

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
        // try {
            $currentDateTime1 = Carbon::now()->toDateTimeString();
            // Create a Carbon instance from the formatted string
            $currentDateTime = Carbon::parse($currentDateTime1);
            

            // rv_assign_agent_status_id' 7 (Shipper Advised Request) and Check If State Is 2 (Unassign Assigned)
            $sendEmails = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 7)
                ->where('rv_state_id', 2)
                ->where('unresponsive_count', 2)
                //selects older records, i.e., records that were updated more than 12 hours ago.            
                ->where('updated_at', '<', $currentDateTime->subHours(16))
                ->where('unresponsive_email_count', '<', 1);
                // ->get();

            // rv_assign_agent_status_id' 8 (Refusal on call) and Check If State Is 2 (Unassign Assigned)
            $sendEmailofRefusalShipments = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 8)
            ->where('updated_at', '<', $currentDateTime->subHours(24))
            ->where('rv_state_id', 2);
            // ->get();

            //Combine the results for sending in single email
            $sendEmail = $sendEmails->union($sendEmailofRefusalShipments)->get();

            // If there are shipments that meet the conditions, send Email Notification to shipper for each shipment

            if ($sendEmail->isNotEmpty()) {
                NotificationsController::send(220, $sendEmail);

                foreach ($sendEmail as $shipment) {
                    // if shipment status is unresponsive Increment the unresponsive_email_count for each shipment after sending the email
                    if($shipment->rv_assign_agent_status_id == 6){
                        $shipment->increment('unresponsive_email_count');
                        $shipment->unresponsive_email_time = $currentDateTime;
                        $shipment->save();
                    }
                }
            }

            // When there is no response from the shipper within 24 hours of the "Shipper Advise Requested" status being set on the shipment, 
            // the system will automatically update the shipment status to "Return Confirm."
            $shipmentsToUpdate = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 7)
                ->where('rv_state_id', 2)
                ->where('unresponsive_count', 2)
                ->where('unresponsive_email_count', '>', 0)
                ->where('unresponsive_email_time', '<', $currentDateTime->subHours(48))
                ->get();

            if ($shipmentsToUpdate->isNotEmpty()) {
                foreach ($shipmentsToUpdate as $shipment) {
                    // $shipment->update(['rv_assign_agent_status_id' => 1, 'rv_assign_agent_sub_status_id' => 4]);
                    $shipment->update(['rv_assign_agent_status_id' => 1, 'rv_assign_agent_sub_status_id' => null,'rv_state_id' => 4]);

                    // $request = $shipment->request->add([
                    //     'shipment_id' => $shipment->shipment_id, 
                    //     'remarks' => $shipment->remarks, 
                    //     'rv_assign_agent_sub_status_id' => null,
                    // ]);
                    $request = [
                        'shipment_id' => $shipment->shipment_id,
                        'remarks' => $shipment->remarks,
                        'rv_assign_agent_sub_status_id' => null,
                    ];
                    $this->return_confirm($request);

                    $data = [
                        'rv_shipment_assign_agent_id' => $shipment->id,
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

            // When there is no response from the shipper within 24 hours of the "Shipper Advise Requested" status after refusal on call status, 
            // the system will automatically update the shipment status to "Return Confirm."
            
            $refusal_call_shipment_update = RvShipmentAssignAgent::where('rv_assign_agent_status_id', 8)
                ->where('rv_state_id', 2)
                ->where('updated_at', '>', $currentDateTime->subHours(24))
                ->get();

            if ($refusal_call_shipment_update->isNotEmpty()) {
                foreach ($refusal_call_shipment_update as $shipment) {
                    // $shipment->update(['rv_assign_agent_status_id' => 1, 'rv_assign_agent_sub_status_id' => null,'rv_state_id' => 4]);

                    // $request = $shipment->request()->add([
                    //     'shipment_id' => $shipment->shipment_id, 
                    //     'remarks' => $shipment->remarks, 
                    //     'rv_assign_agent_sub_status_id' => null,
                    // ]);
                    $requestData = [
                        'shipment_id' => $shipment->shipment_id,
                        'remarks' => $shipment->remarks,
                        'rv_assign_agent_sub_status_id' => null,
                    ];
                    
                    $request = request()->merge($requestData);

                    $data = [
                        'rv_shipment_assign_agent_id' => $shipment->id,
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

        // } catch (\Throwable $th) {
        //     $this->createRvCronLog($th->getMessage());
        // }
    }
}