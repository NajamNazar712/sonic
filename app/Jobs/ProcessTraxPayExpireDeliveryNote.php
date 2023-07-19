<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessTraxPayExpireDeliveryNote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $unique_codes;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $unique_codes)
    {
        $this->queue = 'trax_pay_expire_delivery_note';
        $this->unique_codes = $unique_codes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $unique_codes = $this->unique_codes;

        if ($unique_codes != null) {
            try{
                $environment = config('app.env');
                if($environment == 'production'){
                    $url          = "https://pay.trax.pk/api/";
                }
                else if($environment == 'staging'){
                    $url          = "https://pay-staging.trax.pk/api/";
                }
                else{
                    $url          = "https://trax-payment-portal.test/api/";
                }
                $client = new Client(['base_uri' => $url, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
                $response = $client->delete('online-transaction-expire', [
                    'form_params' => [
                        'unique_codes' => $unique_codes
                    ]
                ]);
            }
            catch(RequestException $e) {
            }
        }
    }
}
