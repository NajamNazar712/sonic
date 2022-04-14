<?php

use Illuminate\Database\Seeder;

class UpdateLeadStatusForUpdate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_statuses')->insert(array(
            array('id' => 15, 'name' => 'Lead Updated')
        ));
    }
}
