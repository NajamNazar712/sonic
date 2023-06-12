<?php

namespace App\Jobs;

use App\Http\Models\SMS;
use App\Http\Models\Telenor;
use App\Mail\Notifications;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ProcessOTPSMSITS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sms, $name, $otp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SMS $sms, string $name, int $otp)
    {
        $this->queue = 'sms_otp';
        $this->sms = $sms;
        $this->name = $name;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->sms->status < 2) {
                $this->its($this->sms, $this->name, $this->otp);
            }
        }
        catch(Exception $exception) {
            $to = ['muhammad.yousuf@trax.pk'];
            $subject = '[Error] SMS API';
            $body = 'Error Exception.<br/>' . json_encode($exception->getMessage());

            $mail = Mail::to($to)->send(new Notifications($subject, $body));
        }
    }

    private function its($sms, $name, $otp) {
//        try {
//            $client = new Client(['base_uri' => 'https://gateway.its.com.pk/api/otp', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);
//
//            $response = $client->get('', [
//                'query' => [
//                    'action' => 'sendmessage',
//                    'username' => 'Trax',
//                    'password' => 'Tr@x!101',
//                    'originator' => 87323,
//                    'recipient' => $sms->to,
//                    'otherurl' => $name,
//                    'otpcode' => $otp,
//                    'otptype' => 1
//                ]
//            ]);
//
//            $response = simplexml_load_string($response->getBody());
//            $response = json_decode(json_encode($response), true);
//
//            if (isset($response['data']['acceptreport']) && $response['data']['acceptreport']['statuscode'] == 0) {
//                $this->sms->status = 3;
//
//                $this->sms->save();
//            }
//            else {
//                $this->sms->status = 2;
//
//                $this->sms->save();
//
//                $to = ['muhammad.yousuf@trax.pk', 'noman.aziz@trax.pk'];
//                $subject = '[Error] SMS API - ITS';
//                $body = 'Unrecognized Error in SMS API.<br/>SMS ID: ' . $sms->id . '<br/>Response Received: ' . json_encode($response);
//
//                $mail = Mail::to($to)->send(new Notifications($subject, $body));
//            }
//        } catch (RequestException $e) {
//            $this->sms->status = 1;
//
//            $this->sms->save();
//        }
        try {
            $client = new Client(['base_uri' => 'https://voicegateway.its.com.pk/api', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

            $response = $client->get('', [
                'query' => [
                    'Apikey' => '3FAE1AA22D777FDD699758014500ECF6',
                    'CampId' => '220',
                    'Recipient' => $sms->to,
                    'Param1' => $otp,
                    'UniqueId' => $sms->id,
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $this->sms->status = 3;

                $this->sms->save();
            }
            else {
                $response = json_decode($response->getBody()->getContents(), true);

                $this->sms->status = 2;

                $this->sms->save();

                $to = ['muhammad.yousuf@trax.pk'];
                $subject = '[Error] CALL API - ITS';
                $body = 'Unrecognized Error in CALL API.<br/>SMS ID: ' . $sms->id . '<br/>Response Received: ' . json_encode($response);

                $mail = Mail::to($to)->send(new Notifications($subject, $body));
            }
        } catch (RequestException $e) {
            $this->sms->status = 1;

            $this->sms->save();
        }
    }
}
