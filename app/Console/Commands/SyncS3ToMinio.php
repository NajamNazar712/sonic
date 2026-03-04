<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncS3ToMinio extends Command
{
    // Pass the date dynamically when running the command
    protected $signature = 'sync:s3-minio {date}';
    protected $description = 'Sync files from AWS S3 to MinIO for a specific date';

    public function handle()
    {
        $date = $this->argument('date'); // YYYY-MM-DD
        $this->info("Starting sync for date: {$date}");

        // Get all S3 files for this date
        $files = $this->getS3FilesByDate(
            $disk = 's3minio',       // AWS S3
            $bucket = 'sonic-archive',
            $date
        );
        if (empty($files)) {
            $this->info("No files found for {$date}");
            return 0;
        }

        foreach ($files as $path) {
            if (!Storage::disk('s3')->exists('sonic-archive/' . $path)) { // check MinIO
                $stream = Storage::disk('s3minio')->readStream($path);
                Storage::disk('s3')->writeStream('sonic-archive/'.$path, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                $this->info("Moved: {$path}");
            } else {
                $this->info("Already exists in MinIO: {$path}");
            }
        }

        $this->info("Sync completed for {$date}");
        return 0;
    }

    /**
     * Get all files from S3 for a specific date
     */
    private function getS3FilesByDate($disk, $bucket, $date)
    {
        $s3Client = Storage::disk($disk)->getClient();
        $params = [
            'Bucket' => $bucket,
        ];

        $paginator = $s3Client->getPaginator('ListObjectsV2', $params);
        $matchedFiles = [];

        foreach ($paginator as $page) {

            if (!isset($page['Contents'])) continue;

            foreach ($page['Contents'] as $object) {
                $lastModified = $object['LastModified']->format('Y-m-d');
                $object;
                // if ($lastModified === $date) {
                    $matchedFiles[] = $object['Key'];
                // }
            }
        }

        return $matchedFiles;
    }
}
