<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ArchiveAwsBucket extends Command
{
    protected $signature = 'archive:aws-bucket';
    protected $description = 'Archive replacement_parcel files to S3, month wise';

    public function handle()
    {
        $localPath = storage_path('app/public/replacement_parcel');

        // Iterate files in the directory
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($localPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $modifiedTime = Carbon::createFromTimestamp($file->getMTime());

                // Only between 2024-04-01 and 2024-09-30
                if ($modifiedTime->between(Carbon::parse('2024-04-01'), Carbon::parse('2024-09-30 23:59:59'))) {
                    $monthFolder = 'replacement_parcel/' . $modifiedTime->format('Y-m');

                    $filePath   = $file->getRealPath();
                    $fileName   = $file->getBasename();

                    // Destination path in S3
                    $s3Path = "sonic-archive/sonic_storage_archieve/{$monthFolder}/{$fileName}";

                    $stream = fopen($filePath, 'r+');
                    Storage::disk('s3')->put($s3Path, $stream);
                    fclose($stream);

                    $this->info("Uploaded: {$s3Path}");
                }
            }
        }

        return Command::SUCCESS;
    }
}
