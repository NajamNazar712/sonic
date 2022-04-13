<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\LeadTaggingController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadLog;
use App\Http\Models\Admin\LeadReference;
use App\Http\Models\City;
use App\Http\Models\ServiceList;
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
            $base_uri = 'https://trax.pk/wp-json/tl/v1/';
        }
//        elseif ($environment == 'staging'){
//            $base_uri = 'https://trax.pk/api/';
//        }
        else{

            $base_uri = 'http://trax_website.test/wp-json/tl/v1/';
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
        $leads_added = array();

        if($response->status == 0){
            $leads = $response->leads;
            foreach ($leads as $lead) {

                if($lead->data){

                    $city = City::where('name', $lead->data->city_name[0])->first();
                    if($city){
                        $city_id = $city->id;
                    }
                    else{
                        continue;
                    }

                    $services = ServiceList::where('name', $lead->data->service_name[0])->first();
                    if($services){
                        $service_id = $services->id;
                    }
                    else{
                        continue;
                    }

                    $reference = LeadReference::where('name', $lead->data->reference_name[0])->first();
                    if($reference){
                        $reference_id = $reference->id;
                    }
                    else{
                        continue;
                    }

                    $new_lead = new Lead();
                    $new_lead->contact_person = $lead->data->full_name;
                    $new_lead->city_id = $city_id;
                    $new_lead->phone_number = $lead->data->phone_number;
                    $new_lead->email_address = $lead->data->email;
                    $new_lead->requested_date = Carbon::now();
                    $new_lead->service_id = $service_id;
                    $new_lead->reference_id = $reference_id;
                    $new_lead->save();

                    $lead_log = new LeadLog();
                    $lead_log->lead_id = $lead->id;
                    $lead_log->prev_status_id = 1;
                    $lead_log->status_id = 1;
                    $lead_log->updated_by = 7;
                    $lead_log->save();
                    $leads_added[] = $lead->id;
                }

            }
        }

        if(count($leads_added) > 0){
            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            $client->delete('leads', [
                'form_params' => [
                    "token" => 'TraxOnlinePvtLtdAYWD',
                    "ids" => $leads_added
                ]
            ]);
        }

        if(count($new_leads) > 0){
            NotificationsController::send(203, $new_leads, Carbon::today());
        }

    }
}
