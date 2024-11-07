<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommissionDashboardUserWise extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 333, 'name' => 'Commission Dashboard User Wise', 'module_id' => 21)
        ));

    }
}
