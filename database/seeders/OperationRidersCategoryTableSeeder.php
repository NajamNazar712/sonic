<?php

use Illuminate\Database\Seeder;

class OperationRidersCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('operation_riders_categories')->insert(array(
            array('id' => 1, 'name' => 'Field Operations'),
            array('id' => 2, 'name' => 'Hold In Operations'),
        ));
    }
}
