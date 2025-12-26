<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NegativePayableAllowShipperZeroCod;
use App\Models\NegativePayableAllowShipperZeroCodLogs;
use Carbon\Carbon;

class RemoveExpiredZeroCodShippers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shippers:remove-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove zero COD shippers older than 48 hours and log them';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredShippers = NegativePayableAllowShipperZeroCod::where('created_at', '<', now()->subHours(48))->get();

        foreach ($expiredShippers as $shipper) {

            NegativePayableAllowShipperZeroCodLogs::create([
                'user_id'    => $shipper->user_id,
                'added_by'   => $shipper->added_by,
                'added_at'   => $shipper->created_at,
                'removed_by' => 346, 
            ]);

            // Delete from main table
            $shipper->delete();
        }

        //$this->info('Expired zero COD shippers removed and logged: ' . $expiredShippers->count());
    }
}
