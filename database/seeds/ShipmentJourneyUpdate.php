<?php

use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Database\Seeder;

class ShipmentJourneyUpdate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $rvAdminIds = RvShipmentAssignAgent::join('shipments_journey as sj', 'sj.shipment_id', '=', 'rv_shipment_assign_agents.shipment_id')
            ->whereIn('sj.shipper_status_id', [13, 20, 65])
            ->whereNull('sj.admin_id')
            ->where('rv_shipment_assign_agents.agent_id', 4620)
            ->limit(100)
            ->pluck('sj.id');
        
            // Update sj.admin_id for the retrieved IDs
        ShipmentsJourney::whereIn('id', $rvAdminIds)
        ->update(['admin_id' => 4620]);
    }
}
