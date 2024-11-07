<?php

use Illuminate\Database\Seeder;

class UpdateLeadReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_reasons')->insert(array(
            array('id' => 1, 'name' => 'Prohibited Items'),
            array('id' => 2, 'name' => 'Wrong Contact Details'),
            array('id' => 3, 'name' => 'Duplicate'),
            array('id' => 4, 'name' => 'A/C Query Call'),
            array('id' => 5, 'name' => 'Operational Query'),
            array('id' => 6, 'name' => 'HR Query'),
            array('id' => 7, 'name' => 'Sales Person Already Assigned'),
            array('id' => 10, 'name' => 'Others'),
        ));

        DB::table('lead_status_reasons')->insert(array(
            array('id' => 1, 'status_id' => 10,'reason_id' => 1),
            array('id' => 2, 'status_id' => 10,'reason_id' => 2),
            array('id' => 3, 'status_id' => 10,'reason_id' => 3),
            array('id' => 4, 'status_id' => 10,'reason_id' => 10),
            array('id' => 5, 'status_id' => 4,'reason_id' => 4),
            array('id' => 6, 'status_id' => 4,'reason_id' => 10),
            array('id' => 7, 'status_id' => 3,'reason_id' => 5),
            array('id' => 8, 'status_id' => 3,'reason_id' => 6),
            array('id' => 9, 'status_id' => 3,'reason_id' => 7),
            array('id' => 10, 'status_id' => 3,'reason_id' => 10),
        ));
    }
}
