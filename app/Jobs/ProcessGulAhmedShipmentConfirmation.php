<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

use App\Mail\Notifications;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class ProcessGulAhmedShipmentConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $confirmation_shipments;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $confirmation_shipments)
    {
        $this->queue = 'gul_ahmed_shipment_confirmation';
        $this->confirmation_shipments = $confirmation_shipments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $client = new Client(['base_uri' => 'http://202.61.49.91:5524', 'http_errors' => FALSE, 'connect_timeout' => 120, 'timeout' => 120]);

            $response = $client->get('logistics/v1/token', [
                'form_params' => [
                    'username' => 'EC_user_trx',
                    'password' => ']w2nUC=7*)NB+^\?',
                    'grant_type' => 'password'
                ]
            ]);

            $response = json_decode($response->getBody()->getContents());

            $access_token = $response->access_token;

            try {
                $response = $client->post('api/logistics/Confirmation', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token
                    ],
                    'json' => $this->confirmation_shipments
                ]);

                if ($response->getStatusCode() != 200) {
                    $subject = '[Error] Gul Ahmed Confirmation API';
                    $body = 'Response Received: ' . json_encode($response->getBody()->getContents());

                    $this->error($subject, $body);
                }
            } catch (RequestException $e) {
                $subject = '[Error] Gul Ahmed Confirmation API';

                if ($e->hasResponse()) {
                    $body = 'Response Received: ' . json_encode($e->getResponse());
                }
                else {
                    $body = 'No Response Received';
                }

                $this->error($subject, $body);
            }
        } catch (RequestException $e) {
            $subject = '[Error] Gul Ahmed Token API';

            if ($e->hasResponse()) {
                $body = 'Response Received: ' . json_encode($e->getResponse());
            }
            else {
                $body = 'No Response Received';
            }

            $this->error($subject, $body);
        }
    }

    private function error($subject, $body) {
        $to = 'munawar.shamsi@logiserves.com';
        $body .= 'Shipment Tracking Number(s): ' . json_encode($this->confirmation_shipments);

        $mail = Mail::to($to)->send(new Notifications($subject, $body));
    }
}
