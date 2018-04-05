<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Allen Walker',
            'email' => 'allen@email.com',
            'password' => bcrypt('password')
        ]);
    }
}
