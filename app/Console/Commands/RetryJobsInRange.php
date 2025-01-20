<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class RetryJobsInRange extends Command
{
    protected $signature = 'queue:retry-range {startId=0} {endId=0} {queue=null}';
    protected $description = 'Retry failed jobs in a specific job ID range';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $startId = (int) $this->argument('startId');
        $endId = (int) $this->argument('endId');
        $queue = (string) $this->argument('queue');
        // Fetch failed jobs within the range from the failed_jobs table
        if($queue != null){
            $failedJobs = DB::table('failed_jobs')
            ->where('queue',$queue)
                ->get();
        }else{
            $failedJobs = DB::table('failed_jobs')
                ->whereBetween('id', [$startId, $endId])
                ->get();
        }

        if ($failedJobs->isEmpty()) {
            $this->info('No failed jobs found in the specified range.');
            return;
        }

        // Loop through each job and retry it
        foreach ($failedJobs as $job) {
            Artisan::call('queue:retry', ['id' => $job->id]);
            $this->info("Retried job with ID: {$job->id}");
        }

        $this->info('All jobs in the range have been retried.');
    }
}
