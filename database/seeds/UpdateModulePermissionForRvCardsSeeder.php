<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRvCardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 943, 'name' => 'Shipment - Reason Validation Required Cards - View', 'module_id' => 7),
        ));

    }
}
