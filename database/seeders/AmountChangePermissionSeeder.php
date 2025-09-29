<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AmountChangePermission;

class AmountChangePermissionSeeder extends Seeder
{
    public function run(): void
    {
        AmountChangePermission::create([
            'user_id' => 11560
        ]);
    }
}
