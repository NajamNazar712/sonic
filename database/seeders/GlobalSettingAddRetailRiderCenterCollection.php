<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalSettingAddRetailRiderCenterCollection extends Seeder
{
    /**
     * Run the database seeds.
     *Rider
     * @return void
     */
    public function run()
    {
        $timestamp = now();

        // 1 Insert rider and get its auto ID
        $riderId = DB::table('riders')->insertGetId([
            'city_id' => 202,
            'name' => 'Retail Center Collection',
            'phone' => '0000-0000000',
            'cnic' => '00000-0000000-0',
            'address' => 'Retail Trax Center',
            'rider_main_category_id' => 1,
            'route_id' => null,
            'rider_category_id' => 1,
            'status' => 0,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
            'pin' => '$2y$10$lghTbTnMYhiDaxzEAATHlOnA6rBHVN010T5dz6dJg8rzeVky2Rn7W',
            'api_token' => null,
            'special_rider' => 0,
            'trax_id' => null,
            'rider_type_id' => 1,
            'blacklist' => 0,
            'dummy_pin' => '5412',
            'operation_rider_id' => 1,
            'allow_delivered_status' => null,
            'employee_id' => null,
            'ccd' => 0
        ]);

        // 2️⃣ Add setting with new rider id
        DB::table('global_settings')->insert([
            [
                'type' => 'rider_center_collection',
                'setting_value' => $riderId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]
        ]);
    }
}
