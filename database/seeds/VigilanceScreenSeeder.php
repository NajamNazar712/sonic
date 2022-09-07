<?php

use Illuminate\Database\Seeder;

class VigilanceScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('shipment_scanning_screen_locations')->insert(array(
            array('id' => 30, 'name' => 'Vigilance Verification', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
        
        DB::table('admins_screen_list')->insert(
             array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Vigilance Verification > Delivery Note', 'url'=>'admin.vigilance.verification.index', 'permission_id' => 770),
             array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Vigilance Verification > History', 'url'=>'admin.vigilance.verification.history.index', 'permission_id' => 771)
        );


    }
}
