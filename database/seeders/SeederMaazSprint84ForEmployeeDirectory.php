<?php

use Illuminate\Database\Seeder;
use \App\Http\Models\Admin\ModulePermission;
use \App\Http\Models\HR\Employee;
use App\Http\Models\Rider;

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

        ModulePermission::whereIn('id',[97,98,99,381,382,447,448,450,451,452,500])->update(['module_id' => 12]);
        ModulePermission::where('id',591)->delete();
        ModulePermission::where('id',492)->update(['name'=> 'HR Rider Incentive - View']);
    }
}
