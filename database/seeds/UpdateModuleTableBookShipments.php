<?php

use Illuminate\Database\Seeder;

class UpdateModuleTableBookShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('modules')->insert(array(
            array('id' => 17, 'name' => 'Book Shipments')
        ));
    }
}
