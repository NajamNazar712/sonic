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

        DB::table('petty_cash_account_titles')->insert(array(
            array('id' =>1, 'name'=> 'ADVERTISEMENT EXPENSE'),
            array('id' =>2, 'name'=> 'AIR FREIGHT'),
            array('id' =>3, 'name'=> 'BANK CHARGES'),
            array('id' =>4, 'name'=> 'BONUS'),
            array('id' =>5, 'name'=> 'COMPANY VEHICLE FUEL'),
            array('id' =>6, 'name'=> 'Computer & Printers'),
            array('id' =>7, 'name'=> 'COMPUTER REPAIR & MAINTENANCE'),
            array('id' =>8, 'name'=> 'CONVEYANCE & TRAVELLING'),
            array('id' =>9, 'name'=> 'COURIER INCENTIVE'),
            array('id' =>10, 'name'=> 'DELIVERY EXPENSES BY THIRD PARTY'),
            array('id' =>11, 'name'=> 'DELIVERY EXPENSES VIA ROAD'),
            array('id' =>12, 'name'=> 'E.O.B.I'),
            array('id' =>13, 'name'=> 'ELECTRICITY EXPENSE'),
            array('id' =>14, 'name'=> 'Expense'),
            array('id' =>15, 'name'=> 'FEE & SUBSCRIPTIONS'),
            array('id' =>16, 'name'=> 'FREIGHT FORWARDING EXPENSE - OTHER THIRD PARTY'),
            array('id' =>17, 'name'=> 'FREIGHT FORWARDING EXPENSE - PIA'),
            array('id' =>18, 'name'=> 'FREIGHT FORWARDING EXPENSE - DAEWOO BOOKING'),
            array('id' =>19, 'name'=> 'FREIGHT FORWARDING EXPENSE - SHAHEEN AIRLINE'),
            array('id' =>20, 'name'=> 'FTL Forwarding Expense'),
            array('id' =>21, 'name'=> 'FUEL ALLOWANCES (ENTITLEMENT)'),
            array('id' =>22, 'name'=> 'G.C. MOVEMENT EXPENSE'),
            array('id' =>23, 'name'=> 'GARDENING EXPENSE'),
            array('id' =>24, 'name'=> 'GAS BILL'),
            array('id' =>25, 'name'=> 'GENERATOR FUEL'),
            array('id' =>26, 'name'=> 'Generator Rental Charges'),
            array('id' =>27, 'name'=> 'LEGAL & PROFESSIONAL CHARGES'),
            array('id' =>28, 'name'=> 'LOADING & UNLOADING EXPENSE'),
            array('id' =>29, 'name'=> 'LOCAL DELIVERY EXPENSES'),
            array('id' =>30, 'name'=> 'LOCAL PICK UP EXPENSE'),
            array('id' =>31, 'name'=> 'MAIL & INTERNET CHARGES'),
            array('id' =>32, 'name'=> 'MARK-UP ON LEASED ASSETS'),
            array('id' =>33, 'name'=> 'MOBILE ALLOWANCES (ENTITLEMENT)'),
            array('id' =>34, 'name'=> 'MOBILE PHONE BILL'),
            array('id' =>35, 'name'=> 'OFFICE EQUIPMENT REPAIR & MAINTENANCE'),
            array('id' =>36, 'name'=> 'OFFICE RENT'),
            array('id' =>37, 'name'=> 'OFFICE REPAIR & MAINTENANCE'),
            array('id' =>38, 'name'=> 'OFFICE STATIONERY'),
            array('id' =>39, 'name'=> 'PACKING MATERIAL'),
            array('id' =>40, 'name'=> 'PRINTER REPAIR & MAINTENANCE'),
            array('id' =>41, 'name'=> 'PRINTING & STATIONARY'),
            array('id' =>42, 'name'=> 'Repair & maintenance'),
            array('id' =>43, 'name'=> 'RUNNER EXPENSE'),
            array('id' =>44, 'name'=> 'SALES COMMISSION'),
            array('id' =>45, 'name'=> 'SECURITY GUARD'),
            array('id' =>46, 'name'=> 'SERVER HOSTING'),
            array('id' =>47, 'name'=> 'SHIPMENT LOST CLAIM'),
            array('id' =>48, 'name'=> 'STAFF ENTERTAINMENT'),
            array('id' =>49, 'name'=> 'STAFF SALARIES'),
            array('id' =>50, 'name'=> 'STAFF UNIFORM'),
            array('id' =>51, 'name'=> 'TELEPHONE BILL'),
            array('id' =>52, 'name'=> 'THIRD PARTY COMMISSION'),
            array('id' =>53, 'name'=> 'TRAVELLING & CONVEYANCE'),
            array('id' =>54, 'name'=> 'VEHCILE ALLOWANCE'),
            array('id' =>55, 'name'=> 'VEHICLE FUEL'),
            array('id' =>56, 'name'=> 'VISITING CARD'),
            array('id' =>57, 'name'=> 'WATER BILL'),
            array('id' =>58, 'name'=> 'OTHER'),
            array('id' =>59, 'name'=> 'OTHER OPERATIONAL EXPENSE')
        ));
    }
}
