<?php

use Illuminate\Database\Seeder;

class UpdateMonthClosingStatusForClosedReattemptedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('month_closing_statuses')->insert(array(
            array('id' => 25, 'name' => 'Closed - Re-Attempted')
        ));
    }
}
