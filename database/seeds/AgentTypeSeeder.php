<?php

use App\AgentType;
use Illuminate\Database\Seeder;

class AgentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AgentType::create([
            'name' => '1st Caller'
        ]);

        AgentType::create([
            'name' => '2nd Caller'
        ]);

        AgentType::create([
            'name' => 'Both'
        ]);
    }
}
