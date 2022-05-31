<?php

use Illuminate\Database\Seeder;

class SeederDesHotfixPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 718, 'name' => 'Designation Add Hub - Bulk', 'module_id' => 28),
        ));
    }
}
