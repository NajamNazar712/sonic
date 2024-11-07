<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetailCommissionMarkPaymentAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 995, 'name' => 'Retail Commission - View', 'module_id' => 8),
            array('id' => 996, 'name' => 'Retail Commission - View', 'module_id' => 26),
        ));
    }
}
