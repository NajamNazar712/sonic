<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPaymentCycleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 365, 'name' => 'Payment Cycle - Update', 'module_id' => 2)
        ));
    }
}
