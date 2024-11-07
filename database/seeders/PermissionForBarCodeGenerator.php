<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionForBarCodeGenerator extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert([
            ['id' => 980, 'name' => 'Barcode Generator - List', 'module_id' => 33],
            ['id' => 981, 'name' => 'Barcode Generator - Create', 'module_id' => 33],
            
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 785, 'screen_name' => 'Barcode Generator', 'action' => 'View'],
            ['id' => 786, 'screen_name' => 'Barcode Generator', 'action' => 'Submit'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Support > Barcode Genetor',
                'url' => 'admin.barcode_generator.index',
                'permission_id' => 980
            ],
        ]);
    }
}
