<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\Admin;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'username' => 'admin',
            'department' => 'Admin Department',
            'password' => bcrypt('password')
        ]);

    }
}



