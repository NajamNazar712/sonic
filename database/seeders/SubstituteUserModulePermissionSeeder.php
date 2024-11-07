<?php

use Illuminate\Database\Seeder;

class SubstituteUserModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('substitute_user_module_permissions')->truncate();

        DB::table('substitute_user_module_permissions')->insert(array(
            array('id' => 1, 'name' => 'Book Shipment'),
            array('id' => 2, 'name' => 'Cancel Shipment'),
            array('id' => 3, 'name' => 'Receiving Sheet'),
            array('id' => 4, 'name' => 'Packaging Material'),
            array('id' => 5, 'name' => 'Finance Payments'),
            array('id' => 6, 'name' => 'Dispute - Log'),
            array('id' => 7, 'name' => 'Dispute - Shipment Rebooking'),
            array('id' => 8, 'name' => 'Reports')
        ));
    }
}
