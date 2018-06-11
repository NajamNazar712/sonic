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
        $this->call(StandardWeightChargesTableSeeder::class);
        $this->call(StandardCashHandlingChargesTableSeeder::class);
        $this->call(StandardInsuranceChargesTableSeeder::class);
        $this->call(StandardReturnChargesTableSeeder::class);
        $this->call(StandardFuelSurchargesTableSeeder::class);
        $this->call(StandardPackagingChargesTableSeeder::class);
        $this->call(StandardBookingTypeChargesTableSeeder::class);
        $this->call(PickupNoteStatusTableSeeder::class);
        $this->call(ShipmentStatusTableSeeder::class);
        $this->call(ShipmentStatusReasonTableSeeder::class);
        $this->call(ShipmentStatusShipmentStatusReasonTableSeeder::class);
    }
}
