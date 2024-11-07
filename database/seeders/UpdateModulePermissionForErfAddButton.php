<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForErfAddButton extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 522, 'name' => 'ERF Request - Add', 'module_id' => 28),
        ));
    }
}
