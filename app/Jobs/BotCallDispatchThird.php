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
        try {
           
            $botRecordData = $this->botCallingDataSet($this->shipmentId,3);
            if ($botRecordData) {
                $token = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJiNjYzMTQyNi05ODQyLTQzNjEtYmU1Mi1lZGI3OWEwMTkyOGMiLCJpYXQiOjE3NjI1MDg4ODUsImV4cCI6NDkxODE4MjQ4NSwidHlwZSI6ImV4dGVybmFsQXBpIn0.J7B45SejW4pDHLh5sYSDCMyayRLgOJpZCWNlJ58Esek";
                $client = new Client(['base_uri' => $botRecordData['base_uri'], 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120, 'verify' => false]);
                // Send POST request
                // if(in_array($botRecordData['user_id'],[16344, 26249, 13060, 5553, 30230, 49055, 50475, 27425, 31794, 31902, 31538, 51353, 19943, 8732, 44233, 37631, 48558, 35049, 25244, 49028,1049])){
                    $response = $client->post('', [
                        'headers' => [
                            'Authorization' => $token,
                            'Content-Type'  => 'application/json',
                        ],
                        'json' => $botRecordData['post'],
                    ]);
                    $channelname = 'whatsapp';
                // }else{
                //     $response = $client->post('', [
                //         'json' => $botRecordData['post']
                //     ]);
                // }

                $status_code = $response->getStatusCode();
                $response = $response->getBody()->getContents();
                $response = json_decode($response);
                WebhookLogController::zong_call_log($botRecordData['user_id'],  $status_code, $this->shipmentId, 3, json_encode($response), $channelname);
                if ($response->message == 'Data Not Found' && $response->code == 400) {
                    Log::channel('botCallJobLog')->info('s ' . 'Log after  respsone condition call second-record' . $response->message);
                    $this->inValidEntityEntertain($botRecordData['post']['tracking_number']);
                }
            }else{
                return json_encode(['status'=>0,'message'=>'Shipment isn`t at the bot call prefernce']);
            }
        } catch (\Throwable $th) {
            // Log::channel('botCallJobLog')->info(' Unresponsive Count ');

            $this->createRvCronLog($th->getMessage() . ' Unresponsive Count ');
        }
       
    }
}
