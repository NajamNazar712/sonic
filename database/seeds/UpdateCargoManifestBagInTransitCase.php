<?php

use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use Illuminate\Database\Seeder;

class UpdateCargoManifestBagInTransitCase extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seal_numbers = array(22331227315490, 2218932, 2089983, 2089744, 2089736, 2448124, 20240627517147);
        foreach ($seal_numbers as $seal_number) {
            $cargo_manifest_bag = CargoManifestBag::where('seal_number', $seal_number)->where('status_id', '!=', 7);
            if($cargo_manifest_bag->exists()){
                $cargo_manifest_bag = $cargo_manifest_bag->first();
                $cargo_manifest_bag_id = $cargo_manifest_bag->id;

                $cargo_manifest_bag_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $cargo_manifest_bag_id)->where('status', 0);
                
                if($cargo_manifest_bag_shipments->exists()){
                    $cargo_manifest_bag_shipments = $cargo_manifest_bag_shipments->first();
                    $cargo_manifest_bag_shipments->status = 1;
                    $cargo_manifest_bag_shipments->update();
                }

                $cargo_manifest_bag->status_id = 7;
                $cargo_manifest_bag->received_shipments = $cargo_manifest_bag->shipments;
                $cargo_manifest_bag->short_received_shipments = 0;
                $cargo_manifest_bag->completed = 1;
                $cargo_manifest_bag->update();
                
            }
        }
    }
}
