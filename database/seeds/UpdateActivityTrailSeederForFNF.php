<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailSeederForFNF extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 419, 'screen_name' => 'FNF Dashboard', 'action'=> 'View'),
            array('id' => 420, 'screen_name' => 'FNF Dashboard', 'action'=> 'Excel Download'),
            array('id' => 421, 'screen_name' => 'FNF Add Request', 'action'=> 'View'),
            array('id' => 422, 'screen_name' => 'FNF Reporting Manager', 'action'=> 'View'),
            array('id' => 423, 'screen_name' => 'FNF Customer Experience', 'action'=> 'View'),
            array('id' => 424, 'screen_name' => 'FNF Administration', 'action'=> 'View'),
            array('id' => 425, 'screen_name' => 'FNF IT Support', 'action'=> 'View'),
            array('id' => 426, 'screen_name' => 'FNF Finance', 'action'=> 'View'),
            array('id' => 427, 'screen_name' => 'FNF HOD', 'action'=> 'View'),
            array('id' => 428, 'screen_name' => 'FNF HR', 'action'=> 'View'),
            array('id' => 429, 'screen_name' => 'FNF Edit', 'action'=> 'View'),
            array('id' => 430, 'screen_name' => 'FNF Status History', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > FNF', 'url'=>'admin.human_resource.fnf.index', 'permission_id' => 568),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > FNF > Add', 'url'=>'admin.human_resource.fnf.add', 'permission_id' => 569)
        );
    }
}
