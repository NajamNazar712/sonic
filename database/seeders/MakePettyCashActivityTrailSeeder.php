<?php

use Illuminate\Database\Seeder;

class MakePettyCashActivityTrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 457, 'screen_name' => 'Make Petty Cash', 'action'=> 'View'),
        ));
    }
}
