<?php

use App\Http\Models\Admin\GlobalSettings;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FetchDataForRVShipmentTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::connection('reports_2')->table('shipments')
        ->leftJoin('rv_agent_call_histories', 'shipments.id', '=', 'rv_agent_call_histories.shipment_id')
        ->leftJoin('shipments_journey', function ($join) {
            $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
            ->where('shipments_journey.id','=',
            DB::connection('reports_2')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
        })
        ->whereDate('shipments_journey.created_at', '=', Carbon::today())
        ->whereIn('shipments.shipper_status_id', [12,66])
        ->groupBy('shipments.id', 'shipments.shipper_status_id', 'shipments_journey.status_reason_id', 'shipments.user_id', 'shipments.updated_at')
        ->select(
            'shipments.id', 
            'shipments.shipper_status_id', 
            'shipments_journey.status_reason_id',
            'shipments.user_id', 
            'shipments.updated_at',
            DB::raw('CASE
                    WHEN COUNT(rv_agent_call_histories.id) = 0 THEN 0 
                    WHEN COUNT(rv_agent_call_histories.id) = 1 THEN 1 
                    ELSE 2 
                END AS call_count'
            )
        )
        ->orderBy('shipments.id')
        ->chunk(1000, function($shipments){

            $transformedData = [];

            foreach ($shipments as $shipment) {

                $transformedData[] = [
                    'shipment_id' => $shipment->id,
                    'shipment_shipper_status_id' => $shipment->shipper_status_id,
                    'shipment_status_reason_id' => $shipment->status_reason_id,
                    'shipment_user_id' => $shipment->user_id,
                    'call_count' => $shipment->call_count,
                    'in_progress' => 0,
                    'is_completed' => 0,
                    'disabled_shipper' => 0,
                    'created_at' => $shipment->updated_at,
                    'updated_at' => $shipment->updated_at
                ];

            }

             // Insert transformed data in chunks
             if (!empty($transformedData)) {
                DB::table('rv_shipment_tickets')->insert($transformedData);
            }

        });

        //Update All Shipments to disabled_shipper = 1 based on global_settings whose shippers are disabled
        $globalSettings = GlobalSettings::where('type','rv_disable_shippers_only_shippers')
        ->get(['text'])->first();
        
        if($globalSettings)
        {
            $shipperIds = explode(',', $globalSettings->text);
            DB::table('rv_shipment_tickets')
            ->whereIn('shipment_user_id', $shipperIds)
            ->update(['disabled_shipper' => 1]);
        }

      
        
    }
}
