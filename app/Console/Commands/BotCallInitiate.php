<?php

namespace App\Console\Commands;

use App\Http\Controllers\Webhook\WebhookLogController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\RvCronLog;
use App\RvShipmentTicket;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


class BotCallInitiate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:botcallunresponsive';

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
            $timeEnd = Carbon::parse(now())->subHour(2)->format('Y-m-d H:i:s'); // Get the timestamp of two hours ago
            $timeStart = Carbon::parse($timeEnd)->subMinute(15)->format('Y-m-d H:i:s');
            // Now, re-initiate process for the retrieved shipment_ids after unresponsive one
            $shipments = RvShipmentAssignAgent::where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=',$timeStart], ['unresponsive_attempt_time', '<=', $timeEnd],'unresponsive_count' => 1, 'rv_assign_agent_status_id'=>6])->pluck('shipment_id');
            Log::channel('cronJobLog')->info('s ' . 'Log after second call  record' . count($shipments));

            if (count($shipments) > 0) {            
                foreach($shipments as $shipmentId){
                    if (GlobalSettings::where(['type' => 'bot_call_enable_disable', 'setting_value' => 1])->exists()) {
                        if (RvShipmentTicket::where('shipment_id', $shipmentId)->whereNull('deleted_at')->where('is_bot', 1)->exists()) {
                            $base_uri = 'https://cap.zong.com.pk:8444/vpbx-apis/roboCalls/outboundCall';
                            RvShipmentTicket::where('shipment_id', $shipmentId)->update(['in_progress' => 1]);
                            $shipment = Shipment::with(['user:id,name,brand_name'])->select('user_id', 'consignee_phone_number_1', 'consignee_name', 'tracking_number', 'amount')->find($shipmentId);
                            $post = [
                                'vpbx_id' => '66bdfd18cb67f',
                                'caller_id' => preg_replace("/[^a-zA-Z0-9]+/", "", $shipment->consignee_phone_number_1),
                                'tracking_number' => $shipment->tracking_number,
                                'cod_amount' => $shipment->amount,
                                'brand_name' => $shipment->user->name ?? $shipment->user->brand_name,
                                'customer_name' => $shipment->consignee_name,
                            ];
                            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60, 'verify' => false]);
                            $response = $client->post('', [
                                    'json' => $post
                                ]);
                            $status_code = $response->getStatusCode();
                            $response = $response->getBody()->getContents();
                            $response = json_decode($response);
                            Log::channel('cronJobLog')->info('s ' . 'Log after second call  with response' . json_encode($response));
                            WebhookLogController::shipment_status_log($shipment->user_id, $status_code, json_encode($response));
                        } else {
                            return json_encode(['status' => 0, 'message' => 'Shipment isn`t at the bot call prefernce']);
                        }
                    }
                }
            }

            //  Now, re-initiate process for the retrieved shipment_ids after unresponsive two            
            $shipments = RvShipmentAssignAgent::where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'unresponsive_count' => 2, 'rv_assign_agent_status_id' => 6])->pluck('shipment_id');
            Log::channel('cronJobLog')->info('s ' . 'Log after third call  record' . count($shipments));

            if(count($shipments) > 0){
                foreach($shipments as $shipmentId){
                    if (GlobalSettings::where(['type' => 'bot_call_enable_disable', 'setting_value' => 1])->exists()) {
                        if (RvShipmentTicket::where('shipment_id', $shipmentId)->whereNull('deleted_at')->where('is_bot', 1)->exists()) {
                            $base_uri = 'https://cap.zong.com.pk:8444/vpbx-apis/roboCalls/outboundCall';
                            RvShipmentTicket::where('shipment_id', $shipmentId)->update(['in_progress' => 1]);
                            $shipment = Shipment::with(['user:id,name,brand_name'])->select('user_id', 'consignee_phone_number_1', 'consignee_name', 'tracking_number', 'amount')->find($shipmentId);
                            $post = [
                                'vpbx_id' => '66bdfd18cb67f',
                                'caller_id' => preg_replace("/[^a-zA-Z0-9]+/", "", $shipment->consignee_phone_number_1),
                                'tracking_number' => $shipment->tracking_number,
                                'cod_amount' => $shipment->amount,
                                'brand_name' => $shipment->user->name ?? $shipment->user->brand_name,
                                'customer_name' => $shipment->consignee_name,
                            ];
                            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60, 'verify' => false]);
                            $response = $client->post('', [
                                    'json' => $post
                                ]);
                            $status_code = $response->getStatusCode();
                            $response = $response->getBody()->getContents();
                            $response = json_decode($response);
                            Log::channel('cronJobLog')->info('s ' . 'Log after call dispatched with response third-call' . json_encode($response));
                            WebhookLogController::shipment_status_log($shipment->user_id, $status_code, json_encode($response));
                        } else {
                            return json_encode(['status' => 0, 'message' => 'Shipment isn`t at the bot call prefernce']);
                        }
                    }
                }
            }
            // RvShipmentAssignAgent::where('rv_assign_agent_status_id', 6)
            // ->where('rv_state_id', 2)
            // ->where('unresponsive_attempt_time', '<', Carbon::today()) // if current day has passed
            // ->update(['rv_state_id' => 3]);

            //Query for Making Shipments Enable again in Get Tickets After their "Unresponsive" Status is Submitted.
            // RvShipmentTicket::where('in_progress', 1)->update(['in_progress' => 0]);


            //Make record of return/dashboard cards count daily to mantain history
            


        } catch (\Throwable $th) {
            $this->createRvCronLog($th->getMessage().' Unresponsive Count ');
        }
    }
}
