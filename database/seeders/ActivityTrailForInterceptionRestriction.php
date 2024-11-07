<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivityTrailForInterceptionRestriction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 755, 'screen_name' => 'Interception Restriction', 'action'=> 'View'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 946, 'name' => 'Interception Restriction Setting - View', 'module_id' => 14)
        ));
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
    }
}
