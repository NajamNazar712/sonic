<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressMissingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('address_missing_shipment_type')->insert([
            ['type_name' => 'House/Flat number', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Floor number', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Street/Block #', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Area Name', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Nearest landmark', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
