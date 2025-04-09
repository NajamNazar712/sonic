<?php

namespace Database\Seeders;

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
        $filePath = storage_path('app/Zone List DHL.xlsx');

        // Load Excel file
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row

            $countryData = $row[0]; // "Countries & Territories"
            $zone = $row[1]; // "Zone"
            DB::table('cities as c1')
            ->join('cities as c2', 'c1.hub_id', '=', 'c2.hub_id')
            ->where('c2.name', $countryData)
            ->update(['c1.zone_id' => $zone, 'c1.updated_at' => now()]);
        //    $zoneCities = City::where('name', $countryData)->first()->hub_id;
        //    $update = City::where('hub_id',$zoneCities)->update(['zone_id' => $zone]); 
           
        }
    }
}
