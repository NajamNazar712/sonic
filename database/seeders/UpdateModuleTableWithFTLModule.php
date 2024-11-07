<?php

use Illuminate\Database\Seeder;

class UpdateModuleTableWithFTLModule extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' =>30, 'name' => 'Full Truck Load (FTL)'),
        ));
    }
}
