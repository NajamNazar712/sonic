<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableMultiplePaymentReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 176, 'name' => 'Multiple Payment', 'module_id' => 9)
        ));
    }
}
