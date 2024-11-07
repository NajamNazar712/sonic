<?php

use App\Http\Models\Shipper\User;
use Illuminate\Database\Seeder;

class UpdateCorporateUsersForRateTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('account_type_id', 2)->where('status', 3)->whereNull('corporate_rate_type_id')->get();
        if(count($users) > 0){
            foreach ($users as $user){
                $user->corporate_rate_type_id = 1;
                $user->save();
            }
        }
    }
}
