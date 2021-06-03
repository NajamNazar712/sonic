<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Models\Shipment;
use App\Http\Models\Webhook\ShipmentStatusSubscription;
use App\Jobs\ProcessShipmentStatusWebhook;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ShipmentStatusWebhookController extends Controller
{
    static public function webhook_subscription($shipment_id, $shipper_status_id){

        $shipment = Shipment::find($shipment_id);
        $user_id = $shipment->user_id;

        $subscriber = ShipmentStatusSubscription::where('user_id', $user_id)->where('status', 1);
        if($subscriber->exists()){
            $subscriber = $subscriber->first();
            $data = array();

            $data['user_id'] = $user_id;
            $data['tracking_number'] = $shipment->tracking_number;
            $data['status'] = $shipment->status_shipper->name;
            $data['url'] = $subscriber->url;
            dispatch(new ProcessShipmentStatusWebhook($data));

        }
    }

    static public function webhook_dispatch($url, $user_id, $tracking_number, $status){
        $attempts = 5;
        $client = new Client(['base_uri' => $url, 'http_errors' => FALSE, 'connect_timeout' => 3, 'timeout' => 3]);
        for($i = 0; $i < $attempts; $i++){
            try{

                $response = $client->post('', [
                    'form_params' => [
                        'tracking_number' => $tracking_number,
                        'status' => $status
                    ]
                ]);
                $status_code = $response->getStatusCode();
                if ($status_code != 200) {
                    if($i == 4){
                        ShipmentStatusSubscription::where('user_id', $user_id)->update(['status' => 0]);
                        break;
                    }
                    continue;
                }
                break;
            }
            catch(RequestException $e){
                if($i == 4){
                    ShipmentStatusSubscription::where('user_id', $user_id)->update(['status' => 0]);
                    break;
                }
                continue;
            }
        }

    }


}
