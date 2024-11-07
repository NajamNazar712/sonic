<?php

use Illuminate\Database\Seeder;

class MonthClosingTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('month_closing_types')->truncate();

        DB::table('month_closing_types')->insert(array(
            array('id' => 1, 'name' => 'Operation Closing'),
            array('id' => 2, 'name' => 'Claims'),
            array('id' => 3, 'name' => 'Fake Status')
        ));
    }
}
