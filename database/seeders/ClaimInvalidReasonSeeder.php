<?php

namespace Database\Seeders;

use App\Models\ClaimInvalidReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClaimInvalidReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $fillable = ['reason'];

    public function run()
    {
        $reasons = [
            'Time Barred Case',
            'Packaging issue',
            'Prohibited Item',
            'Perishable Goods',
            'Safe & Sound Delivery',
            'Excluded Liability',
        ];

        foreach ($reasons as $reason) {
            ClaimInvalidReason::create(['reason' => $reason]);
        }
    }
}
