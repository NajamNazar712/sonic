<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UsersTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(CityTableSeeder::class);
        $this->call(CityPickupTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(BookingTableSeeder::class);
        $this->call(PickupTypeTableSeeder::class);
        $this->call(ShippingModeTableSeeder::class);
        $this->call(ShippingModeSameDayTimingTableSeeder::class);
        $this->call(PaymentModeTableSeeder::class);
    }
}
