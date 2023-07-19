<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ShipmentStatus;

class UpdateRvStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rv_assign_agent_statuses')->insert(array(
            array('shipment_status_id' => 64, 'name' => 'Shipper Advised Requested', 'call_finding_id' => NULL, 'is_active' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'is_visible' => 1),
        ));

        ShipmentStatus::where('id', 12)->update(['code' => 'R-VR', 'name' => 'Shipment - Reason Validation Required', 'updated_at' => Carbon::now()]);
    }
}
