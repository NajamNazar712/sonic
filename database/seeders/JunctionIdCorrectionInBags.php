<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Http\Models\Admin\CargoManifest\V2JunctionMapping;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JunctionIdCorrectionInBags extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = CargoManifestBag::whereIn('junction_mapping_id', ['5502'
        ])->get();

        foreach($data as $d) {
            $record  = V2JunctionMapping::where('origin_id', $d->origin_hub_id)->where('destination_id', $d->destination_hub_id)->first();
            if($record) {
                CargoManifestBag::where('id', $d->id)->update(['junction_mapping_id' => $record->id]);
            }
        }
    }
}
