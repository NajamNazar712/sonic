<?php

use Illuminate\Database\Seeder;

class AddEmployeeNatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
        DB::table('employee_natures')->insert(array(
            array('id' => 1, 'name' => 'Additional', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 2, 'name' => 'Replacement', 'created_at' => $now, 'updated_at' => $now)
        ));
    }
}
