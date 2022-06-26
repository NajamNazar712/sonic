<?php

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Database\Seeder;

class UpdateManifestBagShipmentsStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipment_ids = array(16420995,16657261,16857447,16906846,16976008,17015776,17023389,17024338,17028810,17036909,17040355,17063523,17079769,17081537,17082414,17083290,17084492,17086526,17093177,17093219,17107787,17123852,17137748,17140589,17146031);

        foreach ($shipment_ids as $shipment_id){
            $shipment = Shipment::find($shipment_id);
            if($shipment){
                if($shipment->shipper_status_id == 49 || $shipment->shipper_status_id == 11){
                    $mis_fwd = ShipmentsJourney::where('shipment_id',$shipment_id)->where('shipper_status_id',49)->orderBy('id','desc')->first();
                    if($mis_fwd){
                        $mis_fwd->delete();
                    }
                    $mis = ShipmentsJourney::where('shipment_id',$shipment_id)->where('shipper_status_id',11)->orderBy('id','desc')->first();
                    if($mis){
                        $mis->delete();
                    }
                    $journey = ShipmentsJourney::where('shipment_id',$shipment_id)->orderBy('id','desc')->first();
                    Shipment::where('id',$shipment_id)->update(['shipper_status_id' => $journey->shipper_status_id,'consignee_status_id' => $journey->consignee_status_id ]);
                }
            }
        }
    }
}
