<?php

use Illuminate\Database\Seeder;
use \App\Http\Models\HR\Employee;

class UpdateDateofBirthInEmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::where('employee_type_id', 1)->whereIn('status_id', [1,3])->update(['date_of_birth' => null]);
    }
}
