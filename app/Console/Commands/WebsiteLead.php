<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Http\Models\City;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Http\Models\ServiceList;
use Illuminate\Support\Facades\Log;
use App\Http\Models\Admin\Lead\Lead;
use App\Http\Models\Admin\Lead\LeadLog;
use App\Http\Models\Admin\LeadReference;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\LeadTaggingController;
use App\Http\Models\Admin\Lead\LeadZone;
use App\Http\Models\Admin\SalePersonTag;

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
        elseif ($environment == 'staging'){
            $base_uri = 'https://trax.pk/api/';
        }
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
        $leads_added = array();
        $old_leads = array();
        $token_added = array();

        if($response->status == 0){
            $leads = $response->leads;
            foreach ($leads as $key => $lead) {

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

                    $token = Str::random(8);

                    $new_lead = new Lead();
                    $new_lead->contact_person = $lead->data->contact_person;
                    $new_lead->city_id = $city_id;
                    $new_lead->phone_number = $lead->data->phone_number;
                    $new_lead->email_address = $lead->data->email;
                    $new_lead->requested_date = Carbon::now();
                    $new_lead->service_id = $service_id;
                    $new_lead->ntn_number = $lead->data->ntn_number;
                    $new_lead->average_shipment_per_week = $lead->data->avg_shipment;
                    $new_lead->average_parcel_cod_amount = $lead->data->avg_parcel;
                    $new_lead->business_address = $lead->data->business_address;
                    $new_lead->company_name = $lead->data->company_name;
                    $new_lead->business_registered_status = isset($lead->data->business_address) ? 1 : 0;
                    $new_lead->reference_id = $reference_id;
                    $new_lead->activation_code = $token;
                    $new_lead->cnic_number = $lead->data->cnic_number;
                    $new_lead->save();

                    $lead_log = new LeadLog();
                    $lead_log->lead_id = $lead->id;
                    $lead_log->prev_status_id = 1;
                    $lead_log->status_id = 1;
                    $lead_log->updated_by = 7;
                    $lead_log->save();
                    $leads_added[] = $new_lead->id;
                    $token_added[$key] = $token;
                    $old_leads[] = $lead->id;


                    
                }
            }
        }

        if(count($old_leads) > 0){
            $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
            $client->delete('leads', [
                'form_params' => [
                    "token" => 'TraxOnlinePvtLtdAYWD',
                    "ids" => $old_leads
                    ]
                ]);
            }
            
            if(count($old_leads) > 0){
                NotificationsController::send(203, $old_leads, Carbon::today());
                NotificationsController::send(230, $leads_added, $token_added);    
            }



        Log::channel('cronJobLog')->info('s ' .'website:leads Running');

    }
}
