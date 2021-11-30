<?php

use Illuminate\Database\Seeder;

class Seeder4288PettyCashPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 641, 'name' => 'Edit Petty Cash Statement - Edit Option For Finance', 'module_id' => 8)
        ));
    }
}
