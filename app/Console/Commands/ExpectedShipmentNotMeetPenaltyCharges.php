<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\Shipper\User;
use DB;
use Carbon\Carbon;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;


class ExpectedShipmentNotMeetPenaltyCharges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penalty:expected_shipments_not_meet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start_date = Carbon::now()->subMonth()->startOfMonth();
        $end_date   = Carbon::now()->subMonth()->endOfMonth();

        // $user = User::where('sub_segment_id', 5)->where('status', 3)->where('percentage_on_expected_shipments', '>', 0)->get();

        $users = User::select('id', 'average_shipments')
        ->where('sub_segment_id', 5)
        ->where('status', 3)
        ->where('percentage_on_expected_shipments', '>', 0)
        ->get();

        $user->chunk(500)->each(function ($chunked_users) use ($start_date, $end_date )
        {
            foreach($chunked_users as $chunked_user) {
               $result = Shipment::where('user_id', $chunked_user->id)
                ->whereNotIn('status', [1, 17])
                ->whereBetween('created_at', [$start_date, $end_date])
                ->selectRaw('COUNT(*) as total_shipments, SUM(charges) as total_charges')
                ->first();

                if ($result->total_shipments < $chunked_user->average_shipments) {
                    $totalCharges = $result->total_charges ?? 0;
                }
            }
           

        });



        
    }
}
