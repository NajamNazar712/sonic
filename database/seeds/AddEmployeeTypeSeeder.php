<?php

use Illuminate\Database\Seeder;

class AddEmployeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_types')->insert(array(
            array('id' => 3, 'name' => 'Shipper'),
            array('id' => 4, 'name' => 'Consignee')
        ));
    }
}
