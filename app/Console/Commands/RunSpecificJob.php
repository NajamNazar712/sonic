<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunSpecificJob extends Command
{
    protected $signature = 'job:run {queuename} {limit=100}';

    protected $description = 'Run a specific queued job by ID';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $jobId = $this->argument('queuename');
        $limit = (int) $this->argument('limit');
        // Find the job by ID
        $jobs = DB::table('jobs')
            ->where('queue', 'email') // Filter by the email queue
            ->limit($limit)
            ->orderBy('id','acs')
            ->get();

        if (!$jobs) {
            $this->error("Job with queue name $jobId not found.");
            return;
        }

        foreach ($jobs as $job) {
            // Decode the payload
            $payload = json_decode($job->payload);

            // Unserialize the command (in this case, an email)
            $command = unserialize($payload->data->command);

            // Manually handle the job (send the email)
            $command->handle();

            // Optionally delete the job after it's processed
            DB::table('jobs')->where('id', $job->id)->delete();
        }

        $this->info("Processed {$jobs->count()} email jobs.");
    }
}
