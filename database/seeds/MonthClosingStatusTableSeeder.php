<?php

use Illuminate\Database\Seeder;

class MonthClosingStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('month_closing_statuses')->truncate();

        DB::table('month_closing_statuses')->insert(array(
            array('id' => 1, 'name' => 'Pending'),
            array('id' => 2, 'name' => 'Resolved'),
            array('id' => 3, 'name' => 'Closed')
        ));
    }
}
