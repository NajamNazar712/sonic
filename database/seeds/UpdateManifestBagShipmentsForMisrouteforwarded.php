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
        $shipment_ids = array(20996273,20921174,20958961,20963268,20965618,20967306,20968778,20969019,20969156,20972747,20994263,20995044,20942460,20962624,20963628,20964096,20967607,20969470,20970822,20971284,20974774,20977240,20978253,20988098,20999796,21001288);

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
