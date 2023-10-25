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
        $seal_numbers = array(2447359, 22315827645454, 2198131, 2411328, 2237288, 17422327743493, 2355531, 2488434);
        foreach ($seal_numbers as $seal_number) {
            $cargo_manifest_bags = CargoManifestBag::where('seal_number', $seal_number);
            if($cargo_manifest_bags->exists()){
                $cargo_manifest_bags = $cargo_manifest_bags->get();
                foreach ($cargo_manifest_bags as $cargo_manifest_bag) {
                    # code...
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
}
