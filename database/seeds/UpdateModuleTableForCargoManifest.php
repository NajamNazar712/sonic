<?php

use Illuminate\Database\Seeder;

class UpdateModuleTableForCargoManifest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 32, 'name' => 'Cargo Manifest')
        ));
    }
}
