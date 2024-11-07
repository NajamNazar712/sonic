<?php

use Illuminate\Database\Seeder;

class UpdatePermissionTableNonServiceArea extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('module_permissions')->insert(array(
            array('id' => 150, 'name' => 'Non Service Area', 'module_id' => 14)
        ));
    }
}
