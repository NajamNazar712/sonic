<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForMultiplePieceShipmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 368, 'name' => 'Multiple Piece Add', 'module_id' => 3),
            array('id' => 369, 'name' => 'Multiple Piece List', 'module_id' => 3),
            array('id' => 370, 'name' => 'Multiple Piece Resolved', 'module_id' => 3),
            array('id' => 371, 'name' => 'Multiple Piece - Action', 'module_id' => 3),
            array('id' => 372, 'name' => 'Multiple Piece Return Note', 'module_id' => 3),
        ));
    }
}
