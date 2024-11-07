<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModuleIdForCommission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 21, 'name' => 'Commission')
        ));
    }
}
