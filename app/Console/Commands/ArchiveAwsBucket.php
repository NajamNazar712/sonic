<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ArchiveAwsBucket extends Command
{
    protected $signature = 'archive:aws-bucket';
    protected $description = 'Archive replacement_parcel files to S3, month wise';

    public function handle()
    {
        $basePath = storage_path('app/public/replacement_parcel');
        $files = File::allFiles($basePath);

        $start = \Carbon\Carbon::parse('2024-04-01');
        $end   = \Carbon\Carbon::parse('2024-09-30 23:59:59');

        foreach ($files as $file) {
            $modifiedTime = \Carbon\Carbon::createFromTimestamp($file->getMTime());

            // only files in April → September 2024
            if ($modifiedTime->between($start, $end)) {
                // Get relative path inside replacement_parcel
                $relativePath = str_replace($basePath . '/', '', $file->getPathname());

                // Build S3 path
                $s3Path = "sonic_storage_archive/replacement_parcel/" . $relativePath;

                // Upload file
                $stream = fopen($file->getPathname(), 'r+');
                Storage::disk('s3')->put($s3Path, $stream);
                fclose($stream);

                // (Optional) remove local file after upload
                // \File::delete($file->getPathname());
            }
        }

        return Command::SUCCESS;
    }
}
