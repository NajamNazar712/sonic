<?php

use Illuminate\Database\Seeder;

class ReferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('references')->truncate();
        DB::table('references')->insert(array(
            array('name'=>'Sales person'),
            array('name'=>'Facebook'),
            array('name'=>'LinkedIn'),
            array('name'=>'Instagram'),
            array('name'=>'Trax.pk website'),
            array('name'=>'Friends'),
            array('name'=>'Word of mouth'),

        ));
    }
}
