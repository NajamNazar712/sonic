<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForVigilanceV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('shipment_scanning_screen_locations')->where('id', 30)->update(['name' => 'Vigilance Note']);

        DB::table('admins_screen_list')->where('permission_id', 770)->delete();

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Vigilance > Delivery / Return Note', 'url'=>'admin.vigilance.note.index', 'permission_id' => 770),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Vigilance > History', 'url'=>'admin.vigilance.note.history.index', 'permission_id' => 771)
        ));
    }
}
