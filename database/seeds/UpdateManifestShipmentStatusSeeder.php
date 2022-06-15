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
        $shipment_ids = array(17331222,17321167,17261069,17320162,17326493,17320575,17324240,17210503,17254069,17321235,17321247,17319145,17319886,17324161,17270208,17319928,17323040,17319965,17320157,17334304,17326636,17321153,17319042,17321161,17324791,17320520,17320673,17321258,17308532,17277238,17329406,17323882,17277506,17338176,17277037,17331128,17334303,17330401,17324283,17277043,17320906,17277713,17277590,17320439,17324341,17315472,17330153,17319180,17320149,17309921,17324332,17321216,17330763,17324085,17303610,17277450,17268856,17320063,17323956,17311539,17324687,17328651,17308530,17334312,17332455,17330748,17327990,17333803,17329623,17323347,17331168,17320516,17322445,17320577,17322868,17303233,17319423,17325490,17333077,17303215,17322924,17326255,17326443,17275209,17327165,17331231,17329191,17320695,17323181,17322824,17308546,17312572,17328483,17334310,17329713,17332422,17334308,17308528,17328070,17327616,17330521,17327224,17329034,17309429,17330859,17336043);

        foreach ($shipment_ids as $shipment_id){
            $mis_fwd = ShipmentsJourney::where('shipment_id',$shipment_id)->where('shipper_status_id',49)->latest()->first();
            if($mis_fwd){
                $mis_fwd->delete();
            }
            $mis = ShipmentsJourney::where('shipment_id',$shipment_id)->where('shipper_status_id',11)->latest()->first();
            if($mis){
                $mis->delete();
            }

            $journey = ShipmentsJourney::where('shipment_id',$shipment_id)->latest()->first();
            Shipment::where('id',$shipment_id)->update(['shipper_status_id' => $journey->shipper_status_id,'consignee_status_id' => $journey->consignee_status_id ]);
        }


    }
}
