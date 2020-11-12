<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelenorCallApiController;
use App\Http\Models\TelenorCallResponse;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TelenorCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telenor:call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call to consignee for response';

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
        $start_date = Carbon::yesterday()->format('Y-m-d');
        $start_date = $start_date . '16:00:00';
        $date = Carbon::today()->format('Y-m-d');
        $end_date = $date . '16:00:00';

        $void_shipments = TelenorCallResponse::whereDate('created_at', $date)->whereIn('status', [1, 2, 3])->whereIn('response', [1,2])->pluck('shipment_id')->toArray();

        TelenorCallApiController::call($date, $start_date, $end_date, $void_shipments);
    }
}
