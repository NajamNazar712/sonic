<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\FafChargesGlobal;
use App\FafCharges;
use App\Http\Models\Shipper\User;

class AddShippersIntoFafChargesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $percentage = FafChargesGlobal::value('faf_charges');

        FafCharges::query()->update([
            'percentage' => $percentage,
            'status' => 1,
            'updated_at' => now()
        ]);
        
        $existingShippers  = FafCharges::pluck('user_id');
        User::whereIn('status', [3, 4])
        ->whereNotIn('id', $existingShippers)
        ->select('id')
        ->chunk(1000, function ($shippers) use ($percentage) {
            $now = now();
            $data = [];

            foreach ($shippers as $shipper) {
                $data[] = [
                    'user_id' => $shipper->id,
                    'percentage' => $percentage,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            FafCharges::insert($data);
        });
        
    }
}
