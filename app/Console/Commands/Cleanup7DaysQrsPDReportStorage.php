<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Cleanup7DaysQrsPDReportStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:7DaysOlderQrsPDReportStorage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files from qsr_pending_deliveries_reports folder older than 7 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $directory = storage_path('public/qsr_pending_deliveries_reports'); 

        $sevenDaysAgo = now()->subDays(7);

        if (File::isDirectory($directory)) {
            $files = File::allFiles($directory);

            foreach ($files as $file) {
                $fileTimestamp = File::lastModified($file);

                if ($fileTimestamp < $sevenDaysAgo->timestamp) {
                    File::delete($file);
                    $this->info('Deleted file: ' . $file->getFilename());
                }
            }
        } else {
            $this->info('The directory does not exist: ' . $directory);
        }
    }
}
