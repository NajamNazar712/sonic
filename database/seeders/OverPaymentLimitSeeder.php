<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OverPaymentLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 362, 'name' => 'Over Payment Limit', 'module_id' => 14)
        ));
    }
}
