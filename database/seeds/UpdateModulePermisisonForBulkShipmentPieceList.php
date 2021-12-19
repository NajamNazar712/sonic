<?php

use Illuminate\Database\Seeder;

class UpdateModulePermisisonForBulkShipmentPieceList extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 349, 'name' => 'Multiple Piece Bulk Remaining Piece', 'module_id' => 3),
            array('id' => 350, 'name' => 'Multiple Piece Bulk Return to Shipper', 'module_id' => 3),
            array('id' => 351, 'name' => 'Multiple Piece Bulk Single Piece', 'module_id' => 3),
        ));
    }
}
