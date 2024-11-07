<?php

use Illuminate\Database\Seeder;

class RiderDeactivateIncentiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 691, 'name' => 'Incentive Rider - Disable', 'module_id' => 12)
        ));
    }
}
