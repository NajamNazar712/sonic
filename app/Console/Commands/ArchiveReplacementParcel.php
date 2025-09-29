<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ArchiveReplacementParcel extends Command
{
    protected $signature = 'archive:replacement_parcel';
    protected $description = 'Zip month folders and upload to S3';

    public function handle()
    {
        $sourcePath = storage_path('app/public/crm_claims_archieve');

        // Generate months dynamically between 2023-11 and 2024-09
        $start = new DateTime('2023-11');
        $end   = new DateTime('2024-09');

        $months = [];
        while ($start <= $end) {
            $months[] = $start->format('Y-m');
            $start->modify('+1 month');
        }

        foreach ($months as $month) {
            $zipFile = $sourcePath . '/' . $month . '.zip';

            if (File::exists($zipFile)) {
                // Upload to S3
                $s3Path = "sonic_storage_archieve/crm_claims/{$month}.zip";
                $stream = fopen($zipFile, 'r+');
                Storage::disk('s3')->put($s3Path, $stream);
                fclose($stream);

                echo "Uploaded: {$s3Path}\n";

                // Optional: delete local zip after upload
                // File::delete($zipFile);
                // echo "Deleted local: {$zipFile}\n";
            } else {
                echo "File not found: {$zipFile}\n";
            }
        }
        $this->info("✅ All done!");
    }
}
