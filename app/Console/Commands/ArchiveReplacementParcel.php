<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ArchiveReplacementParcel extends Command
{
    protected $signature = 'archive:replacement_parcel';
    protected $description = 'Zip month folders and upload to S3';

    public function handle()
    {
        $sourcePath = storage_path('app/public/replacement_archive_final');
        $months = ['2024-04', '2024-05', '2024-06', '2024-07', '2024-08', '2024-09'];

        foreach ($months as $month) {
            $zipFile = $sourcePath . '/' . $month . '.zip';

            if (File::exists($zipFile)) {
                // Upload to S3
                $s3Path = "sonic_storage_archieve/replacement_parcel/{$month}.zip";
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
