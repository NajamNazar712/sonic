<?php

use Illuminate\Database\Seeder;

class Seeder4043ForPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 604, 'name' => 'Reconcile To Deposit SDN Status', 'module_id' => 8),
            array('id' => 605, 'name' => 'Add/Remove DNCC From SDN', 'module_id' => 8),
        ));
    }
}
