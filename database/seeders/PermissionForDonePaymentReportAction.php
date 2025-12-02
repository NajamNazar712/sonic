<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionForDonePaymentReportAction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 1052, 'name' => 'Done Payments - Generate Daily Report', 'module_id' => 8),
            array('id' => 1053, 'name' => 'Retail Done Payments - Generate Daily Report', 'module_id' => 8),
        ));
    }
}
