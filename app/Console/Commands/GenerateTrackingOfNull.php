<?php

namespace App\Console\Commands;

use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTrackingOfNull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:empty_tracking';

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

        $shipments = Shipment::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
            ->whereNull('tracking_number')->get();

        foreach ($shipments as $ship) {
            $shipment = Shipment::find($ship->id);
            $shipment_id = $shipment->id;
            $pickup_city_id = $shipment->pickup_address->id;
            $consignee_city_id = $shipment->consignee_city_id;
            $controller = new ShipperShipmentBookController();

            $tracking_number = $controller->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
            $shipment->tracking_number = $tracking_number;
            $shipment->save();

        }

        return Command::SUCCESS;
    }
}
