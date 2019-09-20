<?php

use Illuminate\Database\Seeder;

class DailyVisitLeadStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('daily_visit_lead_statuses')->truncate();

        DB::table('daily_visit_lead_statuses')->insert(array(
            array('id' => 1, 'name' => 'Existing Schedule Visit'),
            array('id' => 2, 'name' => 'Existing Performance review Visit'),
            array('id' => 3, 'name' => 'Existing Hurdle Visit'),
            array('id' => 4, 'name' => 'New Lead Visit'),
            array('id' => 5, 'name' => 'New Lead Follow Up Visit'),
            array('id' => 6, 'name' => 'Loss Business Retention Visit'),
        ));
    }
}
