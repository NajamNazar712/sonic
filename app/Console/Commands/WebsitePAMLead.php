<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\Lead\PamLead;
use App\Http\Models\Admin\Lead\PamLeadItem;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class WebsitePAMLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'website:pamleads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Packing & Movers Leads from Website';

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
        else{
            $base_uri = 'website.test/api/';
        }
        $client = new Client(['base_uri' => $base_uri, 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60]);
        $response = $client->post('pam_leads', [
            'form_params' => [
                "token" => 'TraxOnlinePvtLtdAYWD',
            ]
        ]);
        $response = $response->getBody()->getContents();
        $response = json_decode($response);
        return $response;
        if($response->status == 0){
            $leads = $response->leads;
            foreach ($leads as $lead) {
                $new_lead = new PamLead();
                $new_lead->name = $lead->name;
                $new_lead->phone = $lead->phone;
                $new_lead->video_link = $lead->video_link;
                $new_lead->images = $lead->images;
                $new_lead->origin_id = $lead->origin_id;
                $new_lead->destination_id = $lead->destination_id;
                $new_lead->location_type = $lead->location_type;
                $new_lead->case_type = $lead->case_type;
                $new_lead->save();

                foreach ($lead['item_details'] as $item) {
                    $lead_log = new PamLeadItem();
                    $lead_log->lead_id = $new_lead->id;
                    $lead_log->item = $item->item;
                    $lead_log->quantity = $item->quantity;
                    $lead_log->length = $item->length;
                    $lead_log->width = $item->width;
                    $lead_log->height = $item->height;
                    $lead_log->weight = $item->weight;
                    $lead_log->save();
                }
            }
        }

        echo $response->message;
    }
}
