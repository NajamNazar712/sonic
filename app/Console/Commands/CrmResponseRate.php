<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Http\Models\Rider;
use Illuminate\Console\Command;
use App\Http\Models\Admin\Admin;
use App\Http\Models\CRM\CrmRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Models\ShipmentsJourney;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;

class CrmResponseRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:response_rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily CRM Request Response Email';

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
        $requests = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
                                ->leftjoin('shipments as ships', 'crm_requests.shipment_id', '=', 'ships.id')
                                ->leftjoin('shipment_status as ss', 'ss.id', '=', 'ships.shipper_status_id')
                                ->leftjoin('user_shipping_infos as usi', 'ships.pickup_address_id', '=', 'usi.id')
                                ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')
                                ->leftjoin('cities as och', 'och.id', '=', 'oc.hub_id')
                                ->leftjoin('zones as ocz', 'ocz.id', '=', 'oc.zone_id')
                                ->leftjoin('cities as dc', 'dc.id', '=', 'ships.consignee_city_id')
                                ->leftjoin('cities as dh', 'dh.id', '=', 'dc.hub_id')
                                ->leftjoin('crm_comments', function($join){
                                    $join->on('crm_requests.id', '=', 'crm_comments.crm_request_id')
                                    ->where('crm_comments.comment_type', '!=', 0);
                                })
                                ->where('crm_requests.created_at', ">=", Carbon::now()->subHours(24))
                                ->select('crm_comments.comment_by','crm_comments.comment_by_id', 'crcn.name as case_nature','crm_requests.id as req_id','crm_comments.id as comm_id', 'crm_requests.shipment_id as ship_id','ss.id as ship_status_id','och.name as origin_hub', 'dh.name as hub')
                                ->get(); 
        
        // dd($requests);

        $responses = [];

        foreach($requests as $req) {
            $responsible_hub = "";
            $status = $req->ship_status_id;
            $shipment_id = $req->ship_id;
            $origin_hub = $req->origin_hub;
            $destination_hub = $req->hub;
            $status_destination_array = [4, 12, 20, 24, 11, 8, 14, 30, 7, 13, 52, 5, 15,9];
            $status_origin_array = [1,2,17,22,27,23,31,29,26,25,60,62,53,47,28];
            $shipment_journey = ShipmentsJourney::whereIn('shipper_status_id', [11,12,20,21,22])
                ->where('shipment_id',$shipment_id);
            if($shipment_journey->exists()){
                $shipment_journey = $shipment_journey->pluck('shipper_status_id')->toArray();
                if(in_array(11,$shipment_journey) &&  in_array(12,$shipment_journey) ) {
                    $temp = $destination_hub;
                    $destination_hub = $origin_hub;
                    $origin_hub = $temp;
                }
            }
            if(in_array($status,$status_origin_array)){
                $responsible_hub = $origin_hub;
            }
            elseif($status == 3 || $status == 21){
                $manifest_bag = CargoManifestBagShipments::
                leftjoin('cargo_manifest_bags as cmb','cmb.id','=','cargo_manifest_bag_shipments.cargo_manifest_bag_id')
                    ->leftjoin('cities as c','c.id','=','cmb.origin_hub_id')
                    ->leftjoin('cities as cd','cd.id','=','cmb.destination_hub_id')
                    ->leftjoin('cities as chi','chi.id','=','cmb.current_hub_id')
                    ->where('cargo_manifest_bag_shipments.shipment_id',$shipment_id)
                    ->select(['cargo_manifest_bag_shipments.id','cmb.status_id','c.name as origin_hub','cd.name as destination_hub','chi.name as curren_hub_origin'])
                    ->orderby('cargo_manifest_bag_shipments.id','desc');
                if($manifest_bag->exists()){
                    $manifest_bag = $manifest_bag->first();
                    if ($manifest_bag->status_id == 0) {  //bag created
                        $responsible_hub = $manifest_bag->origin_hub;
                    }
                    elseif ($manifest_bag->status_id == 1) {  //bag created
                        $responsible_hub = $manifest_bag->origin_hub;
                    }elseif ($manifest_bag->status_id == 3) { // bag Received at junction
                        $responsible_hub = $manifest_bag->curren_hub_origin;
                    }
                    elseif ($manifest_bag->status_id == 2 || $manifest_bag->status_id == 4 || $manifest_bag->status_id == 5 || $manifest_bag->status_id == 7) {
                        $responsible_hub = $manifest_bag->destination_hub;
                    } elseif ($manifest_bag->status_id == 9) {
                        $responsible_hub = $manifest_bag->curren_hub_origin;
                    }
                }
            }
            elseif(in_array($status, $status_destination_array) && $req->case_nature != 'Service Request'){
                $responsible_hub = $destination_hub;
            }
            else{
                $responsible_hub = "-";
            }

            if ($responsible_hub=='-') {
                continue;
            } 

            $hubIndex = array_search($responsible_hub, array_column($responses, 'responsible_hub'));

            $role_id = Admin::where('id',$req["comment_by_id"])->value('role_id');
            $admin_role_id = AdminRole::where('id', $role_id)->value('department_id');
            $dept = AdminDepartment::where('id', $admin_role_id)->value('name');
            $deptIsOperation = $dept == 'Operations';
            
            if ($hubIndex !== false) {

                $responses[$hubIndex]['total_tagged']++;

                if ($deptIsOperation) {
                    $responses[$hubIndex]['num_of_resps']++;
                }
                
                $responses[$hubIndex]['response_rate'] = number_format(($responses[$hubIndex]['num_of_resps']/$responses[$hubIndex]['total_tagged']) * 100);
            }
            else
            {
                $responses[] = [
                    'responsible_hub' => $responsible_hub,
                    'total_tagged' => 1,
                    'response_rate' => ($req['comm_id'] && $deptIsOperation) ? "100" : "0",
                    'num_of_resps' => ($req['comm_id'] && $deptIsOperation) ? 1 : 0,
                ];
            }        
        }

        dd($responses);

        if(count($responses) > 0){
            NotificationsController::send(221, array_slice($responses, 0, 3));
        }else{
            return;
        }
    }
}
