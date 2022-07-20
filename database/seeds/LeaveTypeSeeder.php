<?php

use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('leave_types')->truncate();

        DB::table('leave_types')->insert(array(
            array('id' => 1, 'name' => 'Annual Leave', 'count' => 0),
            array('id' => 2, 'name' => 'Maternity Leaves', 'count' => 56),
            array('id' => 3, 'name' => 'Paternity Leaves', 'count' => 6),
            array('id' => 4, 'name' => 'Pilgrimage Leaves', 'count' => 10),
            array('id' => 5, 'name' => 'Extra Ordinary Leaves', 'count' => 30),
            array('id' => 6, 'name' => 'Leaves Without Pay', 'count' => 56),
        ));
    }
}
