<?php

use Illuminate\Database\Seeder;

class ModulePErmissionForCallerAgentScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 497, 'name' => 'Caller Agent Screen - View', 'module_id' => 29),
        ));
    }
}
