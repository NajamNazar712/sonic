<?php

use Illuminate\Database\Seeder;

class PettyCashApprovedEditButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 840, 'name' => 'Approved Petty Cash Edit - Action', 'module_id' => 8),
            array('id' => 841, 'name' => 'Approved Petty Cash Edit Amount - Action', 'module_id' => 8),
        ));
    }
}
