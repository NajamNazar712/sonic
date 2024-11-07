<?php

use Illuminate\Database\Seeder;

class UpdateLeadReasonsForDormantStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_status_reasons')->insert(array(
            array('id' => 16, 'status_id' => 14,'reason_id' => 8),
            array('id' => 17, 'status_id' => 14,'reason_id' => 9),
            array('id' => 18, 'status_id' => 14,'reason_id' => 11),
            array('id' => 19, 'status_id' => 14,'reason_id' => 12),
            array('id' => 20, 'status_id' => 14,'reason_id' => 13),
        ));
    }
}
