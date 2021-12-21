<?php

use Illuminate\Database\Seeder;

class SeederMaazSprint84ForEmployeeDirectory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 652, 'name' => 'Employee Directory - HR', 'module_id' => 28),
        ));

        $employees = Employee::where('trax_id','!=',null)->where('employee_type_id',2)->get();

        foreach ($employees as $employee)
        {
            $rider = Rider::where('trax_id',$employee->trax_id)->where('trax_id','!=',null);
            if($rider->exists())
            {
                $rider = $rider->first();
                $employee->rider_type_id = $rider->rider_type_id;
                $employee->update();
            }
        }
    }
}
