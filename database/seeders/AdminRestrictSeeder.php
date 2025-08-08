<?php

namespace Database\Seeders;

use App\Http\Models\Admin\GlobalSettings;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminRestrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GlobalSettings::insert([
            'setting_value' => '1027',
            'type' => 'admin_restrict',
            'text' => 'For Shipper Cod and SSD visibility purpose',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
