<?php

use Illuminate\Database\Seeder;

class HandoverStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('handover_statuses')->truncate();

        DB::table('handover_statuses')->insert(array(
            array('id' => 1, 'name' => 'Forwarded'),
            array('id' => 2, 'name' => 'Delivered'),
            array('id' => 3, 'name' => 'Partial Delivered'),
            array('id' => 4, 'name' => 'Received')
        ));
    }
}
