<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\CrmAgentAutoAssign;
use App\Http\Models\Admin\CrmAgentAutoLog;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CrmAgent;
use App\Http\Models\CrmAgentLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        $settings = GlobalSettings::where('type', '=', 'crm_agent_auto_assigning');
        if($settings->exists()) {
            $settings = $settings->first();
            if ($settings->setting_value == 1) {
                $crm_requests = CrmRequest::leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
                    ->leftjoin('users as shipper', 'shipper.id', '=', 's.user_id')
                    ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
                    ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
                    ->leftjoin('sale_tier_tags as shipper_key', function ($join) {
                        $join->on('shipper_key.user_id', '=', 's.user_id')
                            ->WhereNotNull('shipper_key.kam');
                    })
                    ->leftjoin('sale_tier_tags as shipper_non_key', function ($join) {
                        $join->on('shipper_non_key.user_id', '=', 's.user_id')
                            ->WhereNull('shipper_non_key.kam');
                    })
                    ->select('crm_requests.id',
                        'crm_requests.case_nature_id',
                        'crm_requests.case_nature_type_id',
                        'crm_requests.agent_id',
                        'z.id as zone_id',
                        'dc.id as hub_id',
                        's.shipper_status_id as shipment_status_id',
                        'shipper.segment_id as business_segment_id',
                        'shipper.sub_segment_id as sub_segment_id',
                        'shipper_key.user_id as shipper_key_id',
                        'shipper_non_key.user_id as shipper_non_key_id'
                    )
                    ->whereNull('crm_requests.agent_id');

                if ($crm_requests->exists()) {
                    $crm_requests = $crm_requests->get();
                    foreach ($crm_requests as $value) {

                        $crm_agent =  CrmAgentAutoAssign::with('zones.zones','hubs.hubs','case_natures','case_nature_types','business_types','sub_business_types','shipper_keys','shipper_non_keys','shipment_statuses')
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
                                if (!empty($value->case_nature_id)) {
                                    $query->WhereHas('case_natures', function ($query) use ($value) {
                                        $query->where('case_nature_id', $value->case_nature_id);
                                    });
                                }
                            })
                            ->where(function ($query) use ($value) {
                                if (!empty($value->hub_id)) {
                                    $query->orWhereHas('hubs', function ($query) use ($value) {
                                        $query->where('hub_id', $value->hub_id);
                                    });
                                }
                                if (!empty($value->case_nature_type_id)) {
                                    $query->orWhereHas('case_nature_types', function ($query) use ($value) {
                                        $query->where('case_nature_type_id', $value->case_nature_type_id);
                                    });
                                }
                                if (!empty($value->business_segment_id)) {
                                    $query->orWhereHas('business_types', function ($query) use ($value) {
                                        $query->where('business_segment_id', $value->business_segment_id);
                                    });
                                }
                                if (!empty($value->sub_segment_id)) {
                                    $query->orWhereHas('sub_business_types', function ($query) use ($value) {
                                        $query->where('sub_segment_id', $value->sub_segment_id);
                                    });
                                }
                                if (!empty($value->shipper_key_id)) {
                                    $query->orWhereHas('shipper_keys', function ($query) use ($value) {
                                        $query->where('shipper_key_id', $value->shipper_key_id);
                                    });
                                }
                                if (!empty($value->shipper_non_key_id)) {
                                    $query->orWhereHas('shipper_non_keys', function ($query) use ($value) {
                                        $query->where('shipper_non_key_id', $value->shipper_non_key_id);
                                    });
                                }
                                if (!empty($value->shipment_status_id)) {
                                    $query->orWhereHas('shipment_statuses', function ($query) use ($value) {
                                        $query->where('shipment_status_id', $value->shipment_status_id);
                                    });
                                }
                            });


                        if ($crm_agent->exists()) {

                            $agents = $crm_agent->pluck('agent_id')->toArray();
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
                                'agent_id' => $agent_log->agent_id
                            ]);
                            $value->agent_id = $agent_log->agent_id;
                            $value->save();

                        }

                    }
                }
            }
        }

    }
}
