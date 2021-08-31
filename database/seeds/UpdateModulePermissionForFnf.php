<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForFnf extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 568, 'name' => 'FNF - View', 'module_id' => 28),
            array('id' => 569, 'name' => 'FNF - Add ', 'module_id' => 28),
            array('id' => 570, 'name' => 'FNF Reporting Manager - View ', 'module_id' => 28),
            array('id' => 571, 'name' => 'FNF Customer Experience - View ', 'module_id' => 28),
            array('id' => 572, 'name' => 'FNF Administration - View ', 'module_id' => 28),
            array('id' => 573, 'name' => 'FNF IT Support - View ', 'module_id' => 28),
            array('id' => 574, 'name' => 'FNF Finance - View ', 'module_id' => 28),
            array('id' => 575, 'name' => 'FNF HOD - View ', 'module_id' => 28),
            array('id' => 576, 'name' => 'FNF HR - View ', 'module_id' => 28),
            array('id' => 577, 'name' => 'FNF Edit - View ', 'module_id' => 28),
            array('id' => 578, 'name' => 'FNF History - View ', 'module_id' => 28),
        ));
    }
}
