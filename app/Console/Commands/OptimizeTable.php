<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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
        $this->info("Started at: $start");

        // Artisan::call('down');

        $this->info('Running OPTIMIZE TABLE...');
        $result = DB::select('OPTIMIZE TABLE orders');

        foreach ($result as $row) {
            $this->line(json_encode($row));
        }

        // Artisan::call('up');

        $end = now();
        $this->info("Completed at: $end");
        $this->info('Execution time: ' . $start->diffInSeconds($end) . ' seconds');

        \Log::info('OPTIMIZE completed in ' . $start->diffInSeconds($end) . ' seconds');
    }
}
