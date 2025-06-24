<?php

namespace Database\Seeders;

use App\Models\CityEtd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CityEtdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
         $data = [
            ['from' => 2, 'to' => 2, 'range' => '1-2', 'label' => '1 - 2 Working Days'],
            ['from' => 4, 'to' => 4, 'range' => '1-2', 'label' => '1 - 2 Working Days'],
            ['from' => 6, 'to' => 6, 'range' => '1-2', 'label' => '1 - 2 Working Days'],
            ['from' => 2, 'to' => 1, 'range' => '4-5', 'label' => '4 - 5 Working Days'],
            ['from' => 1, 'to' => 2, 'range' => '4-5', 'label' => '4 - 5 Working Days'],
            ['from' => 5, 'to' => 2, 'range' => '4-5', 'label' => '4 - 5 Working Days'],
            ['from' => 2, 'to' => 5, 'range' => '4-5', 'label' => '4 - 5 Working Days'],
            ['from' => 1, 'to' => 5, 'range' => '4-5', 'label' => '4 - 5 Working Days'],
            ['from' => 5, 'to' => 1, 'range' => '4-5', 'label' => '4 - 5 Working Days'],
            ['from' => 5, 'to' => 5, 'range' => '1-3', 'label' => '1 - 3 Working Days'],
            ['from' => 2, 'to' => 2, 'range' => '1-3', 'label' => '1 - 3 Working Days'],
            ['from' => 1, 'to' => 1, 'range' => '1-3', 'label' => '1 - 3 Working Days'],
            ['from' => 2, 'to' => 4, 'range' => '1-2', 'label' => '1 - 2 Working Days'],
            ['from' => 4, 'to' => 2, 'range' => '1-2', 'label' => '1 - 2 Working Days'],
            ['from' => 6, 'to' => 2, 'range' => '2-3', 'label' => '2 - 3 Working Days'],
            ['from' => 2, 'to' => 6, 'range' => '2-3', 'label' => '2 - 3 Working Days'],
            ['from' => 6, 'to' => 4, 'range' => '2-3', 'label' => '2 - 3 Working Days'],
            ['from' => 4, 'to' => 6, 'range' => '2-3', 'label' => '2 - 3 Working Days'],
            ['from' => 1, 'to' => 4, 'range' => '2-3', 'label' => '2 - 3 Working Days'],
            ['from' => 4, 'to' => 1, 'range' => '2-3', 'label' => '2 - 3 Working Days'],
            ['from' => 5, 'to' => 4, 'range' => '2-3', 'label' => '2 - 3 Working Days'],
            ['from' => 1, 'to' => 6, 'range' => '4-5', 'label' => '4 - 5 Working Days'],
        ];

        foreach ($data as $item) {
            CityEtd::create([
                'from_etd_city_id' => $item['from'],
                'to_etd_city_id' => $item['to'],
                'range' => $item['range'],
                'label' => $item['label'],
            ]);
        }
    }
}
