<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Queue\CallQueuedHandler;
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
        $queueName = $this->argument('queuename');  // Get queue name argument
        $limit = (int) $this->argument('limit');    // Get the limit argument (default: 100)

        // Fetch the jobs based on queue name and limit
        $jobs = DB::table('jobs')
        ->where('queue', $queueName)            // Filter by the specified queue name
            ->limit($limit)                         // Limit to the specified number
            ->orderBy('id', 'asc')                  // Order by ascending ID
            ->get();

        // Initialize Laravel's job handler
        $jobHandler = app(CallQueuedHandler::class);

        // Process the jobs
        foreach ($jobs as $jobRecord) {
            // Get the payload and deserialize the job
            $payload = json_decode($jobRecord->payload, true);
            $command = unserialize($payload['data']['command']);

            // Process the job by calling Laravel's CallQueuedHandler
            try {
                $jobHandler->call($command, ['id' => $jobRecord->id]);
                $this->info("Successfully processed Job ID: {$jobRecord->id}");
            } catch (\Exception $e) {
                $this->error("Error processing Job ID: {$jobRecord->id} - " . $e->getMessage());
            }
        }

        $this->info("Processed {$jobs->count()} jobs from the '{$queueName}' queue.");
    }
}
