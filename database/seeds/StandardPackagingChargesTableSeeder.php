<?php

use Illuminate\Database\Seeder;

class StandardPackagingChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_packaging_charges')->insert(array(
            array('shipping_mode_id'=>1,'sm_flyer'=>10,'md_flyer'=>15,'lg_flyer'=>20,'box_flyer'=>20),
            array('shipping_mode_id'=>2,'sm_flyer'=>10,'md_flyer'=>15,'lg_flyer'=>20,'box_flyer'=>20),
            array('shipping_mode_id'=>3,'sm_flyer'=>10,'md_flyer'=>15,'lg_flyer'=>20,'box_flyer'=>20),
            array('shipping_mode_id'=>4,'sm_flyer'=>10,'md_flyer'=>15,'lg_flyer'=>20,'box_flyer'=>20),
        ));
    }
}
