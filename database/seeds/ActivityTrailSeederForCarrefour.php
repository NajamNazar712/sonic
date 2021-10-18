<?php

use Illuminate\Database\Seeder;

class ActivityTrailSeederForCarrefour extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 455, 'screen_name' => 'Carrefour Bulk Delivery', 'action'=> 'View'),
            array('id' => 456, 'screen_name' => 'Carrefour Bulk Return', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Carrefour > Carrefour Bulk Delivery', 'url'=>'admin.carrefour.delivery.index', 'permission_id' => 606)
        );

        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Carrefour > Carrefour Bulk Return', 'url'=>'admin.carrefour.return.index', 'permission_id' => 607)
    );
    }
}
