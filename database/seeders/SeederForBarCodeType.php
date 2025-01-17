<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeederForBarCodeType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('barcode_types')->truncate();
        DB::table('barcode_types')->insert(array(
            array('id' => 1,'barcode_name' => 'Canvas Bag'),
        ));
    }
}
