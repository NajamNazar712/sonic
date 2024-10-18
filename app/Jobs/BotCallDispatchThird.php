<?php

namespace App\Jobs;

use App\Http\Controllers\Webhook\WebhookLogController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\RvShipmentTicket;
use GuzzleHttp\Client;
use App\Http\Traits\RvTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class BotCallDispatchThird implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RvTrait;

    protected $shipmentId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->queue = 'bot_call_shipment_third';
        $this->shipmentId = $data;

    }

    /**
     * Execute the job.
     *
     * Method : handle
     * Parameter: Shipment ID
     * Usage: Bot call implementation through a zong service specifiy for the some parameter using  api 
     * @return void
     */
    public function handle()
    {

        //
        $environment = config('app.env');
        $post = $this->BotCallingDataSet($this->shipmentId);

        if ($post) {
            if(RvShipmentTicket::where('shipment_id', $this->shipmentId)->whereNull('deleted_at')->where('is_bot',1)->exists() && Shipment::whereIn('shipper_status_id', [12, 52, 66])->where('id', $this->shipmentId)->exists()){
                $base_uri = 'https://cap.zong.com.pk:8444/vpbx-apis/roboCalls/outboundCall';
                RvShipmentTicket::where('shipment_id', $this->shipmentId)->update(['in_progress' => 1]);
                $shipment = Shipment::with(['user:id,name,brand_name'])->select('user_id', 'consignee_phone_number_1', 'consignee_name', 'tracking_number', 'amount')->find($this->shipmentId);
                // Clean the phone number by removing non-alphanumeric characters    
                $cleaned_phone = preg_replace("/[^a-zA-Z0-9]+/", "", $shipment->consignee_phone_number_1);
                if (substr($cleaned_phone, 0, 2) === "00") {
                    // Remove one "0" by replacing "00" at the start with "0"
                    $final_phone = preg_replace("/^00/", "0", $cleaned_phone);
                } else {
                    // No leading "00", so leave the cleaned phone number as is
                    $final_phone = $cleaned_phone;
                }
                $post = [
                    'vpbx_id' => '66bdfd18cb67f',
                    'caller_id' => $final_phone,
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
                WebhookLogController::zong_call_log($shipment->user_id,  $status_code, $this->shipmentId, 3, json_encode($response));
            }
        }else {
            return json_encode(['status' => 0, 'message' => 'Shipment isn`t at the bot call prefernce']);
        }
       
    }
}
