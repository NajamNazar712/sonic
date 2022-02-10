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

       $data = NotificationsController::send(169,$this->shipment_id);
       self::telecard($data,$this->shipment_id);

    }
    private function telecard($sms,$shipment_id) {
        //$to = ['nabeel.siddiqui@trax.pk'];
        try {
            $limit = GlobalSettings::where('type','return_confirmation_pending_sms')->first();
            if($limit) {
                $attempt = ReturnConfirmationPendingSmsAttempt::where('shipment_id', $shipment_id);
                $current_count = 0;
                $status = true;
                if ($attempt->exists()) {
                    $attempt = $attempt->first();
                    $current_count = $attempt->count;
                    $status = $attempt->status == 0;
                }
                if ($current_count < $limit->text && $status) {

                    $client = new Client(['base_uri' => 'https://bsms.telecard.com.pk/SMSPortal/Customer/ProcessSMS.aspx', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

                    $response = $client->get('', [
                        'query' => [
                            'userid' => 'TraxPL',
                            'pwd' => 'TRAX@2022',
                            'mobileno' => $sms[1],
                            'msg' => $sms[0]
                        ]
                    ]);

                    $response = $response->getBody()->getContents();

                    if (substr($response, 0, 2) == 'OK') {

                        SMS::create(['to' => $sms[1],'body'=>$sms[0],'status' => 3]);
                        if($current_count == 0){
                            ReturnConfirmationPendingSmsAttempt::create(['shipment_id' => $shipment_id, 'status' => 1, 'count' => 1]);
                        }
                        else{
                            $attempt->count = $attempt->count + 1;
                            $attempt->update();
                        }
                        
                    } else {
                        $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
                        SMS::create(['to' => $sms[1],'body'=>$sms[0],'status' => 2]);
                        $subject = '[Error] RCP SMS API';
                        $body = 'Unrecognized Error in RCP SMS API.<br/>Response Received: ' . json_encode($response);

                        $mail = Mail::to($to)->send(new Notifications($subject, $body));
                    }
                }
            }
        } catch (RequestException $e) {
            $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
            SMS::create(['to' => $sms[1],'body'=>$sms[0],'status' => 1]);
            $subject = '[Error] RCP SMS API';
            $body = 'Unrecognized Error in RCP SMS API.<br/>Response Received: ' . $e->getMessage();

            $mail = Mail::to($to)->send(new Notifications($subject, $body));
        }
    }
}
