<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Shipper\UserShippingInfo;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /* User::create([
            'name' => 'Allen Walker',
            'email' => 'allen@email.com',
            'password' => bcrypt('password'),
            'active' => 1
        ]);*/
        
        
        factory(App\Http\Models\Shipper\User::class,7)->create();
        $faker = Faker\Factory::create();

        // following line retrieve all the user_ids from DB
        $users = User::all();
        $user_array = array();

        foreach($users as $user){
           // array_push($user_array, $user->id);
             $userBankInfo = UserBankInfo::create([
                'user_id' => $user->id,
                'bank_name' => 'HBL',
                'bank_branch' => 'Sadar',
                'account_no' => $faker->bankAccountNumber(),
                'account_title' => $faker->firstName(),
                'iban' => $faker->bankAccountNumber(),
                'city_code' => 202,
                'payment_mode' => 'ibft',
                'payment_cycle' => 'daily'
            ]);

            $userShippingInfo = UserShippingInfo::create([
                'user_id' => $user->id,
                'pickup_address' => $faker->address(),
                'poc' => $faker->name(),
                'phone' => $faker->phoneNumber(),
                'email' => $faker->email(),
                'city_code' => 202
            ]);
        }

    }
}
