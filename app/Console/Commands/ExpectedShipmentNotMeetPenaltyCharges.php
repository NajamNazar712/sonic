<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\Shipper\User;
use DB;
use Carbon\Carbon;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Models\ExpectedShipmentPenaltyAdjustment;


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
        // $users = User::select('id', 'average_shipments', 'percentage_on_expected_shipments')
        // ->where('sub_segment_id', 5)
        // ->where('status', 3)
        // ->where('percentage_on_expected_shipments', '>', 0)
        // ->get();

        $users = User::select(
            'users.id',
            'users.average_shipments',
            'pos.percentage_on_expected_shipments'
        )
        ->join('percentage_on_expected_shipments as pos', 'pos.user_id', '=', 'users.id')
        ->where('users.sub_segment_id', 5)
        ->where('users.status', 3)
        ->where('pos.percentage_on_expected_shipments', '>', 0)
        ->get();

        //dd($users);

        $users->chunk(500)->each(function ($chunked_users) use ($start_date, $end_date )
        {
            $insertData = [];
            foreach($chunked_users as $chunked_user) {
                $result = Shipment::from('shipments')
                ->leftJoin(
                    'shipment_additional_charges',
                    'shipment_additional_charges.shipment_id',
                    '=',
                    'shipments.id'
                )
                ->leftJoin(
                    'shipment_services_charges',
                    'shipment_services_charges.shipment_id',
                    '=',
                    'shipments.id'
                )
                ->where('shipments.user_id', $chunked_user->id)
                ->whereNotIn('shipments.shipper_status_id', [1, 17])
                ->whereBetween('shipments.created_at', [$start_date, $end_date])
                ->selectRaw('
                    COUNT(DISTINCT shipments.id) as total_shipments,
                    MAX(shipments.id) as last_shipment_id,
                    SUM(
                        COALESCE(shipments.weight_charges, 0) +
                        COALESCE(shipments.cash_handling_charges, 0) +
                        COALESCE(shipments.insurance_charges, 0) +
                        COALESCE(shipments.packaging_material_charges, 0) +
                        COALESCE(shipments.fuel_surcharge, 0) +
                        COALESCE(shipments.return_charges, 0) +
                        COALESCE(shipments.replacement_charges, 0) +
                        COALESCE(shipments.try_and_buy_charges, 0) +
                        COALESCE(shipments.nsa_osa_charges, 0) +
                        COALESCE(shipments.intercept_charges, 0) +
                        COALESCE(shipments.packaging_charges, 0) +
                        COALESCE(shipment_additional_charges.faf_charges, 0) +
                        COALESCE(shipment_additional_charges.wallet_charges, 0) +
                        COALESCE(shipment_services_charges.reverse_pickup_charges, 0)
                    ) as total_charges
                ')
                ->first();
                dd($result);
                
                if ($result->total_shipments != 0 && $result->total_shipments < $chunked_user->average_shipments) {
                    $total_charges = $result->total_charges ?? 0;
                    $percentage = $chunked_user->percentage_on_expected_shipments ?? 0;
                    $percentage_amount = round(($total_charges * $percentage) / 100, 2);

                    $insertData[] = [
                        'shipment_id' => $result->last_shipment_id,
                        'user_id' => $chunked_user->id,
                        'recorded_shipments' => $result->total_shipments,
                        'average_shipments' => $chunked_user->average_shipments,
                        'percentage_applied' => $percentage,
                        'adjustment_type_id' => 12,
                        'amount' => $percentage_amount,
                        'status' => 1, // created
                        'total_amount' => $total_charges,
                        'applied_month' => $start_date->format('Y-m'),
                        //'status_updated_by' => 346,
                        //'status_updated_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            if (!empty($insertData)) {
                ExpectedShipmentPenaltyAdjustment::insert($insertData);
            }
        });
    }
}
