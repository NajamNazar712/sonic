<?php

namespace App\Jobs;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AgentCallMonitoring;
use App\Http\Models\Admin\DeliveryNote;
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
        //
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
            $count = 1;
            $admin_ids = array();
            $admins = array();
            $admin_ids = AdminHub::where('hub_id',$delivery_note->hub_id)->pluck('admin_id')->toArray();
            if(count($admin_ids) > 0){

                $admins = Admin::whereIn('id', $admin_ids)->where('role_id', 18)->where('status',1)->pluck('id')->toArray();
                // AgentCallMonitoring::where('completed',0)->groupBy('agent_id')->count();
                $recs = array();
                if(count($admins) > 0){

                    foreach($admins as $admin_id) {
                        $admin = Admin::find($admin_id);
                        $rec = array();
                        $rec['admin_id'] = $admin_id;
                        $rec['count'] = AgentCallMonitoring::where('agent_id',$admin->id)->where('completed',0)->count();
                        $recs[] = $rec;


                        // if((AgentCallMonitoring::where('agent_id',$admin->id)->where('completed',0)->count())<$count){
                        //     $agent_id=$admin->id;
                        //     $count=AgentCallMonitoring::where('agent_id',$admin->id)->where('completed',0)->count();
                        // }else{
                        //     $count=AgentCallMonitoring::where('agent_id',$admin->id)->where('completed',0)->count();
                        //     $agent_id=$admin->id;
                        // }
                    }
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
}
