<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Rider;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;

class Seeder5202ForUpdatingEmployeeIdInAdminRiderTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = Admin::whereNull('employee_id')->whereNotNull('trax_id')->where('duplicate_user',0);
        if($admins->exists())
        {
            $admins = $admins->get();
            foreach ($admins as $admin)
            {
                $emp = Employee::where('trax_id',$admin->trax_id);
                if($emp->exists())
                {
                    $emp = $emp->first();
                    $admin->employee_id = $emp->id;
                    $admin->save();
                }
            }
        }

        $riders = Rider::whereNull('employee_id')->whereNotNull('trax_id');
        if($riders->exists())
        {
            $riders = $riders->get();
            foreach ($riders as $rider)
            {
                $emp = Employee::where('trax_id',$rider->trax_id);
                if($emp->exists())
                {
                    $emp = $emp->first();
                    $rider->employee_id = $emp->id;
                    $rider->save();
                }
            }
        }
    }
}
