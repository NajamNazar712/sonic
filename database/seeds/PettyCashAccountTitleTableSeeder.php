<?php

use Illuminate\Database\Seeder;

class PettyCashAccountTitleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('petty_cash_account_titles')->truncate();
        $time = \Carbon\Carbon::now();
        DB::table('petty_cash_account_titles')->insert(array(
            array('id' =>1, 'name'=> 'ADVERTISEMENT EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>2, 'name'=> 'AIR FREIGHT', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>3, 'name'=> 'BANK CHARGES', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>4, 'name'=> 'BONUS', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>5, 'name'=> 'COMPANY VEHICLE FUEL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>6, 'name'=> 'Computer & Printers', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>7, 'name'=> 'COMPUTER REPAIR & MAINTENANCE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>8, 'name'=> 'CONVEYANCE & TRAVELLING', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>9, 'name'=> 'COURIER INCENTIVE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>10, 'name'=> 'DELIVERY EXPENSES BY THIRD PARTY', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>11, 'name'=> 'DELIVERY EXPENSES VIA ROAD', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>12, 'name'=> 'E.O.B.I', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>13, 'name'=> 'ELECTRICITY EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>14, 'name'=> 'Expense', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>15, 'name'=> 'FEE & SUBSCRIPTIONS', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>16, 'name'=> 'FREIGHT FORWARDING EXPENSE - OTHER THIRD PARTY', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>17, 'name'=> 'FREIGHT FORWARDING EXPENSE - PIA', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>18, 'name'=> 'FREIGHT FORWARDING EXPENSE - DAEWOO BOOKING', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>19, 'name'=> 'FREIGHT FORWARDING EXPENSE - SHAHEEN AIRLINE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>20, 'name'=> 'FTL Forwarding Expense', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>21, 'name'=> 'FUEL ALLOWANCES (ENTITLEMENT)', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>22, 'name'=> 'G.C. MOVEMENT EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>23, 'name'=> 'GARDENING EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>24, 'name'=> 'GAS BILL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>25, 'name'=> 'GENERATOR FUEL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>26, 'name'=> 'Generator Rental Charges', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>27, 'name'=> 'LEGAL & PROFESSIONAL CHARGES', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>28, 'name'=> 'LOADING & UNLOADING EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>29, 'name'=> 'LOCAL DELIVERY EXPENSES', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>30, 'name'=> 'LOCAL PICK UP EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>31, 'name'=> 'MAIL & INTERNET CHARGES', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>32, 'name'=> 'MARK-UP ON LEASED ASSETS', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>33, 'name'=> 'MOBILE ALLOWANCES (ENTITLEMENT)', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>34, 'name'=> 'MOBILE PHONE BILL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>35, 'name'=> 'OFFICE EQUIPMENT REPAIR & MAINTENANCE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>36, 'name'=> 'OFFICE RENT', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>37, 'name'=> 'OFFICE REPAIR & MAINTENANCE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>38, 'name'=> 'OFFICE STATIONERY', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>39, 'name'=> 'PACKING MATERIAL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>40, 'name'=> 'PRINTER REPAIR & MAINTENANCE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>41, 'name'=> 'PRINTING & STATIONARY', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>42, 'name'=> 'Repair & maintenance', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>43, 'name'=> 'RUNNER EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>44, 'name'=> 'SALES COMMISSION', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>45, 'name'=> 'SECURITY GUARD', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>46, 'name'=> 'SERVER HOSTING', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>47, 'name'=> 'SHIPMENT LOST CLAIM', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>48, 'name'=> 'STAFF ENTERTAINMENT', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>49, 'name'=> 'STAFF SALARIES', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>50, 'name'=> 'STAFF UNIFORM', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>51, 'name'=> 'TELEPHONE BILL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>52, 'name'=> 'THIRD PARTY COMMISSION', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>53, 'name'=> 'TRAVELLING & CONVEYANCE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>54, 'name'=> 'VEHCILE ALLOWANCE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>55, 'name'=> 'VEHICLE FUEL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>56, 'name'=> 'VISITING CARD', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>57, 'name'=> 'WATER BILL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>58, 'name'=> 'OTHER', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>59, 'name'=> 'OTHER OPERATIONAL EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>60, 'name'=> 'GENERAL EXPENSE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>61, 'name'=> 'COURIER STAFF FUEL', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>62, 'name'=> 'VEHICLE REPAIR AND MAINTENANCE', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>63, 'name'=> 'SHIPMENT LOST CLAIM', 'created_at' => $time, 'updated_at' => $time),
            array('id' =>64, 'name'=> 'TRAINING & DEVELOPMENT', 'created_at' => $time, 'updated_at' => $time),

        ));
    }
}
