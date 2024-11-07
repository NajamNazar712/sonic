<?php

use App\Http\Models\ShipmentStatusReason;
use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonAudioBitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reasonIds = [
            14, 23, 25, 1, 3, 4, 6, 28, 60, 63, 17, 18, 5, 7, 8, 12, 19, 27, 34, 35, 40, 45, 31, 32, 33, 77
        ];

        foreach($reasonIds as $reasonId){
            ShipmentStatusReason::where('id', $reasonId)->update(['audio'=> 1]);
        }
    }
}
