<?php

use Illuminate\Database\Seeder;

class RiderActivateIncentiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
        array('id' => 690, 'name' => 'Incentive Rider - Enable', 'module_id' => 12)
        ));
    }
}
