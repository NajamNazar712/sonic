<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagingMaterialRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        DB::table('packaging_material_types')->truncate();
        DB::table('packaging_material_types')->insert([
           'id' => 1, 'type' => 'Flyers', 'description' => 'Sealable plastic packaging bags with pocket to enter air waybill', 'status' => 1, 'created_by' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp
        ]);
        DB::table('packaging_material_type_sizes')->truncate();
        DB::table('packaging_material_type_sizes')->insert(array(
            array('id'=>1, 'size'=>'Small', 'type_id' => 1, 'standard_charges' => 10, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id'=>2, 'size'=>'Medium', 'type_id' => 1, 'standard_charges' => 15, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id'=>3, 'size'=>'Large', 'type_id' => 1, 'standard_charges' => 20, 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));


        DB::table('packaging_charges')->truncate();
        $users = DB::table('users')->select('id', 'account_type_id')->get();
        foreach ($users as $user){
            if($user->account_type_id == 1){
                DB::table('packaging_charges')->insert(array(
                    array('user_id'=> $user->id, 'type_id' => 1, 'size_id' => 1, 'charges' => 10, 'created_at' => $timestamp, 'updated_at' => $timestamp),
                    array('user_id'=> $user->id, 'type_id' => 1, 'size_id' => 2, 'charges' => 15, 'created_at' => $timestamp, 'updated_at' => $timestamp),
                    array('user_id'=> $user->id, 'type_id' => 1, 'size_id' => 3, 'charges' => 20, 'created_at' => $timestamp, 'updated_at' => $timestamp),

                ));
            }

        }
    }
}
