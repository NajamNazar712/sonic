<?php

use Illuminate\Database\Seeder;

class StandardReturnChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_return_charges')->insert(array(
            array('shipping_mode_id'=>1,'local'=>50,'national'=>100),
            array('shipping_mode_id'=>2,'local'=>50,'national'=>100),
            array('shipping_mode_id'=>3,'local'=>50,'national'=>100),
            array('shipping_mode_id'=>4,'local'=>50,'national'=>100),

        ));
    }
}
