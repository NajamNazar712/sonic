<?php

use Illuminate\Database\Seeder;

class UpdatePermissionForFinaceInvoiceUploadSlip extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 589, 'name' => 'Invoice Upload Deposit Slip', 'module_id' => 8)
        ));
    }
}
