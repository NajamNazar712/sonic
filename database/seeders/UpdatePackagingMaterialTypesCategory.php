<?php

use Illuminate\Database\Seeder;

class UpdatePackagingMaterialTypesCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('packaging_material_types')->where('category',0)->update([
            'category' => 1,
        ]);
        //
    }
}
