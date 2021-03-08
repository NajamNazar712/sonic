<?php

use Illuminate\Database\Seeder;

class UpdateSealNumberForCargo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 453, 'name' => 'Update Seal Number For Master Cargo','module_id' => 3)
        ));
    }
}
