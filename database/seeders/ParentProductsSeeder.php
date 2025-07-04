<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParentProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('parent_products')->truncate();
        DB::table('parent_products')->insert([
            [
                'id' => 1,
                'name' => 'Electronic Goods',
                'tax_percentage' => 2,
                'sst_percentage' => 2
                
            ],
            [
                'id' => 2,
                'name' =>'Clothing Articles / Apparel / Garments',
                'tax_percentage' => 2,
                'sst_percentage' => 2
            ],
            [
                'id' => 3,
                'name' => 'Other',
                'tax_percentage' => 2,
                'sst_percentage' => 2
            ]
        ]);
    }
}
