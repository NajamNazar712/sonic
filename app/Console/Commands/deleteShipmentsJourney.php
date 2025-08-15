<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class deleteShipmentsJourney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:delete_journey';

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
        $filePath = storage_path('app/arrival.xlsx');

        // Load Excel file
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        // Remove header row and flatten to just tracking numbers
        $trackingNumbers = array_filter(array_column(array_slice($rows, 1), 0));
        
        // Process in chunks of 500 to avoid memory or timeout issues
        $chunks = array_chunk($trackingNumbers, 500);
        
        foreach ($chunks as $chunk) {
        
            DB::transaction(function () use ($chunk) {
        
                // 1. Insert into shipments_journey_remove
                DB::table('shipments_journey_remove')->insertUsing(
                    [
                        'id',
                        'created_at',
                        'updated_at',
                        'shipment_id',
                        'shipper_status_id',
                        'consignee_status_id',
                        'status_reason_id',
                        'remarks',
                        'user_id',
                        'admin_id',
                        'rider_id',
                        'city_id',
                        'reference_1_id',
                        'reference_2_id',
                        'received_or_refused_by',
                        'verification',
                        'ip_address',
                        'relation',
                        'cnic'
                    ],
                    DB::table('shipments_journey')
                        ->select([
                            'id',
                            'created_at',
                            'updated_at',
                            'shipment_id',
                            'shipper_status_id',
                            'consignee_status_id',
                            'status_reason_id',
                            'remarks',
                            'user_id',
                            'admin_id',
                            'rider_id',
                            'city_id',
                            'reference_1_id',
                            'reference_2_id',
                            'received_or_refused_by',
                            'verification',
                            'ip_address',
                            'relation',
                            'cnic'
                        ])
                        ->where('shipper_status_id', 17)
                        ->whereIn('shipment_id', function ($query) use ($chunk) {
                            $query->select('id')
                                ->from('shipments')
                                ->whereIn('tracking_number', $chunk);
                        })
                );
        
                // 2. Delete from shipments_journey
                DB::table('shipments_journey')
                    ->where('shipper_status_id', 17)
                    ->whereIn('shipment_id', function ($query) use ($chunk) {
                        $query->select('id')
                            ->from('shipments')
                            ->whereIn('tracking_number', $chunk);
                    })
                    ->delete();
        
                // 3. Update shipments.status_id to latest journey status
                DB::table('shipments')
                    ->join(DB::raw('(
                        SELECT shipment_id, shipper_status_id,consignee_status_id
                        FROM shipments_journey
                        WHERE id IN (
                            SELECT MAX(id) 
                            FROM shipments_journey 
                            GROUP BY shipment_id
                        )
                    ) as latest'), 'shipments.id', '=', 'latest.shipment_id')
                    ->whereIn('shipments.tracking_number', $chunk)
                    ->update([
                    'shipments.shipper_status_id' => DB::raw('latest.shipper_status_id'),
                    'shipments.consignee_status_id' => DB::raw('latest.consignee_status_id')
                    ]);
            });
        }
    }
}
