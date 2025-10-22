<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LostCategory;

class LostCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Transit Lost',
            'Snatching/Theft/Stolen',
            'Lost by Operation Staff',
            'Lost by Rider',
            'Lost by Rider & Operation Staff',
        ];

        foreach ($categories as $category) {
            LostCategory::updateOrCreate(['name' => $category]);
        }
    }
}
