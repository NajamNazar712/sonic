<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class WebsiteLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'website:leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Leads from Website';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $environment = env('APP_ENV');
        if($environment == 'production'){
            $base_uri = 'https://trax.pk/api';
        }
//        elseif ($environment == 'staging'){
//            $base_uri = 'https://trax.pk/api';
//        }
        else{
            $base_uri = 'website.test/api/';
        }
        $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
        $response = $client->post('leads', [
            'form_params' => [
                "token" => 'TraxOnlinePvtLtdAYWD',
            ]
        ]);
        $response = $response->getBody()->getContents();
        $response = json_decode($response);

        if($response->status == 0){
            $leads = $response->leads;
            foreach ($leads as $lead) {
                dd($lead);
            }
        }
    }
}
