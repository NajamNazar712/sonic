<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use App\Http\Models\SMS;
use App\Mail\Notifications;

class GenerateDeliveryOTP implements ShouldQueue
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
        $this->queue = 'generate_delivery_otp';
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
        $client = new Client(['base_uri' => 'https://voicegateway.its.com.pk/api', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

        $response = $client->get('', [
            'query' => [
                'Apikey' => '3FAE1AA22D777FDD699758014500ECF6',
                'Recipient' => $this->sms->to,
                'CampId' => '315',
                'Param1' => $this->otp,
                'UniqueId' => $this->sms->id,
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
        $body = 'Unrecognized Error in CALL API.<br/>SMS ID: ' . $this->sms->id . '<br/>Response Received: ' . json_encode($response);

        $mail = Mail::to($to)->send(new Notifications($subject, $body));
    }
}
}