<?php

namespace App\Jobs;

use App\Http\Controllers\Webhook\WebhookLogController;
use App\Http\Traits\RvTrait;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class BotCallDispatch implements ShouldQueue
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
        $botRecordData = $this->botCallingDpataSet($this->shipmentId);

        if ($botRecordData) {

            $client = new Client(['base_uri' => $botRecordData['base_uri'], 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120, 'verify' => false]);
            $response = $client->post('', [
                'json' => $botRecordData['post']
            ]);

            $status_code = $response->getStatusCode();
            $response = $response->getBody()->getContents();
            $response = json_decode($response);
            WebhookLogController::zong_call_log($botRecordData['user_id'],  $status_code, $this->shipmentId, 1, json_encode($response));

            if ($response->message == 'Data Not Found' && $response->code == 400) {
                $this->inValidEntityEntertain($botRecordData['post']['tracking_number']);
            }

        } else {
            return json_encode(['status' => 0, 'message' => 'Shipment isn`t at the bot call prefernce']);
        }
       
    }
}
