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
        $shipment_ids = array(23996315,24001497,24049073,23998203,23970950,23848970,23959661,23783648,23894761,23919991,24013273,23809283,23919513,23956153,23936152,24014560,23715936,23903634,23900538,23994032,23936399,23794787);

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
