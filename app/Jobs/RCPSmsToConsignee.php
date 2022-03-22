<?php

namespace App\Jobs;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipment;
use App\Http\Models\SMS;
use App\Mail\Notifications;
use App\ReturnConfirmationPendingSmsAttempt;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class RCPSmsToConsignee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $shipment_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shipment_id)
    {
        $this->queue = 'rcp_sms_to_consignee';
        $this->shipment_id = $shipment_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = NotificationsController::send(169, $this->shipment_id);

        $sms = new SMS();

        $sms->to = str_replace('-', '', $data[1]);
        $sms->body = $data[0];

        $sms->save();
        
       self::its($sms, $this->shipment_id);

    }

    private function its($sms, $shipment_id) {
        try {
            $limit = GlobalSettings::where('type','return_confirmation_pending_sms')->first();

            if ($limit) {
                $attempt = ReturnConfirmationPendingSmsAttempt::where('shipment_id', $shipment_id)->where('status', 0);
                $current_count = 0;
                $status = true;

                if ($attempt->exists()) {
                    $attempt = $attempt->latest('id')->first();
                    $current_count = $attempt->count;
                }
                else {
                    $attempt = FALSE;
                }

                if ($current_count < $limit->text) {
                    $client = new Client(['base_uri' => 'https://gateway.its.com.pk/api', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

                    $response = $client->get('', [
                        'query' => [
                            'action' => 'sendmessage',
                            'username' => 'Trax',
                            'password' => 'Tr@x!101',
                            'originator' => 87323,
                            'recipient' => $sms->to,
                            'messagedata' => $sms->body
                        ]
                    ]);

                    $response = simplexml_load_string($response->getBody());
                    $response = json_decode(json_encode($response), true);

                    if (isset($response['data']['acceptreport']) && $response['data']['acceptreport']['statuscode'] == 0) {
                        $sms->status = 3;

                        $sms->save();

                        if (!$attempt) {
                            ReturnConfirmationPendingSmsAttempt::create(['shipment_id' => $shipment_id, 'status' => 0, 'count' => 1]);
                        }
                        else {
                            $attempt->count = $attempt->count + 1;
                            $attempt->update();
                        }
                    }
                    else {
                        $sms->status = 2;

                        $sms->save();

                        if ($attempt) {
                            $attempt->status = 4;
                            $attempt->update();
                        }

                        $to = ['muhammad.yousuf@trax.pk'];
                        $subject = '[Error] SMS API - ITS';
                        $body = 'Unrecognized Error in SMS API.<br/>SMS ID: ' . $sms->id . '<br/>Response Received: ' . json_encode($response);

                        $mail = Mail::to($to)->send(new Notifications($subject, $body));
                    }
                }
                else {
                    if ($attempt) {
                        $attempt->status = 3;
                        $attempt->update();
                    }
                }
            }
        } catch (RequestException $e) {
            $sms->status = 1;

            $sms->save();
        }
    }
}
