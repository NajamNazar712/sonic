<?php

use Illuminate\Database\Seeder;

class PackagingMaterialStockHeadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('packaging_material_stock_heads')->truncate();
        DB::table('packaging_material_stock_heads')->insert(array(
            array('id'=>1,'small_flyers'=>0,'medium_flyers'=>0,'large_flyers'=>0,'boxes'=>0),
        ));
    }
}
