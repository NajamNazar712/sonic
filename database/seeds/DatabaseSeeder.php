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
        $this->call(AdminDepartmentTableSeeder::class);
        $this->call(AdminRoleModulePermissionsTableSeeder::class);
        $this->call(AdminRoleTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(BanksListTableSeeder::class);
        $this->call(BookingTableSeeder::class);
        $this->call(CargoConsignmentStatusTableSeeder::class);
        $this->call(DisputeTypeTableSeeder::class);
        $this->call(ModulePermissionTableSeeder::class);
        $this->call(ModuleTableSeeder::class);
        $this->call(NewCityTableSeeder::class);
        $this->call(NotificationTableSeeder::class);
        $this->call(NotificationTypeTableSeeder::class);
        $this->call(PackagingMaterialStockHeadsTableSeeder::class);
        $this->call(PackagingPaymentModesTableSeeder::class);
        $this->call(PaymentModeTableSeeder::class);
        $this->call(PickupNoteStatusTableSeeder::class);
        $this->call(PickupTypeTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(RiderCategoryTableSeeder::class);
        $this->call(RidersTableSeeder::class);
        $this->call(ShipmentPaymentStatusTableSeeder::class);
        $this->call(ShipmentStatusReasonTableSeeder::class);
        $this->call(ShipmentStatusShipmentStatusReasonTableSeeder::class);
        $this->call(ShipmentStatusTableSeeder::class);
        $this->call(ShippingModeSameDayTimingTableSeeder::class);
        $this->call(ShippingModeTableSeeder::class);
        $this->call(StandardBookingTypeChargesTableSeeder::class);
        $this->call(StandardCashHandlingChargesTableSeeder::class);
        $this->call(StandardFuelSurchargesTableSeeder::class);
        $this->call(StandardInsuranceChargesTableSeeder::class);
        $this->call(StandardPackagingChargesTableSeeder::class);
        $this->call(StandardReturnChargesTableSeeder::class);
        $this->call(StandardWeightChargesTableSeeder::class);
        $this->call(SubstituteUserModulePermissionSeeder::class);
        $this->call(TransportModeTableSeeder::class);
        $this->call(TransportModeVendorTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
