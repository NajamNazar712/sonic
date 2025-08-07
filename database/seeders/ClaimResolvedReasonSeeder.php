<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClaimResolvedReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClaimResolvedReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reasons = [
            'As per Policy',
            'Beyond Policy',
        ];

        foreach ($reasons as $reason) {
            ClaimResolvedReason::create(['name' => $reason]);
        }
    }
}
