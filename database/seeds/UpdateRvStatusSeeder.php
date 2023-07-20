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

        ShipmentStatus::create([
            'id'=> 65 ,'code' => 'R-SAR', 'name' => 'Shipment - Shipper Advise Requested','description'=> '', 'created_at' => Carbon::now(),'updated_at' => Carbon::now()
        ]);

        ShipmentStatus::where('id', 12)->update(['code' => 'R-VR', 'name' => 'Shipment - Reason Validation Required', 'updated_at' => Carbon::now()]);
    }
}
