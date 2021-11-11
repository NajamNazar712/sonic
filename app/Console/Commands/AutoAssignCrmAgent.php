<?php

namespace App\Console\Commands;

use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CrmAgent;
use App\Http\Models\CrmAgentLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoAssignCrmAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:autoassign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Assign CRM Agent';

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
        $crm_agents = CrmAgent::where('status',1)->get();
        foreach ($crm_agents as $crm_agent) {
            $crm_agent_log = new CrmAgentLog();
            $crm_agent_log->admin_id = $crm_agent->admin_id;
            $crm_agent_log->case_nature_id = $crm_agent->case_nature_id;
            $crm_agent_log->zone_id = $crm_agent->zone_id;
            $crm_agent_log->assigned_date = Carbon::today();
            $crm_agent_log->assinged_requests = 0;
            $crm_agent_log->save();
        }
        $crm_requests = CrmRequest::leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
                                    ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
                                    ->leftjoin('zones as z', 'z.id', '=', 'dc.zone_id')
                                    ->select('crm_requests.id as id', 'crm_requests.case_nature_id as case_nature_id','z.id as zone_id')
                                    ->where('crm_requests.agent_id','=',Null)
                                    ->where('crm_requests.case_nature_id','<>',3)
                                    ->get();
        foreach ($crm_requests as $value) {
            if($value->case_nature_id == 4){
                $agent_log = CrmAgentLog::where('case_nature_id',$value->case_nature_id)
                ->where('assinged_requests','<',90)
                ->whereDate('assigned_date',Carbon::today()->toDateString())
                ->orderBy('assinged_requests', 'asc')->get()->first();

               if($agent_log){
                $agent_log->assinged_requests = $agent_log->assinged_requests+1;
                $agent_log->save();
                
                CrmRequestAgentHistory::create([
                    'crm_request_id' => $value->id,
                    'agent_id' => $agent_log->admin_id
                ]);
                $value->agent_id = $agent_log->admin_id;
                $value->save();
                }
            }else{
                if($value->case_nature_type_id == 1){
                    $agent_log = CrmAgentLog::where('case_nature_id',4)
                    ->where('assinged_requests','<',90)
                    ->whereDate('assigned_date',Carbon::today()->toDateString())
                    ->orderBy('assinged_requests', 'asc')->get()->first();
    
                   if($agent_log){
                    $agent_log->assinged_requests = $agent_log->assinged_requests+1;
                    $agent_log->save();
                    
                    CrmRequestAgentHistory::create([
                        'crm_request_id' => $value->id,
                        'agent_id' => $agent_log->admin_id
                    ]);
                    $value->agent_id = $agent_log->admin_id;
                    $value->save();
                    }
                }else{
                    $agent_log = CrmAgentLog::where('zone_id',$value->zone_id)
                    ->where('case_nature_id',$value->case_nature_id)
                    ->where('assinged_requests','<',90)
                    ->whereDate('assigned_date',Carbon::today()->toDateString())
                    ->orderBy('assinged_requests', 'asc')->get()->first();
    
                   if($agent_log){
                    $agent_log->assinged_requests = $agent_log->assinged_requests+1;
                    $agent_log->save();
                    CrmRequestAgentHistory::create([
                        'crm_request_id' => $value->id,
                        'agent_id' => $agent_log->admin_id
                    ]);
                    $value->agent_id = $agent_log->admin_id;
                    $value->save();
                    }
                }

               

            }

               
        }

    }
}
