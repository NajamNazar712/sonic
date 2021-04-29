<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReturnConfirmationTAT extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 488, 'name' => 'Return Confirmation TAT Setting - View', 'module_id' => 14),
            array('id' => 489, 'name' => 'Return Confirmation TAT Setting - Update', 'module_id' => 14),
            array('id' => 490, 'name' => 'Bypass Return Confirmation TAT', 'module_id' => 7),
        ));
    }
}
