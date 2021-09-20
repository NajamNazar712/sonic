<?php

use Illuminate\Database\Seeder;

class GenerateMenuallReprotForDonePayment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
        public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 597, 'name' => 'Generate Manual Report For Done Payent', 'module_id' => 8),
        ));
    }
}
