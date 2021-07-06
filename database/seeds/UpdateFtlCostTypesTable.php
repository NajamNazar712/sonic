<?php

use Illuminate\Database\Seeder;

class UpdateFtlCostTypesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('ftl_cost_types')->insert(array(
            array('id' => 1, 'name' => 'Detention'),
            array('id' => 2, 'name' => 'Extra Fuel')
        ));
    }
}
