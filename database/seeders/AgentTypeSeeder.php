<?php

use App\Http\Models\Agent\RvAgentType;
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
        RvAgentType::create([
            'name' => '1st Caller'
        ]);

        RvAgentType::create([
            'name' => '2nd Caller'
        ]);

        RvAgentType::create([
            'name' => 'Both'
        ]);
    }
}
