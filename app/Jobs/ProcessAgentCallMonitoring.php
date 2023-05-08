<?php

namespace App\Jobs;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AgentCallMonitoring;
use App\Http\Models\Admin\AgentDay;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

class ProcessAgentCallMonitoring implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $booking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $booking)
    {
        $this->queue = 'agent_call_monitoring';
        $this->booking = $booking;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shipment_id = $this->booking['shipment_id'];
        $delivery_note = DeliveryNote::find($this->booking['delivery_note_id']);
        if($delivery_note){
            $rider = Rider::find($delivery_note->rider_id);
            if($rider){
                if($rider->rider_type_id != 2 && $rider->operation_rider_id != 2){

                    $count = 1;
                    $admin_ids = array();
                    $admins = array();
        
                    $settings = GlobalSettings::where('type', 'debriefing_time_setting');
        
                    if ($settings->exists()) {
                        $settings = $settings->first();
                        $time = $settings->text;
                    }
                    else {
                        $time = 0;
                    }
                    $next_time = Carbon::today()->endOfDay()->addHours($time)->toDateTimeString();
                   
                    $prev_time = Carbon::today()->addHours($time)->toDateTimeString();
         
                    if(AgentCallMonitoring::where('delivery_note_id',$delivery_note->id)->where('shipment_id', $shipment_id)->where('created_at','>=',$prev_time)->where('created_at','<=', $next_time)->exists()){
                         return false;
                    }
                    $admin_ids = AdminHub::where('hub_id',$delivery_note->hub_id)->pluck('admin_id')->toArray();

                    $settings = GlobalSettings::where('type', 'debriefing_role_setting')->get();
                    $roles = explode(',',$settings[0]->text);
                    
                    $today = Carbon::now()->format('Y-m-d');
                    $admin_ids = AgentDay::where('date',$today)
                        ->where('status',1)
                        ->whereIn('agent_id',$admin_ids)
                        ->pluck('agent_id')
                        ->toArray();
                    $admins = Admin::whereIn('id', $admin_ids)->whereIn('role_id', $roles)->where('status',1)->pluck('id')->toArray();
                    $recs = array();
                    if(count($admins) > 0){
                   
                        foreach($admins as $admin_id) {
    
                            $rec = array();
                            $rec['admin_id'] = $admin_id;
    
                            if(AgentCallMonitoring::where('agent_id',$admin_id)->where('created_at','>=',$prev_time)->where('created_at','<=', $next_time)->exists()){
                                $rec['count'] = AgentCallMonitoring::where('agent_id',$admin_id)->where('created_at','>=',$prev_time)->where('created_at','<=', $next_time)->count();
                            }
                            else{
                                $rec['count'] = 0;
                            }
                            $recs[] = $rec;
                        }
    
                        if(count($recs) > 0){
                            $recs = collect($recs);
                            $min = $recs->where('count', $recs->min('count'))->first();
                            $agent_call_monitoring = new AgentCallMonitoring;
                            $agent_call_monitoring->agent_id= $min['admin_id'];
                            $agent_call_monitoring->shipment_id= $shipment_id;
                            $agent_call_monitoring->delivery_note_id= $delivery_note->id;
                            $agent_call_monitoring->save();
                        }
                    }
                }

            }

//            if(count($admin_ids) > 0){

               
//                $admins = Admin::join('employee_attendances as ea','ea.employee_id','=','admins.employee_id')
//                ->whereIn('admins.id', $admin_ids)
//                ->where('admins.role_id', 18)
//                ->where('admins.status',1)
//                ->where('ea.clock_out_datetime','=',null)
//                ->whereDate('ea.attendance_date','=',Carbon::now()->format('Y-m-d'))->pluck('admins.id')->toArray();

                
//            }
        }

    }
}
