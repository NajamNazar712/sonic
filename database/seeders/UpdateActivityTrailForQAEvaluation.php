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

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('admins_screen_list')->insert(
             array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > CX Evaluation ', 'url'=>'admin.qa_evaluation.index', 'permission_id' => 648)
            );
        DB::table('admins_screen_list')->insert(
                array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > CX Evaluation > Add ', 'url'=>'admin.qa_evaluation.add', 'permission_id' => 654)
               );
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Quality Assurance > CX Evaluation Activities', 'url'=>'admin.qa_evaluation.edit_activities', 'permission_id' => 656)
               );
    }
}
