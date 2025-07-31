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
        $this->info('🔧 Putting app into maintenance mode...');
        Artisan::call('down', [
            '--message' => 'Optimizing shipment_scanning_journey table...',
            '--retry' => 60,
        ]);

        $this->info('Running OPTIMIZE TABLE...');
        DB::statement('OPTIMIZE TABLE shipment_scanning_journey');

        $this->info('Optimization complete. Bringing app back online...');
        Artisan::call('up');

        $this->info('Done.');
    }
}
