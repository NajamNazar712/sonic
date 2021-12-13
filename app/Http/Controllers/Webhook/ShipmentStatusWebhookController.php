<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Webhook\ShipmentStatusesForShipperWebhook;
use App\Http\Models\Webhook\ShipmentStatusSubscription;
use App\Jobs\ProcessShipmentStatusWebhook;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ShipmentStatusWebhookController extends Controller
{
    static public function webhook_subscription($shipment_id, $shipper_status_id){

        $date = Carbon::now()->toDateTimeString();
        $shipment = Shipment::find($shipment_id);
        $user_id = $shipment->user_id;

        $subscriber = ShipmentStatusSubscription::where('user_id', $user_id)->where('status', 1);
        if($subscriber->exists()){
            $subscriber = $subscriber->first();
            $data = array();

            $data['user_id'] = $user_id;
            $data['tracking_number'] = $shipment->tracking_number;
            $status = ShipmentStatusesForShipperWebhook::where('user_id',$user_id)->where('status_id',$shipper_status_id);
            if($status->exists())
            {
                $status = $status->first();
                $data['status'] = $status->webhook_status;
            }
            else{
                $data['status'] = ShipmentStatus::find($shipper_status_id)->name;
            }

            $data['date_time'] = $date;
            $data['url'] = $subscriber->url;
            dispatch(new ProcessShipmentStatusWebhook($data));

        }
    }

    static public function webhook_dispatch($url, $user_id, $tracking_number, $status, $date){
        $attempts = 5;
        $client = new Client(['base_uri' => $url, 'http_errors' => FALSE, 'connect_timeout' => 3, 'timeout' => 3]);
        for($i = 0; $i < $attempts; $i++){
            try{

                $response = $client->post('', [
                    'form_params' => [
                        'tracking_number' => $tracking_number,
                        'status' => $status,
                        'date_time' => $date
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
