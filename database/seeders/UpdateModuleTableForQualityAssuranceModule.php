<?php

use Illuminate\Database\Seeder;

class UpdateModuleTableForQualityAssuranceModule extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 31, 'name' => 'Quality Assurance'),
        ));
    }
}
