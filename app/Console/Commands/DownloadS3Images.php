<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DownloadS3Images extends Command
{
    protected $signature = 's3:download-images';
    protected $description = 'Download images from S3 to local storage';

    public function handle()
    {
        // Fetch image filenames from the database
        $images = DB::table('return_note_images AS rni')
            ->join('return_note_shipments AS rns', 'rns.return_note_id', '=', 'rni.return_note_id')
            ->join('shipments AS s', 's.id', '=', 'rns.shipment_id')
            ->whereIn('s.tracking_number', [34015233887621, 20220334206500])
            ->pluck('rni.image');

        if ($images->isEmpty()) {
            $this->warn('No images found for the given tracking numbers.');
            return;
        }

        foreach ($images as $image) {
            $s3Path = 'return_note_images/' . $image;
            $localPath = public_path('return_note_images/' . $image);;

            if (Storage::disk('s3')->exists($s3Path)) {
                try {
                    $fileContent = Storage::disk('s3')->get($s3Path);
                    Storage::disk('local')->put($localPath, $fileContent);
                    $this->info("Downloaded: $image");
                } catch (\Exception $e) {
                    Log::error("Failed to download image: $image. Error: " . $e->getMessage());
                    $this->error("Failed to download image: $image");
                }
            } else {
                $this->warn("Image not found in S3: $s3Path");
            }
        }

        $this->info('Image download process completed.');
    }
}
