<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Models\SMS;

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
                $client = new Client(['base_uri' => 'http://sms.its.com.pk/api/', 'http_errors' => FALSE, 'connect_timeout' => 15, 'timeout' => 30]);

                $response = $client->get('', [
                    'query' => [
                        'username' => 'trax',
                        'password' => '123456',
                        'receiver' => $this->sms->to,
                        'msgdata' => $this->sms->body
                    ]
                ]);

                $response = simplexml_load_string($response->getBody());
                $response = (array)$response;

                if ($response['errorno'] == 0) {
                    $this->sms->status = 3;

                    $this->sms->save();
                }
                else {
                    $this->sms->status = 1;

                    $this->sms->save();
                }
            } catch (RequestException $e) {
                $this->sms->status = 1;

                $this->sms->save();
            }
        }
    }
}
