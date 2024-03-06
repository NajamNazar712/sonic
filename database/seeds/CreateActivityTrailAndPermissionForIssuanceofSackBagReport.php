<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateActivityTrailAndPermissionForIssuanceofSackBagReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 937, 'name' => 'Issuance of Canvas Bag Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 745, 'screen_name' => 'Issuance Canvas Bag', 'action' => 'View'),
            array('id' => 746, 'screen_name' => 'Issuance Canvas Bag', 'action' => 'Excel'),

        ));
    }
}
