<?php

use Illuminate\Database\Seeder;

class CommissionDashboardOverall extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 334, 'name' => 'Commission Dashboard Overall', 'module_id' => 21)
        ));

    }
}
