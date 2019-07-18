<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

use App\Http\Models\SMS;

use App\Mail\Notifications;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class ProcessSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sms;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SMS $sms)
    {
        $this->queue = 'sms';
        $this->sms = $sms;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->sms->status < 2) {
            try {
                $client = new Client(['base_uri' => 'https://bsms.telecard.com.pk/SMSPortal/Customer/ProcessSMS.aspx', 'http_errors' => FALSE, 'connect_timeout' => 15, 'timeout' => 30]);

                $response = $client->get('', [
                    'query' => [
                        'userid' => 'trax',
                        'pwd' => 'trax123',
                        'mobileno' => $this->sms->to,
                        'msg' => $this->sms->body
                    ]
                ]);

                $response = $response->getBody()->getContents();

                if (substr($response, 0, 2) == 'OK') {
                    $this->sms->status = 3;

                    $this->sms->save();
                }
                else if ($response == 'Invalid Mobile Number Entered.') {
                    $this->sms->status = 2;

                    $this->sms->save();
                }
                else {
                    $this->sms->status = 2;

                    $this->sms->save();

                    $to = 'yousuf.fazal@trax.pk';
                    $subject = '[Error] SMS API';
                    $body = 'Unrecognized Error in SMS API.<br/>SMS ID: ' . $this->sms->id . '<br/>Response Received: ' . json_encode($response);

                    $mail = Mail::to($to)->send(new Notifications($subject, $body));
                }
            } catch (RequestException $e) {
                $this->sms->status = 1;

                $this->sms->save();
            }
        }
    }
}
