<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddingProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('provinces')->insert([
            ['id' => 1, 'name' => 'Sindh', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Punjab', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Balochistan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Khyber Pakhtunkhwa', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
