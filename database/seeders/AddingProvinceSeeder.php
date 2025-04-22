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
        $provinces = [
            ['id' => 1, 'name' => 'Sindh'],
            ['id' => 2, 'name' => 'Punjab'],
            ['id' => 3, 'name' => 'Balochistan'],
            ['id' => 4, 'name' => 'Khyber Pakhtunkhwa'],
            ['id' => 5, 'name' => 'Azad Jammu And Kashmir'],
        ];
    
        foreach ($provinces as $province) {
            DB::table('provinces')->updateOrInsert(
                ['id' => $province['id']],
                [
                    'name' => $province['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
