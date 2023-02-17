<?php

use Illuminate\Database\Seeder;

class DailyVisitRatingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('daily_visit_ratings')->truncate();

        DB::table('daily_visit_ratings')->insert(array(
            array('id' => 1, 'name' => 'Bad', 'code' => '⭐'),
            array('id' => 2, 'name' => 'Poor', 'code' => '⭐⭐'),
            array('id' => 3, 'name' => 'Average', 'code' => '⭐⭐⭐'),
            array('id' => 4, 'name' => 'Good', 'code' => '⭐⭐⭐⭐'),
            array('id' => 5, 'name' => 'Excellent', 'code' => '⭐⭐⭐⭐⭐')
        ));
    }
}
