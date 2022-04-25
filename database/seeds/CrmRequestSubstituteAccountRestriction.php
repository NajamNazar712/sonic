<?php

use Illuminate\Database\Seeder;

class CrmRequestSubstituteAccountRestriction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('substitute_user_module_permissions')->insert(array(
            array('id' => 16, 'name' => 'Complaints Request'),
            array('id' => 17, 'name' => 'Service Request'),
            array('id' => 18, 'name' => 'Claims & Feedback'),
        ));
    }
}
