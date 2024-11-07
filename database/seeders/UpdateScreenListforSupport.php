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
        //For Support > Order Management 
        $orderid=AdminsScreenList::where('id', 84)->first();
        $orderid->permission_id=621;
        $orderid->save();
        //For Support > Self Collection Shipments 
        $selfcollectionid=AdminsScreenList::where('id', 86)->first();
        $selfcollectionid->permission_id=622;
        $selfcollectionid->save();
    }
}
