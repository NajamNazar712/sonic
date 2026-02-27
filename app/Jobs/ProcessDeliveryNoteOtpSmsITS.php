<?php

namespace App\Jobs;

use App\Http\Models\DeliveryNoteOtpSms;
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

class ProcessDeliveryNoteOtpSmsITS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $sms, $name, $otp;

    public function __construct(DeliveryNoteOtpSms $sms, string $name, int $otp)
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
            $to = ['munawar.shamsi@logiserves.com'];
            $subject = '[Error] SMS API';
            $body = 'Error Exception.<br/>' . json_encode($exception->getMessage());

            $mail = Mail::to($to)->send(new Notifications($subject, $body));
        }
    }

    private function its($sms, $name, $otp) {
        try {
            $client = new Client(['base_uri' => 'https://gateway.its.com.pk/api/otp', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

            $response = $client->get('', [
                'query' => [
                    'action' => 'sendmessage',
                    'username' => 'Trax',
                    'password' => 'Tr@x!101',
                    'originator' => 87323,
                    'recipient' => $sms->to,
                    'otherurl' => $name,
                    'otpcode' => $otp,
                    'otptype' => 2
                ]
            ]);

            $response = simplexml_load_string($response->getBody());
            $response = json_decode(json_encode($response), true);

            if (isset($response['data']['acceptreport']) && $response['data']['acceptreport']['statuscode'] == 0) {
                $this->sms->status = 3;

                $this->sms->save();
            }
            else {
                $this->sms->status = 2;

                $this->sms->save();

                $to = ['munawar.shamsi@logiserves.com', 'munawar.shamsi@logiserves.com'];
                $subject = '[Error] SMS API - ITS';
                $body = 'Unrecognized Error in SMS API.<br/>SMS ID: ' . $sms->id . '<br/>Response Received: ' . json_encode($response);

                $mail = Mail::to($to)->send(new Notifications($subject, $body));
            }
        } catch (RequestException $e) {
            $this->sms->status = 1;

            $this->sms->save();
        }
    }
}
