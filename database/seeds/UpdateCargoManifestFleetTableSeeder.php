<?php

use Illuminate\Database\Seeder;

class UpdateCargoManifestFleetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('fleets')->truncate();
        DB::table('fleet_drivers')->truncate();
        DB::table('fleet_vendors')->truncate();

        DB::table('fleets')->insert(array(
            array('id' => 1 , 'vehicle_type_id' => 1 , 'reg_number' => 'LES-5155' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 1 , 'vendor_id' => 4 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2 , 'vehicle_type_id' => 2 , 'reg_number' => 'LES-2686' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 2 , 'vendor_id' => 2 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3 , 'vehicle_type_id' => 3 , 'reg_number' => 'FDS-145' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 3 , 'vendor_id' => 2 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4 , 'vehicle_type_id' => 2 , 'reg_number' => 'FDS-2527' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 4 , 'vendor_id' => 2 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5 , 'vehicle_type_id' => 2 , 'reg_number' => 'RIS-1413' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 5 , 'vendor_id' => 3 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6 , 'vehicle_type_id' => 1 , 'reg_number' => 'TN-608' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 6 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7 , 'vehicle_type_id' => 2 , 'reg_number' => 'MNS-1200' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 7 , 'vendor_id' => 3 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 8 , 'vehicle_type_id' => 1 , 'reg_number' => 'JF-4431' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 8 , 'vendor_id' => 4 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 9 , 'vehicle_type_id' => 2 , 'reg_number' => 'LA-0881' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 9 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 10 , 'vehicle_type_id' => 3 , 'reg_number' => 'LES-3351' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 10 , 'vendor_id' => 2 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 11 , 'vehicle_type_id' => 3 , 'reg_number' => 'LES-6006' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 11 , 'vendor_id' => 2 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 12 , 'vehicle_type_id' => 2 , 'reg_number' => 'LES-9575' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 12 , 'vendor_id' => 2 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 13 , 'vehicle_type_id' => 1 , 'reg_number' => 'LES-4028' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 13 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 14 , 'vehicle_type_id' => 4 , 'reg_number' => 'JV-8216' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 14 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 15 , 'vehicle_type_id' => 4 , 'reg_number' => 'JV-3728' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 15 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 16 , 'vehicle_type_id' => 4 , 'reg_number' => 'JV-8217' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 16 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 17 , 'vehicle_type_id' => 4 , 'reg_number' => 'JV-7577' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 17 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 18 , 'vehicle_type_id' => 4 , 'reg_number' => 'JV-7701' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 18 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 19 , 'vehicle_type_id' => 4 , 'reg_number' => 'JV-6240' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 19 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 20 , 'vehicle_type_id' => 4 , 'reg_number' => 'JV-8701' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 20 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 21 , 'vehicle_type_id' => 4 , 'reg_number' => 'JW-6701' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 21 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 22 , 'vehicle_type_id' => 1 , 'reg_number' => 'JF-9701' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 22 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 23 , 'vehicle_type_id' => 1 , 'reg_number' => 'RIS-1644' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 23 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 24 , 'vehicle_type_id' => 5 , 'reg_number' => 'CU-7431' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 24 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 25 , 'vehicle_type_id' => 5 , 'reg_number' => 'MNS-272' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 25 , 'vendor_id' => 1 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 26 , 'vehicle_type_id' => 5 , 'reg_number' => 'CE-6107' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 26 , 'vendor_id' => 5 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 27 , 'vehicle_type_id' => 5 , 'reg_number' => 'CN-1676' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 27 , 'vendor_id' => 6 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 28 , 'vehicle_type_id' => 5 , 'reg_number' => 'ADG-449' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 28 , 'vendor_id' => 2 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 29 , 'vehicle_type_id' => 5 , 'reg_number' => 'LEJ-6327' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 29 , 'vendor_id' => 7 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 30 , 'vehicle_type_id' => 5 , 'reg_number' => 'LES-5721' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 30 , 'vendor_id' => 8 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 31 , 'vehicle_type_id' => 5 , 'reg_number' => 'LE-1276' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 31 , 'vendor_id' => 9 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 32 , 'vehicle_type_id' => 5 , 'reg_number' => 'W-9139' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 32 , 'vendor_id' => 9 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 33 , 'vehicle_type_id' => 6 , 'reg_number' => '3PL' , 'status' => 1, 'tracking_id' => 1, 'driver_id' => 33 , 'vendor_id' => 10 , 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));

        DB::table('fleet_vendors')->insert(array(
            array('id' => 1 , 'name' => 'PMGT'),
            array('id' => 2 , 'name' => 'RELIANCE INTL.'),
            array('id' => 3 , 'name' => 'K.G Transport'),
            array('id' => 4 , 'name' => 'Khan Transport'),
            array('id' => 5 , 'name' => 'FAYYAZ TRANSPORT'),
            array('id' => 6 , 'name' => 'SOHAIL TRANSPORT'),
            array('id' => 7 , 'name' => 'YAQEEN TRANSPORT'),
            array('id' => 8 , 'name' => 'SIKANDER'),
            array('id' => 9 , 'name' => 'FARRUKH & SONS'),
            array('id' => 10 , 'name' => '3PL'),
        ));

        DB::table('fleet_drivers')->insert(array(
            array('id' =>  1 , 'name' => ' AYAZ/ALAM  ' , 'phone_no' => ' 0317-1460485  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  2 , 'name' => ' Azhar  ' , 'phone_no' => ' 0324-7786186  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp), 
            array('id' =>  3 , 'name' => ' ALAM  ' , 'phone_no' => ' 0301-2002824  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  4 , 'name' => ' ADNAN  ' , 'phone_no' => ' 0302-5137801  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  5 , 'name' => ' AMJAD  ' , 'phone_no' => ' 0345-6100826  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  6 , 'name' => ' SAJID/ZIAQAT  ' , 'phone_no' => ' 0304-2982304  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  7 , 'name' => ' EJAZ  ' , 'phone_no' => ' 0336-4051407  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  8 , 'name' => ' WALEED  ' , 'phone_no' => ' 0303-2685405  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  9 , 'name' => ' FAHEEM  ' , 'phone_no' => ' 0344-9478965  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  10 , 'name' => ' HAMID  ' , 'phone_no' => ' 0306-4958262  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  11 , 'name' => ' TOUQEER  ' , 'phone_no' => ' 0301-6281617  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  12 , 'name' => ' FAISAL/YASIR  ' , 'phone_no' => ' 0302-7759740  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  13 , 'name' => ' MUZZAFAR  ' , 'phone_no' => ' 0300-7354223  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  14 , 'name' => ' KASHIF  ' , 'phone_no' => ' 0306-5237896  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  15 , 'name' => ' JAVED  ' , 'phone_no' => ' 0302-7475096  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  16 , 'name' => ' ASIF  ' , 'phone_no' => ' 0301-2082504  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  17 , 'name' => ' MUJAHID  ' , 'phone_no' => ' 0342-3393884  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  18 , 'name' => ' RAFEEQ  ' , 'phone_no' => ' 0302-2619251  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  19 , 'name' => ' SHAKEEL  ' , 'phone_no' => ' 0308-2569009  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  20 , 'name' => ' INTEZAR  ' , 'phone_no' => ' 0305-3628379  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  21 , 'name' => ' MOHSIN  ' , 'phone_no' => ' 0335-3711356  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  22 , 'name' => ' YASIR  ' , 'phone_no' => ' 0331-8376977  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  23 , 'name' => ' ROZI  ' , 'phone_no' => ' 0331-8376977  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  24 , 'name' => ' NADEEM  ' , 'phone_no' => ' 0306-7496515  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  25 , 'name' => ' HAFEEZ  ' , 'phone_no' => ' 0301-5252288  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  26 , 'name' => ' MANZOOR  ' , 'phone_no' => ' 0333-7072315  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  27 , 'name' => ' SOHAIL  ' , 'phone_no' => ' 0321-2814912  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  28 , 'name' => ' WAQAR  ' , 'phone_no' => ' 0301-643013  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  29 , 'name' => ' NABEEL  ' , 'phone_no' => ' 0345-5725120  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  30 , 'name' => ' ADEEL  ' , 'phone_no' => ' 0342-0680025  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  31 , 'name' => ' ZABEEL  ' , 'phone_no' => ' 0315-5768059  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  32 , 'name' => ' NAEEM  ' , 'phone_no' => ' 0315-2250099  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
            array('id' =>  33 , 'name' => ' 3PL Driver'  , 'phone_no' => ' 0300-0000000  ' , 'cnic_no' => ' -  ' , 'created_at' => $timestamp,  'updated_at' => $timestamp),
        ));
    }
}
