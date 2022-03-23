<?php

use Illuminate\Database\Seeder;

class AirwayPrintRightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 699, 'name' => 'Airway Bill Print Rights','module_id' => 15),
        ));
    }
}
