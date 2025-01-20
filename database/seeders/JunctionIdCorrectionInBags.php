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
        $data = CargoManifestBag::whereIn('junction_mapping_id', [142,
        196,
        247,
        299,
        352,
        404,
        456,
        508,
        560,
        1652,
        1652,
        1704,
        1752,
        1804,
        1856,
        1912,
        1977,
        1978,
        1979,
        1980,
        1981,
        1982,
        1983,
        1984,
        1985,
        1986,
        1987,
        1988,
        1989,
        1990,
        1991,
        1992,
        1993,
        1994,
        1995,
        1996,
        1997,
        1998,
        1999,
        3350,
        3358,
        3501,
        3545,
        3661,
        3687,
        3753,
        3847,
        3904,
        4245,
        4275,
        4829,        
        ])->get();

        foreach($data as $d) {
            $record  = V2JunctionMapping::where('origin_id', $d->origin_hub_id)->where('destination_id', $d->destination_hub_id)->first();
            if($record) {
                CargoManifestBag::where('id', $d->id)->update(['junction_mapping_id' => $record->id]);
            }
        }
    }
}
