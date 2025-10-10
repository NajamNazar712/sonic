<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\CrmAgentAutoAssign;
use App\Http\Models\Admin\CrmAgentAutoLog;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CrmAgent;
use App\Http\Models\CrmAgentLog;
use App\Http\Models\SaleTierTag;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoAssignCrmAgentNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:autoassign_new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRM Auto Assign Agent';

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
        try{
            Log::channel('botCallJobLog')->info('status sharing crm-auto job initiated');
            $settings = GlobalSettings::where('type', '=', 'crm_agent_auto_assigning');
            if ($settings->exists()) {
                $settings = $settings->first();
                if ($settings->setting_value == 1) {
                    $yesterday = Carbon::now()->subDay(1)->format('Y-m-d 00:00:01');
                    $today = Carbon::now()->format('Y-m-d H:i:s');

                    $crm_requests = CrmRequest::leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
                        ->leftjoin('users as shipper', 'shipper.id', '=', 's.user_id')
                        ->leftjoin('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
                        ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
                        ->leftjoin('cities as h', 'h.id', '=', 'dc.hub_id')
                        ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
                        ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')
                        ->leftjoin('cities as oh', 'oh.id', '=', 'oc.hub_id')
                        ->leftjoin('zones as oz', 'oz.id', '=', 'oc.zone_id')
                        ->leftjoin('city_areas as ca', 'ca.city_id', '=', 'oc.id')
                        ->leftjoin('sale_tier_tags as shipper_key', function ($join) {
                            $join->on('shipper_key.user_id', '=', 's.user_id')
                                ->WhereNotNull('shipper_key.kam');
                        })
                        ->leftjoin('sale_tier_tags as shipper_non_key', function ($join) {
                            $join->on('shipper_non_key.user_id', '=', 's.user_id')
                                ->WhereNull('shipper_non_key.kam');
                        })
                        ->select(
                            'crm_requests.id',
                            'crm_requests.shipper_id',
                            'crm_requests.case_nature_id',
                            'crm_requests.case_nature_type_id',
                            'crm_requests.agent_id',
                            'z.id as zone_id',
                            'dc.id as city_id',
                            'h.id as hub_id',
                            'oz.id as origin_zone_id',
                            'oc.id as origin_id',
                            'oh.id as origin_hub_id',
                            'ca.id as origin_area_id',
                            's.shipper_status_id as shipment_status_id',
                            'shipper.segment_id as business_segment_id',
                            'shipper.sub_segment_id as sub_segment_id',
                            'shipper_key.user_id as shipper_key_id',
                            'shipper_non_key.user_id as shipper_non_key_id'
                        )
                        ->whereNull('crm_requests.agent_id')
                        ->whereBetween('crm_requests.created_at', [$yesterday, $today])
                        ->orderby('crm_requests.created_at', 'desc');

                    if ($crm_requests->exists()) {
                        $crm_requests = $crm_requests->get();

                        foreach ($crm_requests as $value) {

                            $crm_agent =  CrmAgentAutoAssign::with('agent_admin', 'origin_zones.zones', 'origin_hubs.hubs', 'origin_areas.city_area', 'zones.zones', 'hubs.hubs', 'case_natures', 'case_nature_types', 'business_types', 'sub_business_types', 'shipper_keys', 'shipper_non_keys', 'shipment_statuses')
                                ->select([
                                    'crm_agent_auto_assigns.id',
                                    'crm_agent_auto_assigns.agent_id',
                                    'crm_agent_auto_assigns.created_at',
                                    'crm_agent_auto_assigns.status',
                                ])->where('crm_agent_auto_assigns.status', 1)
                                ->where(function ($query) use ($value) {
                                    if (!empty($value->zone_id)) {
                                        $query->WhereHas('zones', function ($query) use ($value) {
                                            $query->where('zone_id', $value->zone_id);
                                        });
                                    }
                                    if (!empty($value->origin_zone_id)) {
                                        $query->WhereHas('origin_zones', function ($query) use ($value) {
                                            $query->where('origin_zone_id', $value->origin_zone_id);
                                        });
                                    }
                                    if (!empty($value->case_nature_id)) {
                                        $query->WhereHas('case_natures', function ($query) use ($value) {
                                            $query->where('case_nature_id', $value->case_nature_id);
                                        });
                                    }
                                });

                            if (SaleTierTag::where('user_id', $value->shipper_id)->exists()) {
                                $crm_agent = $crm_agent->whereHas('agent_admin', function ($query) {
                                    $query->whereIn('role_id', [43, 67, 75, 115]);
                                });
                            } else {
                                $crm_agent = $crm_agent->whereHas('agent_admin', function ($query) {
                                    $query->whereIn('role_id', [28, 37]);
                                });
                            }

                            if ($crm_agent->exists()) {
                                $crm_agent->get();

                                $modifies_data = $crm_agent->get()->filter(function ($val) use ($value) {
                                    $return  = 1;
                                    $hubs = isset($val->hubs) ?  $val->hubs->pluck('hub_id')->toArray() : [];
                                    $case_nature_type_id = isset($val->case_nature_types) ? $val->case_nature_types->pluck('case_nature_type_id')->toArray() : [];
                                    $business_types = isset($val->business_types) ? $val->business_types->pluck('business_segment_id')->toArray() : [];
                                    $sub_business_types = isset($val->sub_business_types) ? $val->sub_business_types->pluck('sub_segment_id')->toArray() : [];
                                    $shipper_keys = isset($val->shipper_keys) ? $val->shipper_keys->pluck('shipper_key_id')->toArray() : [];
                                    $shipper_non_keys = isset($val->shipper_non_keys) ? $val->shipper_non_keys->pluck('shipper_non_key_id')->toArray() : [];
                                    $shipment_statuses = isset($val->shipment_statuses) ? $val->shipment_statuses->pluck('shipment_status_id')->toArray() : [];
                                    $origin_hubs = isset($val->origin_hubs) ?  $val->origin_hubs->pluck('origin_hub_id')->toArray() : [];
                                    $origin_areas = isset($val->origin_areas) ?  $val->origin_areas->pluck('origin_area_id')->toArray() : [];
                                    //                               if(count($origin_hubs) > 0 || count($hubs) > 0) {
                                    //                                   $originHubMatch = true;
                                    //                                   $hubMatch = true;
                                    //
                                    //                                   if(!empty($value->origin_hub_id)) {
                                    //                                       if(!in_array($value->origin_hub_id,$origin_hubs)){
                                    //                                           $originHubMatch = false;
                                    //                                       }
                                    //                                   }
                                    //
                                    //                                   if(!empty($value->hub_id)){
                                    //                                       if(!in_array($value->hub_id,$hubs)){
                                    //                                           $hubMatch = false;
                                    //                                       }
                                    //                                   }
                                    //                                   $return = ($originHubMatch || $hubMatch) ? 1 : 0;
                                    //                               }
                                    if (count($origin_hubs) > 0) {
                                        if (!empty($value->origin_hub_id)) {
                                            if (!in_array($value->origin_hub_id, $origin_hubs)) {
                                                $return = 0;
                                            }
                                        }
                                    }
                                    //DESTINATION HUB
                                    if (count($hubs) > 0) {
                                        if (!empty($value->hub_id)) {
                                            if (!in_array($value->hub_id, $hubs)) {
                                                $return = 0;
                                            }
                                        }
                                    }

                                    if (count($case_nature_type_id) > 0) {
                                        if (!empty($value->case_nature_type_id)) {
                                            if (!in_array($value->case_nature_type_id, $case_nature_type_id)) {
                                                $return = 0;
                                            }
                                        }
                                    }
                                    if (count($business_types) > 0) {
                                        if (!empty($value->business_segment_id)) {
                                            if (!in_array($value->business_segment_id, $business_types)) {
                                                $return = 0;
                                            }
                                        }
                                    }
                                    if (count($sub_business_types) > 0) {
                                        if (!empty($value->sub_segment_id)) {
                                            if (!in_array($value->sub_segment_id, $sub_business_types)) {
                                                $return = 0;
                                            }
                                        }
                                    }
                                    if (count($shipper_keys) > 0) {
                                        if (!empty($value->shipper_key_id)) {
                                            if (!in_array($value->shipper_key_id, $shipper_keys)) {
                                                $return = 0;
                                            }
                                        } elseif (!in_array($value->shipper_id, $shipper_keys)) {
                                            $return = 0;
                                        }
                                    }

                                    if (count($shipper_non_keys) > 0) {
                                        if (!empty($value->shipper_non_key_id)) {
                                            if (!in_array($value->shipper_non_key_id, $shipper_non_keys)) {
                                                $return = 0;
                                            }
                                        } elseif (!in_array($value->shipper_id, $shipper_non_keys)) {
                                            $return = 0;
                                        }
                                    }
                                    if (count($shipment_statuses) > 0) {
                                        if (!empty($value->shipment_status_id)) {
                                            if (!in_array($value->shipment_status_id, $shipment_statuses)) {
                                                $return = 0;
                                            }
                                        }
                                    }
                                    if (count($origin_areas) > 0) {
                                        if (!empty($value->origin_area_id)) {
                                            if (!in_array($value->origin_area_id, $origin_areas)) {
                                                $return = 0;
                                            }
                                        }
                                    }

                                    if ($return == 1) {
                                        return  $val;
                                    }
                                });
                                $agents = $modifies_data->pluck('agent_id')->toArray();
                                if (count($agents) > 0) {
                                    $agent_log = CrmAgentAutoLog::whereIn('agent_id', $agents)->orderBy('assinged_requests', 'asc');
                                    $current_agent = $agent_log->pluck('agent_id')->toArray();

                                    $difference = collect(array_diff($agents, $current_agent));
                                    if (count($difference) == 0) {
                                        if ($agent_log->exists()) {
                                            $agent_log = $agent_log->first();
                                            $agent_log->assinged_requests = $agent_log->assinged_requests + 1;
                                            $agent_log->assigned_date = Carbon::today()->toDateString();
                                            $agent_log->save();
                                        } else {
                                            $agent_log = new CrmAgentAutoLog();
                                            $agent_log->agent_id = $agents[0];
                                            $agent_log->assinged_requests = 1;
                                            $agent_log->assigned_date = Carbon::today()->toDateString();
                                            $agent_log->save();
                                        }
                                    } else {
                                        $agent_log = new CrmAgentAutoLog();
                                        $agent_log->agent_id = $difference->first();
                                        $agent_log->assinged_requests = 1;
                                        $agent_log->assigned_date = Carbon::today()->toDateString();
                                        $agent_log->save();
                                    }

                                    CrmRequestAgentHistory::create([
                                        'crm_request_id' => $value->id,
                                        'agent_id' => $agent_log->agent_id,
                                        'assigned_by' => 346
                                    ]);
                                    $value->agent_id = $agent_log->agent_id;
                                    $value->save();
                                }
                            }
                        }
                    }
                }
            }
            Log::channel('botCallJobLog')->info('status new-crm job dispatched');
        }catch(\Throwable $e){
            Log::channel('botCallJobLog')->error('Status new-crm job failed: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
