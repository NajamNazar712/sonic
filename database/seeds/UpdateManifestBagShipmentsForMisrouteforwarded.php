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
        $shipment_ids = array(17607235, 17649212, 17662940, 17681866, 17695019, 17710064, 17710209, 17710222, 17715576, 17718022, 17729288, 17738512, 17752873, 17755551, 17759829, 17763689,17765587, 17776217, 17982874, 18177656);

        foreach ($shipment_ids as $shipment_id) {
            $bag_shipment = CargoManifestBagShipments::where('shipment_id',$shipment_id)->where('status',1);
            if($bag_shipment->exists()) {
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
}
