<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FnfSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('fnf_sections')->truncate();

        DB::table('fnf_sections')->insert(array(
            array('id' => 1, 'name' => 'Reporting Manager'),
            array('id' => 2, 'name' => 'Customer Experience'),
            array('id' => 3, 'name' => 'Administration'),
            array('id' => 4, 'name' => 'It Support'),
            array('id' => 5, 'name' => 'Finance'),
            array('id' => 6, 'name' => 'HOD'),
            array('id' => 7, 'name' => 'HR'),
        ));
    }
}
