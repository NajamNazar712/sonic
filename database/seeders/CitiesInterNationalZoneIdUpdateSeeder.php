<?php

namespace Database\Seeders;

use App\ChangeLogs;
use App\Http\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CitiesInterNationalZoneIdUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $filePath = storage_path('app/ZoneDHLLIST.xlsx');

        // Load Excel file
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row

            $countryData = $row[0]; // "Countries & Territories"
            $zone = $row[1]; // "Zone"
            $zoneid = DB::table('international_dhl_zones')
                ->where('zone_name', $zone)
                ->first();

            if (!empty($zoneid)) {
                $model = City::where('name', $countryData)->first();

                if ($model) {
                    $oldData = $model->getOriginal();

                    $model->zone_id = $zoneid->zone_id;

                    // Detect changed attributes before saving
                    $changes = $model->getDirty();

                    if (!empty($changes)) {
                        $model->save();

                        $oldChanges = [];
                        $newChanges = [];

                        foreach ($changes as $key => $newValue) {
                            $oldChanges[$key] = $oldData[$key] ?? null;
                            $newChanges[$key] = $newValue;
                        }


                        ChangeLogs::create([
                            'table_name' => $model->getTable(),
                            'record_id'  => $model->getKey(),
                            'old_data'   => json_encode($oldChanges, JSON_UNESCAPED_UNICODE),
                            'new_data'   => json_encode($newChanges, JSON_UNESCAPED_UNICODE),
                            'updated_by' => 346,
                        ]);
                    }
                }
            }
            // DB::table('cities as c1')
            // ->join('cities as c2', 'c1.hub_id', '=', 'c2.hub_id')
            // ->where('c2.name', $countryData)
            // ->update(['c1.zone_id' => $zone, 'c1.updated_at' => now()]);
        //    $zoneCities = City::where('name', $countryData)->first()->hub_id;
        //    $update = City::where('hub_id',$zoneCities)->update(['zone_id' => $zone]); 
           
        }
    }
}
