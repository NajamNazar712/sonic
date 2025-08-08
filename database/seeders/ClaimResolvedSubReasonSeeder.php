<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClaimResolvedSubReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClaimResolvedSubReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subReasons = [
            'Recoverable amount',
            'Non-Recoverable amount',
            'Partially recoverable',
        ];

        foreach ($subReasons as $reason) {
            ClaimResolvedSubReason::create(['name' => $reason]);
        }
    }
}
