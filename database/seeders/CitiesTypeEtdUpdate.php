<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CitiesTypeEtdUpdate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $filePath = storage_path('app/citiesetdupdate.xlsx');

        // Load Excel file
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        // Remove header row
        array_shift($rows);

        foreach ($rows as $row) {
            $cityName = $row[0] ?? null;  // Column A (City Name)
            $cityType = $row[1] ?? null;  // Column B (City Type)

            if ($cityName && $cityType && is_numeric($cityType)) {
                // Clean the city name (remove duplicate markers and trim)
                $cleanName = trim(preg_replace('/\s*\(.*?\)\s*/', '', $cityName));
           
                // Update matching cities in the database
                $updated = DB::table('cities')
                    ->where('name', 'like', $cleanName . '%')
                    ->update(['city_type_etd_id' => (int)$cityType]);

                // Output progress
                $this->command->info("Processed: {$cleanName} => Type {$cityType} (" . ($updated ? "$updated records updated" : "no matches") . ")");
            }
        }

        $this->command->info('City type update completed successfully!');
    }
}
