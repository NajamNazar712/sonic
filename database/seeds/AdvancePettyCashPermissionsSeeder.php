<?php

use Illuminate\Database\Seeder;

class AdvancePettyCashPermissionsSeeder extends Seeder
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
            array('id' => 881, 'name' => 'Advance Petty Cash Create - View', 'module_id' => 8),
            array('id' => 882, 'name' => 'Advance Petty Cash Statement', 'module_id' => 8),
            array('id' => 883, 'name' => 'Advance Petty Cash Statement - Actions', 'module_id' => 8),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 675, 'screen_name' => 'Advance Petty Cash (Create)s', 'action'=> 'View'),
            array('id' => 676, 'screen_name' => 'Advance Petty Cash (Statement)', 'action'=> 'View'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > Petty Cash > Advance Petty Cash > Create', 'url'=>'admin.petty_cash.advance.index', 'permission_id' => 881),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > Petty Cash > Advance Petty Cash > Create', 'url'=>'admin.petty_cash.advance.statements.index', 'permission_id' => 882),
        ));
    }
}
