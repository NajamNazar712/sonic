<?php

namespace App\Jobs;

use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\EmployeeNotificationHistory;
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
//        dd($push_notification);
        $employee_device_token = EmployeeDeviceToken::where('employee_id', $push_notification->employee_id)
            ->where('employee_type_id', $push_notification->employee_type_id)
            ->select('device_token');
//        dd($push_notification->employee_type_id, $push_notification->employee_id);
        if ($employee_device_token->exists()) {
            $employee_device_token = $employee_device_token->first();
//            dd($employee_device_token);
            $device_token = $employee_device_token->device_token;
            $server_key = 'AAAAPew_cdc:APA91bEJb7w_3-rOI5Pkr1wVVG9Qtl_WBQh_fEEk1N0yY-CHeUwOWKmSUODGhFbGuJv-BaqY-NS6KAYIo3Cw_UyKm2PvlM4reEae1SPj-y75z0Eu722IYUUqm_M2W9UOYnu40QyCIFGL';
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            $data['title'] = $push_notification->title;
            $data['body'] = $push_notification->message;
            if($push_notification->screen_id != null){
                $data['screen_id'] = $push_notification->screen_id;
            }
            $message = [
                'data' => $data,
                'to' => $device_token
            ];

            $client = new Client(['base_uri' => $fcmUrl, 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

            $response = $client->post('', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . $server_key,
                ],
                'body' => json_encode($message)
            ]);
            $response = json_decode($response->getBody()->getContents(), true);
            dd($response);
            if ($response['success'] != 0) {
                $push_notification->status = 1;
                $push_notification->save();
            }
        }
    }
}
