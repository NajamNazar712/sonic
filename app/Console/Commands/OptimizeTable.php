<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OptimizeTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize-table';

    protected $description = 'Puts the app in maintenance mode, optimizes the table, and brings it back up';

    public function handle()
    {
        $start = now();
        $logLines = [];

        $logLines[] = "🚀 Optimize Command Started at: $start";

        try {
            // Put the app into maintenance mode
            Artisan::call('down');
            $logLines[] = "✅ App is now in maintenance mode.";

            // Run the OPTIMIZE TABLE command
            $logLines[] = '📦 Running OPTIMIZE TABLE shipments_journey...';
            $result = DB::select('OPTIMIZE TABLE shipments_journey');

            foreach ($result as $row) {
                $jsonRow = json_encode($row);
                $this->line($jsonRow); // Output to console
                $logLines[] = "📝 Result: $jsonRow"; // Append to log
            }
            $logLines[] = '📦 Running OPTIMIZE TABLE shipments...';
            $result = DB::select('OPTIMIZE TABLE shipments');

            foreach ($result as $row) {
                $jsonRow = json_encode($row);
                $this->line($jsonRow); // Output to console
                $logLines[] = "📝 Result: $jsonRow"; // Append to log
            }
            // Bring the app back online
            Artisan::call('up');
            $logLines[] = "✅ App is back online.";

            $end = now();
            $duration = $start->diffInSeconds($end);

            $logLines[] = "⏱ Completed at: $end";
            $logLines[] = "⏱ Execution time: {$duration} seconds";
        } catch (\Throwable $th) {
            $logLines[] = "❌ Exception occurred: " . $th->getMessage();
            // Always try to bring the app back up if something failed
            Artisan::call('up');
        }

        // Write all lines to the custom cronJobLog channel
        foreach ($logLines as $line) {
            Log::channel('cronJobLog')->info($line);
        }
    }
}
