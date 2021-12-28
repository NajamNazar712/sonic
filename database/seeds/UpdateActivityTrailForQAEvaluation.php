<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForQAEvaluation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 486, 'screen_name' => 'QA Evaluation - Report', 'action'=> 'View'),
            array('id' => 487, 'screen_name' => 'QA Evaluation - Report', 'action'=> 'Excel Download'),
        ));
    }
}
