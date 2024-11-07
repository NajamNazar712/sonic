<?php

use Illuminate\Database\Seeder;

class SeederUpdateRiderOTP extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Http\Models\Admin\ModulePermission::where('id',720)->delete();
        $screen = \App\Http\Models\Admin\AdminsScreenList::where('name','Support > Rider OTP')->first();
        $screen->name = "Support > Rider OTP (Login & Delivery Note)";
        $screen->permission_id = 563;
        $screen->update();
    }
}
