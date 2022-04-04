<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReferralModule extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 701, 'name' => 'Referral Module', 'module_id' => 14)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 520, 'screen_name' => 'Referral Module', 'action'=> 'View'),
        ));
    }
}
