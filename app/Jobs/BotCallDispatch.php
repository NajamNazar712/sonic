<?php

namespace App\Jobs;

use App\Http\Controllers\Webhook\WebhookLogController;
use App\Http\Models\Shipment;
use App\RvShipmentTicket;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BotCallDispatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shipmentId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->queue = 'bot_call_shipment';
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

        $base_uri = 'https://cap.zong.com.pk:8444/vpbx-apis/roboCalls/outboundCall';
        RvShipmentTicket::where('shipment_id', $this->shipmentId)->update(['in_progress' => 1]);
        $shipment = Shipment::with(['user:id,name,brand_name'])->select('user_id', 'consignee_phone_number_1', 'consignee_name', 'tracking_number', 'amount')->find($this->shipmentId);
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
        WebhookLogController::shipment_status_log($shipment->user_id, $status_code, json_encode($response));
    }
}
