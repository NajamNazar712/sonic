<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentOtp;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
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
    static public function webhook_subscription($shipment_id, $shipper_status_id, $status_reason_id = NULL){

        $date = Carbon::now()->toDateTimeString();
        $shipment = Shipment::find($shipment_id);
        $user_id = $shipment->user_id;

        $subscriber = ShipmentStatusSubscription::where('user_id', $user_id)->where('status', 1);
        if($subscriber->exists()){
            $subscriber = $subscriber->first();
                       $data = array();
            $data['user_id'] = $user_id;
            $data['tracking_number'] = $shipment->tracking_number;
            $data['order_id'] = $shipment->order_id;
            $status = ShipmentStatusesForShipperWebhook::where('user_id',$user_id)->where('status_id',$shipper_status_id);
            if($status->exists())
            {
                $status = $status->first();
                $data['status'] = $status->webhook_status;
            }
            else{
                $data['status'] = ShipmentStatus::find($shipper_status_id)->name;
            }

            if($status_reason_id){
                $data['reason'] = ShipmentStatusReason::find($status_reason_id)->name;
            }
            else{
                $data['reason'] = NULL;
            }

            if($shipper_status_id == 5){
                $shipment_otp = ShipmentOtp::where('shipment_id', $shipment_id)->first();
                if($shipment_otp){
                    $data['otp'] = $shipment_otp->otp;
                }
                else{
                    $data['otp'] = NULL;
                }
            }
            else{
                $data['otp'] = NULL;
            }

            $data['date_time'] = $date;
            $data['url'] = $subscriber->url;
            dispatch(new ProcessShipmentStatusWebhook($data));

        }
    }

    static public function webhook_dispatch($url, $user_id, $tracking_number, $status, $date, $reason = NULL, $otp = NULL,$orderId = NULL){
        $attempts = 5;
        $client = new Client(['base_uri' => $url, 'http_errors' => FALSE, 'connect_timeout' => 30, 'timeout' => 30]);
        
        $notification_data = ['user_id' => $user_id, 'url' => $url];
        // if ($user_id == 30860) {
        //     Log::channel('botCallJobLog')->info('s ' . 'Webhook log check-error' . json_encode($notification_data));
        // }
        for($i = 0; $i < $attempts; $i++){
            try{

                $payload = [];
                $payload['tracking_number'] = $tracking_number;
                $payload['order_id'] = $orderId ?? '-';
                $payload['status'] = $status;
                $payload['date_time'] = $date;
                $payload['courier_name'] = 'Trax';
                if($reason){
                    $payload['reason'] = $reason;
                }
                if($otp){
                    $payload['otp'] = $otp;
                }
                
                if (!in_array($user_id, [30860, 12221, 5333])) {
                    $response = $client->post('', [
                        'form_params' => $payload
                    ]);
                }

                if (in_array($user_id, [30860, 12221, 5333])) {
                    $response = $client->post('', [
                        'json' => $payload,
                    ]);
                    // $body = $response->getBody();
                    // $body->rewind();  // Rewind the stream to ensure we're reading from the start
                    // $responseBody = $body->getContents(); // Get the body content
                    // Log::channel('botCallJobLog')->info("Guzzle Response Body: " . $responseBody);
                    // $response1 = $client->post('', [
                    //     'form_params' => $payload
                    // ]);
                    // Log::channel('botCallJobLog')->info('s ' . 'Webhook log responseBody-body' . json_encode($response1));
                }
                if ($response instanceof \Psr\Http\Message\ResponseInterface) {
                    $status_code = $response->getStatusCode();
                } else {
                    $status_code = 500;
                }
                // if ($user_id == 30860) {
                //     Log::channel('botCallJobLog')->info('s ' . 'Webhook log Status-Code' . $status_code . "Payload" . json_encode($payload) ."User ID". $user_id);
                // }
                if (in_array($status_code, [200, 201, 202, 204])) {
                    break;
                }

            }
            catch (\GuzzleHttp\Exception\ConnectException $e) {
                // log the error here
                // if ($user_id == 30860) {
                //     Log::channel('botCallJobLog')->info('s ' . 'Webhook log check-error' . json_encode($e->getMessage()));
                // }
                $res = $e->getMessage();
                $status_code = 404;
                $notification_data['status_code'] = $status_code;
                $notification_data['message'] = $res;
                WebhookLogController::shipment_status_log($user_id, $status_code, $res);

                if($i == 4){
                    ShipmentStatusSubscription::where('user_id', $user_id)->update(['status' => 0]);
                    WebhookLogController::shipper_webhook_log($user_id, 'Disabled by Webhook');
                    NotificationsController::send(167, $notification_data);
                    break;
                }
                continue;
            }
            catch(RequestException $e){
                $status_code = 400;
                $res = $e->getMessage();
                // if ($user_id == 30860) {
                //     Log::channel('botCallJobLog')->info('s ' . 'Webhook log check-error' . json_encode($res));
                // }
                $notification_data['status_code'] = $status_code;
                $notification_data['message'] = $res;
                WebhookLogController::shipment_status_log($user_id, $status_code, $res);

                if($i == 4){
                    ShipmentStatusSubscription::where('user_id', $user_id)->update(['status' => 0]);
                    WebhookLogController::shipper_webhook_log($user_id, 'Disabled by Webhook');
                    NotificationsController::send(167, $notification_data);
                    break;
                }
                continue;
            }
        }

    }


}
