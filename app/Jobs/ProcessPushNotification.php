<?php

namespace App\Jobs;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\EmployeeNotificationHistory;
use App\Models\PusherNotification;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification_history;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(EmployeeNotificationHistory $notification_history)
    {
        $this->queue = 'process_push_notification';
        $this->notification_history = $notification_history;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $push_notification = $this->notification_history;
        $employee_device_token = EmployeeDeviceToken::where('employee_id', $push_notification->employee_id)
            ->where('employee_type_id', $push_notification->employee_type_id)
            ->select('device_token')->orderby('id', 'DESC');
        if ($employee_device_token->exists()) {
            $employee_device_token = $employee_device_token->first();
            $device_token = $employee_device_token->device_token;
            $data['title'] = $push_notification->title;
            $data['body'] = $push_notification->message;
            if($push_notification->screen_id != null){
                $data['screen_id'] = $push_notification->screen_id;
            }
            var_dump('Sendig to device token:');
            if ($data['title'] == 'Shipment Change Cod Amount') {
                $response = NotificationsController::sendFcmNotificationV1($employee_device_token->employee_id, $employee_device_token->employee_type_id, $employee_device_token->device_token, $data['title'], $data['body'],  $data['screen_id'] ?? null);
                var_dump('Sendig to device token:'. $response);
                if (isset($response['status'])) {
                    if ($response['status'] == 200) {
                        $push_notification->status = 1;
                        $push_notification->save();
                    }else{
                        PusherNotification::create([
                            'push_notification_id' => $push_notification->id,
                            'device_token'         => $push_notification->device_token,
                            'title'                => $push_notification->title,
                            'body'                 => $push_notification->body,
                            'payload'              => $response,
                            'status'               => 0,
                        ]);
                    }
                }
                return true;
            }
            $server_key = 'AAAAPew_cdc:APA91bEJb7w_3-rOI5Pkr1wVVG9Qtl_WBQh_fEEk1N0yY-CHeUwOWKmSUODGhFbGuJv-BaqY-NS6KAYIo3Cw_UyKm2PvlM4reEae1SPj-y75z0Eu722IYUUqm_M2W9UOYnu40QyCIFGL';
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            if(in_array($push_notification->employee_type_id,[3,4])){
                $message = [
                    'notification' => $data,
                    'to' => $device_token
                ];
            }else{
                $message = [
                    'data' => $data,
                    'to' => $device_token
                ];
            }
            $client = new Client(['base_uri' => $fcmUrl, 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

            $response = $client->post('', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . $server_key,
                ],
                'body' => json_encode($message)
            ]);
            $response = json_decode($response->getBody()->getContents(), true);
            if(isset($response['success'])) {
                if ($response['success'] != 0) {
                    $push_notification->status = 1;
                    $push_notification->save();
                }
            }
        }
    }
}
