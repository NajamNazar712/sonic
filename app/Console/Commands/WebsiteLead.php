<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadLog;
use Carbon\Carbon;
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
        $environment = config('app.env');
        if($environment == 'production'){
            $base_uri = 'https://trax.pk/api/';
        }
//        elseif ($environment == 'staging'){
//            $base_uri = 'https://trax.pk/api/';
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
        $new_leads = array();
        if($response->status == 0){
            $leads = $response->leads;
            foreach ($leads as $lead) {
                $max_lead_id = Lead::max('lead_id');
                $max_lead_id = $max_lead_id + 1;
                $new_lead = new Lead();
                $new_lead->lead_id = $max_lead_id;
                $new_lead->contact_person = $lead->full_name;
                $new_lead->city_id = $lead->city_id;
                $new_lead->territory_id = $lead->territory_id;
                $new_lead->territory_area_id = $lead->territory_area_id;
                $new_lead->phone_number = $lead->phone_number;
                $new_lead->email_address = $lead->email;
                $new_lead->requested_date = $lead->created_at;
                $new_lead->message = $lead->message;
                $new_lead->reference_id = $lead->reference_id;
                $new_lead->brand = $lead->brand;
                $new_lead->service_id = $lead->service_id;
                $new_lead->company = $lead->company;
                $new_lead->save();

                $lead_log = new LeadLog();
                $lead_log->lead_id = $lead->id;
                $lead_log->prev_status_id = 1;
                $lead_log->status_id = 1;
                $lead_log->updated_by = 7;
                $lead_log->save();

                $new_leads[] = $new_lead->id;
            }
        }

        if(count($new_leads) > 0){
            NotificationsController::send(203, $new_leads, Carbon::today());
        }

        echo $response->message;
    }
}
