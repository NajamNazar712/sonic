<?php

use Illuminate\Database\Seeder;
use \App\Http\Models\City;
use \App\Http\Models\HR\EmployeePayslip;

class SeederForEmployeePayslipHubId extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (EmployeePayslip::where('hub_id',null)->get() as $payslip)
        {
            $city = City::where('name',ucfirst($payslip->hub))->first();
            if($city)
            {
                $payslip->hub_id = $city->id;
                $payslip->update();
            }
        }
    }
}
