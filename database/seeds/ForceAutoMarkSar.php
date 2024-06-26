<?php

use App\Http\Models\RvShipmentAssignAgent;
use Illuminate\Database\Seeder;
use App\Http\Traits\RvTrait;

class ForceAutoMarkSar extends Seeder
{
    use RvTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $shipment = RvShipmentAssignAgent::join('rv_shipment_tickets as rvt','rvt.shipment_id', 'rv_shipment_assign_agents.shipment_id')->where('unresponsive_count',1)->where(['deleted_at'=>null, 'disabled_shipper' => 0, 'rvt.call_count'=>1])->limit(1)->get();
        
        foreach($shipment as $data){
            $this->unresponsiveForceFully($data);
        }
    }
}
