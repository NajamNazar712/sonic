<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LogisticModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 34, 'name' => 'Logistic' )
        ));
    }
}
