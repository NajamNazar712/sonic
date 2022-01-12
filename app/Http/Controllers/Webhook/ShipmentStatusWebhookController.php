<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\NotificationsController;
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

        $notification_data = ['user_id' => $user_id, 'url' => $url];
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
                if (!in_array($status_code, [200, 201, 202, 204])) {

                    $res = NULL;

                    $body = $response->getBody();

                    $result = json_decode($body);

                    if (json_last_error() === 0 || is_object($result)) {
                        $res = $body;
                    }
                    WebhookLogController::shipment_status_log($user_id, $status_code, $res);

                    if($i == 4){
                        $notification_data['status_code'] = $status_code;
                        ShipmentStatusSubscription::where('user_id', $user_id)->update(['status' => 0]);
                        NotificationsController::send(167, $notification_data);
                        break;
                    }
                    continue;
                }
                break;
            }
            catch(RequestException $e){
                if ($e->hasResponse()) {
                    $res = NULL;
                    $response = $e->getResponse();
                    $status_code = $response->getStatusCode();
                    $body = $response->getBody();
                    $notification_data['status_code'] = $status_code;
                    $result = json_decode($body);

                    if (json_last_error() === 0 || is_object($result)) {
                        $res = $body;
                    }
                    WebhookLogController::shipment_status_log($user_id, $status_code, $res);
                }
                if($i == 4){
                    ShipmentStatusSubscription::where('user_id', $user_id)->update(['status' => 0]);
                    NotificationsController::send(167, $notification_data);
                    break;
                }
                continue;
            }
        }

    }


}
