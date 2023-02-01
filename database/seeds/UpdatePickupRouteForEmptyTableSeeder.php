<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Route;

class UpdatePickupRouteForEmptyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = Route::where('route_type_id', 1);
        if($routes->exists()){
            $routes = $routes->get();
        }
    }
}
