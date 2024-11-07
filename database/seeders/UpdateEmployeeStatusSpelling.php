<?php

use Illuminate\Database\Seeder;

class UpdateEmployeeStatusSpelling extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_statuses')->where('id', 3)->update(['name' => 'Active - No Info']);
    }
}
