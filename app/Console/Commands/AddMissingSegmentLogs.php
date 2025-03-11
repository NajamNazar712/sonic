<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddMissingSegmentLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:shipper_segment_logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add segment logs for missing booked shipments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 10 minutes ago
        $startTime = now()->subMinutes(10)->format('Y-m-d H:i:s');
        // Current time
        $endTime = now()->format('Y-m-d H:i:s');

        $shipments = DB::table('shipments')
        ->leftJoin('users', 'users.id', '=', 'shipments.user_id')
        ->leftJoin('user_shipping_infos', 'user_shipping_infos.id', '=', 'shipments.pickup_address_id')
        ->leftJoin('shipper_segment_logs', 'shipper_segment_logs.shipment_id', '=', 'shipments.id')

        // Get shipments where shipper_segment_logs is missing OR segment_id & sub_segment_id are 0
        ->where(function ($query) {
            $query->whereNull('shipper_segment_logs.shipment_id')
                ->orWhere(function ($subQuery) {
                    $subQuery->where('shipper_segment_logs.segment_id', 0)
                        ->where('shipper_segment_logs.sub_segment_id', 0);
                });
        })

        // Only select shipments created from 21st December 2024 and onwards
        ->whereDate('shipments.created_at', '>=', '2024-12-21')

        // Only select shipments in the time interval
        ->whereBetween('shipments.created_at', [$startTime, $endTime])
        ->select(
            'shipments.id as shipment_id',
            'user_shipping_infos.city_id',
            'shipments.consignee_city_id',
            'users.segment_id',
            'users.sub_segment_id',
            'shipper_segment_logs.segment_id as log_segment_id',
            'shipper_segment_logs.sub_segment_id as log_sub_segment_id'
        )
        ->get();

        // Filter shipments where city_id and consignee_city_id are different
        $filteredShipments = $shipments->filter(function ($shipment) {
            return $shipment->city_id != $shipment->consignee_city_id;
        });

        // Prepare logs to insert
        $logsToInsert = [];
        foreach ($filteredShipments as $shipment) {
            // If shipper_segment_logs is missing, prepare for insertion
            if (is_null($shipment->log_segment_id) && is_null($shipment->log_sub_segment_id)) {
                $logsToInsert[] = [
                    'shipment_id' => $shipment->shipment_id,
                    'segment_id' => $shipment->segment_id,
                    'sub_segment_id' => $shipment->sub_segment_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // If shipper_segment_logs exists but has segment_id and sub_segment_id as 0, update it
            elseif ($shipment->log_segment_id == 0 && $shipment->log_sub_segment_id == 0) {
                DB::table('shipper_segment_logs')
                    ->where('shipment_id', $shipment->shipment_id)
                    ->update([
                        'segment_id' => $shipment->segment_id,
                        'sub_segment_id' => $shipment->sub_segment_id,
                        'updated_at' => now(),
                    ]);
            }
        }

        if (!empty($logsToInsert)) {
            DB::table('shipper_segment_logs')->insert($logsToInsert);
        }

        return 0;  
    }
}
