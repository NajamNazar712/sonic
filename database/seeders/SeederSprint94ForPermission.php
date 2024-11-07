<?php

use Illuminate\Database\Seeder;

class SeederSprint94ForPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 712, 'name' => 'Update Line Manager', 'module_id' => 28),
            array('id' => 713, 'name' => 'Reopen FNF', 'module_id' => 28),
        ));
    }
}
