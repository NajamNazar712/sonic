<?php

namespace App\Console\Commands;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Console\Command;

class shipmentInTransitjourney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journey:insertIntoTransit {tracking_number?}';

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
        $trackingNumbers = $this->argument('tracking_number')
            ? explode(',', $this->argument('tracking_number'))
            : [];

        if (!empty($trackingNumbers)) {
            $shipments = Shipment::whereIn('tracking_number', $trackingNumbers)->get();

            if ($shipments->isNotEmpty()) {
                echo "Found {$shipments->count()} shipment(s)." . PHP_EOL;

                foreach ($shipments as $shipment) {
                    $cargo = CargoManifestBagShipments::where('shipment_id', $shipment->id)->first();

                    if (!$cargo || !$cargo->bag) {
                        echo "⚠️ Skipped shipment ID {$shipment->id}, cargo/bag missing." . PHP_EOL;
                        continue;
                    }

                    // Update shipment
                    $shipment->update([
                        'shipper_status_id'   => 3,
                        'consignee_status_id' => 3,
                    ]);

                    // Create journey
                    ShipmentsJourney::create([
                        'shipment_id'         => $shipment->id,
                        'verification'        => 1,
                        'created_at'          => $cargo->bag->updated_at,
                        'updated_at'          => $cargo->bag->updated_at,
                        'shipper_status_id'   => 3,
                        'consignee_status_id' => 3,
                        'status_reason_id'    => null,
                        'city_id'             => $cargo->bag->origin_hub_id,
                        'remarks'             => null,
                        'user_id'             => null,
                        'admin_id'            => 346,
                        'rider_id'            => null,
                        'reference_1_id'      => $cargo->bag->id,
                        'reference_2_id'      => null,
                        'received_or_refused_by' => null,
                        'relation'            => null,
                        'cnic'                => null,
                    ]);

                    echo "✅ Shipment ID {$shipment->id} updated and journey created." . PHP_EOL;
                }
            } else {
                echo "❌ No shipments found for provided tracking numbers." . PHP_EOL;
            }
        } else {
            echo "❌ No tracking number(s) provided. Please pass at least one." . PHP_EOL;
        }
    }
}
