<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableSeederForCRM extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 18, 'name' => 'CRM')
        ));
    }
}
