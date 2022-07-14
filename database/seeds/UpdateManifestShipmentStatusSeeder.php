<?php

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Database\Seeder;

class UpdateManifestShipmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipment_ids = array(16841384, 16841434, 16842232, 16842267, 16842270, 16842403, 16849434, 16849865, 16849930, 16850114, 16850985, 16851317, 16872990, 16873006, 16887049, 16889316, 16891233, 16891243, 16891991, 16893686, 16894611, 16895692, 16896894, 16897081, 16897123, 16897372, 16922210, 16923454, 16923718, 16924341, 16924794, 16925048, 16925127, 16925665, 16928177, 16928559, 16930322, 16931178, 16931560, 16931593, 16931772, 16950332, 16841915, 16844052, 16844422, 16850286, 16850329, 16851013, 16858963, 16865926, 16868655, 16868850, 16869035, 16871359, 16873264, 16891721, 16958282, 16958469, 16841440, 16841931, 16842224, 16842315, 16843101, 16843196, 16843230, 16849107, 16849156, 16850441, 16866821, 16877448, 16887433, 16887444, 16892046, 16923611, 16923674, 16924309, 16924518, 16925137, 16925216, 16926010, 16926245, 16926873, 16928351, 16928511, 16929198, 16929948, 16930324, 16931286, 16931861, 16931888, 16932014, 16958369);

        foreach ($shipment_ids as $shipment_id){
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
