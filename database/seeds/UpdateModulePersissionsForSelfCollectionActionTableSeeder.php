<?php

use Illuminate\Database\Seeder;

class UpdateModulePersissionsForSelfCollectionActionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 211, 'name' => 'Mark for Self Collection Action', 'module_id' => 7)
        ));
    }
}
