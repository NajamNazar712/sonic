<?php

use Illuminate\Database\Seeder;

class UpdateEmployeeDesignationForCodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $designations = \App\Http\Models\HR\EmployeeDesignation::whereNull('code')->get();
        if($designations){
            foreach ($designations as $designation){
                $designation->code = 'Des'. str_pad($designation->id, 3, '0', STR_PAD_LEFT);
                $designation->save();
            }
        }
        $departments = \App\Http\Models\Admin\AdminDepartment::whereNull('code')->get();
        if($departments){
            foreach ($departments as $department){
                $department->code = 'Dep'. str_pad($department->id, 3, '0', STR_PAD_LEFT);
                $department->save();
            }
        }
    }
}
