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
        $shipment_ids = array(18191333,18193659,18193989,18202872,18188580,18193072,18189655,18189340,18197690,18202792,18114253,18201919,18188311,18199301,18196558,18200812,18200956,18200567,18195789,18199566,18193079,18193990,18195671,18200880,18188496,18202648,18188888,18194623);

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
