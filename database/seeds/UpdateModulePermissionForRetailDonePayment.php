<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRetailDonePayment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 657, 'name' => 'Retail Done Payments Email Generate - Manual', 'module_id' => 26),
        ));
    }
}
