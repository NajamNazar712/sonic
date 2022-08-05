<?php

use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Database\Seeder;

class UpdateManifestBagShipmentsForMisrouteforwarded extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipment_ids = array(18260175, 18260199, 18268595, 18271315, 18283062, 18283474, 18283765, 18284026, 18286016, 18286144, 18286339, 18286481, 18286616, 18287447, 18288154, 18288762, 18288953, 18289472, 18290212, 18290358, 18290474, 18290996, 18291789, 18292113, 18292342, 18292959, 18294225, 18296647, 18297637, 18297758, 18298412, 18298610, 18283893, 18283899, 18287850, 18260239, 18283919, 18286497, 18292084, 18294631, 18271296, 18298610, 18297758, 18297637, 18294631, 18294225, 18292959, 18292342, 18292113, 18292084, 18291789, 18290996, 18290474, 18290212, 18289472, 18288953, 18288762, 18288154, 18287850, 18287447, 18286497, 18286481, 18286144, 18284026, 18283919, 18283899, 18283893, 18283765, 18283474, 18283062, 18271315, 18271296, 18268595, 18260239, 18260199, 18260175, 17695019);

        foreach ($shipment_ids as $shipment_id) {

            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                if ($shipment->shipper_status_id == 49 || $shipment->shipper_status_id == 11) {
                    $mis_fwd = ShipmentsJourney::where('shipment_id', $shipment_id)->where('shipper_status_id', 49)->orderBy('id', 'desc')->first();
                    if ($mis_fwd) {
                        $mis_fwd->delete();
                    }
                    $mis = ShipmentsJourney::where('shipment_id', $shipment_id)->where('shipper_status_id', 11)->orderBy('id', 'desc')->first();
                    if ($mis) {
                        $mis->delete();
                    }
                    $journey = ShipmentsJourney::where('shipment_id', $shipment_id)->orderBy('id', 'desc')->first();
                    Shipment::where('id', $shipment_id)->update(['shipper_status_id' => $journey->shipper_status_id, 'consignee_status_id' => $journey->consignee_status_id]);
                }
            }

        }
    }
}
