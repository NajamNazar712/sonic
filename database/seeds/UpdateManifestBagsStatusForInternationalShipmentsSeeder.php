<?php

use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use Illuminate\Database\Seeder;

class UpdateManifestBagsStatusForInternationalShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bags = CargoManifestBag::join('cities as c','c.id','cargo_manifest_bags.destination_hub_id')
            ->select('cargo_manifest_bags.seal_number','cargo_manifest_bags.shipments','cargo_manifest_bags.actual_weight','cargo_manifest_bags.')
    }
}
