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
        DB::table('parent_products')->insert([
            [
                'id' => 1,
                'name' => 'On supply of electronic goods',
                'tax_percentage' => 0.25
                
            ],
            [
                'id' => 2,
                'name' =>'On supply of clothing articles, apparels, garments etc',
                'tax_percentage' => 2
            ],
            [
                'id' => 3,
                'name' => 'On supply of goods other than mentioned in S. No.  1 and 2 above',
                'tax_percentage' => 1
            ]
        ]);
    }
}
