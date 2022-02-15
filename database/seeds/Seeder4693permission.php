<?php

use Illuminate\Database\Seeder;

class Seeder4693permission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 677, 'name' => 'Outstanding SDN - Edit Deposit Slip', 'module_id' => 8),
        ));
    }
}
