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
    /**
     * Old existing users from your previous code
     * هؤلاء users already use JSON payload
     */
    private const LEGACY_JSON_USERS = [30860, 12221, 5333];

    /**
     * Add your Google Sheet specific user IDs here
     * Example only - replace/add real IDs
     */
    private const GOOGLE_SHEET_USERS = [
        52873,
        52975,
        52974,
        52946,
        52944,
        52943,
        52947,
        52945
    ];

    static public function webhook_subscription($shipment_id, $shipper_status_id, $status_reason_id = NULL)
    {
        $date = Carbon::now()->toDateTimeString();
        $shipment = Shipment::find($shipment_id);
        $user_id = $shipment->user_id;

        $subscriber = ShipmentStatusSubscription::where('user_id', $user_id)->where('status', 1);

        if ($subscriber->exists()) {
            $subscriber = $subscriber->first();

            $data = array();
            $data['user_id'] = $user_id;
            $data['tracking_number'] = $shipment->tracking_number;
            $data['order_id'] = $shipment->order_id;

            $status = ShipmentStatusesForShipperWebhook::where('user_id', $user_id)
                ->where('status_id', $shipper_status_id);

            if ($status->exists()) {
                $status = $status->first();
                $data['status'] = $status->webhook_status;
            } else {
                $data['status'] = ShipmentStatus::find($shipper_status_id)->name;
            }

            if ($status_reason_id) {
                $data['reason'] = ShipmentStatusReason::find($status_reason_id)->name;
            } else {
                $data['reason'] = NULL;
            }

            if ($shipper_status_id == 5) {
                $shipment_otp = ShipmentOtp::where('shipment_id', $shipment_id)->first();
                if ($shipment_otp) {
                    $data['otp'] = $shipment_otp->otp;
                } else {
                    $data['otp'] = NULL;
                }
            } else {
                $data['otp'] = NULL;
            }

            $data['date_time'] = $date;
            $data['url'] = $subscriber->url;

            dispatch(new ProcessShipmentStatusWebhook($data));
        }
    }

    /**
     * Old existing users from previous code
     */
    private static function isLegacyJsonUser($user_id): bool
    {
        return in_array((int) $user_id, self::LEGACY_JSON_USERS, true);
    }

    /**
     * New Google Sheet specific users
     */
    private static function isGoogleSheetUser($user_id): bool
    {
        return in_array((int) $user_id, self::GOOGLE_SHEET_USERS, true);
    }

    /**
     * Detect Google Apps Script webhook URLs also
     */
    private static function isGoogleScriptUrl($url): bool
    {
        return str_contains($url, 'script.google.com')
            || str_contains($url, 'script.googleusercontent.com');
    }

    /**
     * Decide whether this webhook should be sent as JSON
     *
     * JSON will be used for:
     * 1) old legacy JSON users
     * 2) Google Sheet users
     * 3) Google Apps Script URLs
     */
    private static function shouldSendJsonWebhook($user_id, $url): bool
    {
        return self::isLegacyJsonUser($user_id)
            || self::isGoogleSheetUser($user_id)
            || self::isGoogleScriptUrl($url);
    }

    /**
     * Default old behavior for normal users
     */
    private static function sendFormWebhook(Client $client, array $payload)
    {
        return $client->post('', [
            'form_params' => $payload,
        ]);
    }

    /**
     * JSON behavior for legacy + Google Sheet users
     */
    private static function sendJsonWebhook(Client $client, array $payload)
    {
        return $client->post('', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);
    }

    /**
     * Centralized sender
     */
    private static function sendWebhookByCondition(Client $client, $user_id, $url, array $payload)
    {
        if (self::shouldSendJsonWebhook($user_id, $url)) {
            return self::sendJsonWebhook($client, $payload);
        }

        return self::sendFormWebhook($client, $payload);
    }

    static public function webhook_dispatch($url, $user_id, $tracking_number, $status, $date, $reason = NULL, $otp = NULL, $orderId = NULL)
    {
        $attempts = 5;

        $client = new Client([
            'base_uri' => $url,
            'http_errors' => false,
            'connect_timeout' => 30,
            'timeout' => 30,
        ]);

        $notification_data = [
            'user_id' => $user_id,
            'url' => $url
        ];

        for ($i = 0; $i < $attempts; $i++) {
            try {
                $payload = [];
                $payload['tracking_number'] = $tracking_number;
                $payload['order_id'] = $orderId ?? '-';
                $payload['status'] = $status;
                $payload['date_time'] = $date;
                $payload['courier_name'] = 'Trax';

                if ($reason) {
                    $payload['reason'] = $reason;
                }

                if ($otp) {
                    $payload['otp'] = $otp;
                }

                // Optional: if you want extra security for Google Sheet
                // $payload['secret'] = 'your-secret-key-here';

                $response = self::sendWebhookByCondition($client, $user_id, $url, $payload);

                if ($response instanceof \Psr\Http\Message\ResponseInterface) {
                    $status_code = $response->getStatusCode();
                } else {
                    $status_code = 500;
                }

                if (in_array($status_code, [200, 201, 202, 204])) {
                    break;
                }

            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                $res = $e->getMessage();
                $status_code = 404;

                $notification_data['status_code'] = $status_code;
                $notification_data['message'] = $res;

                WebhookLogController::shipment_status_log($user_id, $status_code, $res);

                if ($i == 4) {
                    ShipmentStatusSubscription::where('user_id', $user_id)->update(['status' => 0]);
                    WebhookLogController::shipper_webhook_log($user_id, 'Disabled by Webhook');
                    NotificationsController::send(167, $notification_data);
                    break;
                }

                continue;

            } catch (RequestException $e) {
                $status_code = 400;
                $res = $e->getMessage();

                $notification_data['status_code'] = $status_code;
                $notification_data['message'] = $res;

                WebhookLogController::shipment_status_log($user_id, $status_code, $res);

                if ($i == 4) {
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