<?php

namespace App\Console\Commands;

use App\Http\Models\HR\EmployeeLeave;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoRejectLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:reject-leaves';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto reject leave requests after 24 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $threshold = Carbon::now()->subHours(24);
        EmployeeLeave::where('status', 1)
            ->where('created_at', '<=', $threshold)
            ->update([
                'status' => 7,
                'rejected_reason' => 'Auto rejected: 24 hours exceeded',
                'updated_at' => Carbon::now()
            ]);

        $this->info('Old leave requests auto rejected successfully.');
    }
}
