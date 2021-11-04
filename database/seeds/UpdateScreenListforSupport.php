<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\AdminsScreenList;

class UpdateScreenListforSupport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminsScreenList::where('id', 85)->delete();
        AdminsScreenList::where('id', 86)->delete();
        
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support  >  Order Management', 'url'=>'admin.orders.index', 'permission_id' => 621),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support  >  Self Collection Shipments', 'url'=>'admin.orders.self_collection.index', 'permission_id' => 622),
        ));
    }
}
