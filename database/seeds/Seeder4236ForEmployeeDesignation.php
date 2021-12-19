<?php

use Illuminate\Database\Seeder;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\City;
use App\Http\Models\HR\EmployeeDesignationHub;

class Seeder4236ForEmployeeDesignation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = City::select('id', 'name')->where('hub', 1)->get();
        $designations = EmployeeDesignation::whereIn('department_id', [3,1,10,9,7,8,5,4])->where('id','!=',58)->get();
        foreach ($designations as $designation)
        {
            EmployeeDesignationHub::where('designation_id',$designation->id)->delete();
            foreach ($cities as $city)
            {
                $designation_hub = new EmployeeDesignationHub();
                $designation_hub->designation_id = $designation->id;
                $designation_hub->hub_id = $city->id;
                $designation_hub->save();
            }
        }
    }
}
