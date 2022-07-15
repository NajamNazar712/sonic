<?php

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Rider;
use Illuminate\Database\Seeder;

class SeederForUpdateFirstLoginTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rider = Rider::where('status', 1)->update(['first_login' => 1]);
        $admins = Admin::where('status', 1)->update(['first_login' => 1]);
        $retail_users = RetailUser::where('status', 1)->update(['first_login' => 1]);
    }
}
