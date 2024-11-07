<?php

use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagJourney;
use Illuminate\Database\Seeder;

class UpdateCargoManifestBagForCurrentHubIdTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bags = CargoManifestBag::get();
        foreach ($bags as $bag){
            $last_journey = CargoManifestBagJourney::where('cargo_manifest_bag_id', $bag->id)->whereNotNull('junction_id')->orderBy('id', 'desc')->first();
            if($last_journey){
                $bag->current_hub_id = $last_journey->junction_id;
                $bag->save();
            }
        }
    }
}
