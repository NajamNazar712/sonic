<?php

use App\Http\Models\HR\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateJoiningDateEmployeeDirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return array
     */

    function import_CSV($filename, $delimiter = ','){
        if(!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false){
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false){
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }

    public function run()
    {
        $file = public_path("/SeederDataFiles/TO-5426DataFile.csv");
        $data = $this->import_CSV($file);
        if(count($data) > 0){
            foreach ($data as $datum){
                $employee = Employee::where('trax_id', trim($datum["TraxID"]));
                if($employee->exists()){
                    $employee = $employee->first();
                    if($employee->employee_nature_id == null){
                        $employee->employee_nature_id = 1;
                    }
                    if($employee->joining_date == null){
                        $employee->joining_date = Carbon::parse(trim($datum["Date of Joining"]))->format("Y-m-d");
                    }
                    $employee->save();
                }
            }
        }
    }
}
