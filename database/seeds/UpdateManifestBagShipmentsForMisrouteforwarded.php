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
        $shipment_ids = array(20065842	, 20096359, 20126654, 20141556, 20146919, 20147964, 20156914, 20157201, 20157987, 20158206, 20158454, 20159457, 20161342, 20161780, 20162137, 20162152, 20162172, 20162575, 20162752, 20163048, 20165599, 20165705, 20165773, 20166156, 20166244, 20166729, 20168721, 20169129, 20169462, 20170289, 20171137, 20171293, 20171327, 20171935, 20172826, 20173021, 20173209, 20173305, 20176616, 20176625);

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
