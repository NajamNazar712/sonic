<?php

use Illuminate\Database\Seeder;

class CmsCaseNatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cms_case_nature')->truncate();

        DB::table('cms_case_nature')->insert(array(
            array('id' => 1, 'name' => 'Complaints'),
            array('id' => 2, 'name' => 'Service Request'),
            array('id' => 3, 'name' => 'Feedback')
        ));
    }
}
