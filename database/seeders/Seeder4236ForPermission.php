<?php

use Illuminate\Database\Seeder;

class Seeder4236ForPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 620, 'name' => 'Rejoin Employee', 'module_id' => 28)
        ));
    }
}
