<?php

use Illuminate\Database\Seeder;

class UpdateCaseNatureTypeForFlyersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Http\Models\CRM\CrmRequestCaseNatureType::create([
           'nature_id' => 1,
            'type' => 'Flyers'
        ]);
    }
}
