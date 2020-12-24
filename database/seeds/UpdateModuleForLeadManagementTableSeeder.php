<?php

use Illuminate\Database\Seeder;

class UpdateModuleForLeadManagementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 25, 'name' => 'Lead Management')
        ));
    }
}
