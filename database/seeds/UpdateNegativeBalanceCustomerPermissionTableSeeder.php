<?php

use Illuminate\Database\Seeder;

class UpdateNegativeBalanceCustomerPermissionTableSeeder extends Seeder
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
            array('id' => 148, 'name' => 'Negative Balance Customers', 'module_id' => 9),
        ));
    }
}
