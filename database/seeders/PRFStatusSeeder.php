<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class PRFStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('prf_statuses')->insert(array(
            array('id' => 1, 'name' => 'Requested'),
            array('id' => 2, 'name' => 'Pending for Documents'),
            array('id' => 3, 'name' => 'Pending for Approval'),
            array('id' => 4, 'name' => 'Pending for payment Creation'),
            array('id' => 5, 'name' => 'Approved and Created'),
            array('id' => 6, 'name' => 'Completed/Done'),
            array('id' => 7, 'name' => 'Cancelled'),

        ));
    }
}
