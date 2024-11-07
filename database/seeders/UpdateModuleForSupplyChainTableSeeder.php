<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModuleForSupplyChainTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 24, 'name' => 'Supply Chain')
        ));
    }
}
