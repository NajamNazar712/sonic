<?php

use Illuminate\Database\Seeder;

class ModulePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->truncate();

        DB::table('module_permissions')->insert(array(
            array('id' => 1, 'name' => 'View', 'module_id' => 1),
            array('id' => 2, 'name' => 'Launch', 'module_id' => 1),
            array('id' => 3, 'name' => 'Update', 'module_id' => 1),
            array('id' => 4, 'name' => 'Resolve', 'module_id' => 1),

            array('id' => 5, 'name' => 'Pending - View', 'module_id' => 2),
            array('id' => 6, 'name' => 'Pending - Add Rates', 'module_id' => 2),
            array('id' => 7, 'name' => 'Pending - Edit Rates', 'module_id' => 2),
            array('id' => 8, 'name' => 'Pending - Authorize Rates', 'module_id' => 2),
            array('id' => 9, 'name' => 'Pending - Activate', 'module_id' => 2),
            array('id' => 10, 'name' => 'Pending - Block', 'module_id' => 2),
            array('id' => 11, 'name' => 'Active - View', 'module_id' => 2),
            array('id' => 12, 'name' => 'Active - Edit Rates', 'module_id' => 2),
            array('id' => 13, 'name' => 'Active - Enable/Disable', 'module_id' => 2),
            array('id' => 14, 'name' => 'Active - Block', 'module_id' => 2),
            array('id' => 15, 'name' => 'Block - View', 'module_id' => 2),
            array('id' => 16, 'name' => 'Block - Unblock', 'module_id' => 2),

            array('id' => 17, 'name' => 'Pending - View', 'module_id' => 3),
            array('id' => 18, 'name' => 'Pending - Cancel', 'module_id' => 3),
            array('id' => 19, 'name' => 'Pending - Assign', 'module_id' => 3),
            array('id' => 20, 'name' => 'Assigned - View', 'module_id' => 3),
            array('id' => 21, 'name' => 'Assigned - Cancel', 'module_id' => 3),
            array('id' => 22, 'name' => 'Assigned - Dispatch', 'module_id' => 3),
            array('id' => 23, 'name' => 'Receive - View', 'module_id' => 3),
            array('id' => 24, 'name' => 'Receive - Receive', 'module_id' => 3),

            array('id' => 25, 'name' => 'Pending - View', 'module_id' => 4),
            array('id' => 26, 'name' => 'Create', 'module_id' => 4),
            array('id' => 27, 'name' => 'In Transit - View', 'module_id' => 4),
            array('id' => 28, 'name' => 'In Transit - Update Forwarding Details', 'module_id' => 4),
            array('id' => 29, 'name' => 'In Transit - Launch Dispute', 'module_id' => 4),
            array('id' => 30, 'name' => 'In Transit - Receive at Link', 'module_id' => 4),
            array('id' => 31, 'name' => 'In Transit - Receive', 'module_id' => 4),

            array('id' => 32, 'name' => 'View', 'module_id' => 5),

            array('id' => 33, 'name' => 'Pending - View', 'module_id' => 6),
            array('id' => 34, 'name' => 'Pending - Launch Dispute', 'module_id' => 6),
            array('id' => 35, 'name' => 'Create', 'module_id' => 6),
            array('id' => 36, 'name' => 'Receive - View', 'module_id' => 6),
            array('id' => 37, 'name' => 'Receive - Receive', 'module_id' => 6),
            array('id' => 38, 'name' => 'Receive - Shift Shipment', 'module_id' => 6),
            array('id' => 39, 'name' => 'Receive - Verify Statuses', 'module_id' => 6),
            array('id' => 40, 'name' => 'Completed - View', 'module_id' => 6),
            array('id' => 41, 'name' => 'Completed - Deposit DNCC', 'module_id' => 6),
            array('id' => 42, 'name' => 'SDN - View', 'module_id' => 6),
            array('id' => 43, 'name' => 'SDN - Upload Deposit Slip', 'module_id' => 6),

            array('id' => 44, 'name' => 'Marked - View', 'module_id' => 7),
            array('id' => 45, 'name' => 'Marked - Confirm', 'module_id' => 7),
            array('id' => 46, 'name' => 'Marked - Re-Attempt', 'module_id' => 7),
            array('id' => 47, 'name' => 'Confirm - View', 'module_id' => 7),
            array('id' => 48, 'name' => 'Create', 'module_id' => 7),
            array('id' => 49, 'name' => 'Receive - View', 'module_id' => 7),
            array('id' => 50, 'name' => 'Receive - Receive', 'module_id' => 7),
            array('id' => 51, 'name' => 'Receive - Shift Shipment', 'module_id' => 7),

            array('id' => 52, 'name' => 'Outstanding SDN - View', 'module_id' => 8),
            array('id' => 53, 'name' => 'Outstanding SDN - Reconcile Delivery Note(s)', 'module_id' => 8),
            array('id' => 54, 'name' => 'Outstanding Shipments - View', 'module_id' => 8),
            array('id' => 55, 'name' => 'Outstanding Shipments - Resolve', 'module_id' => 8),
            array('id' => 56, 'name' => 'Outstanding Shipments - Adjust in Payment', 'module_id' => 8),
            array('id' => 57, 'name' => 'Change Shipment Amount - View', 'module_id' => 8),
            array('id' => 58, 'name' => 'Change Shipment Amount - Change', 'module_id' => 8),
            array('id' => 59, 'name' => 'Make Payments - View', 'module_id' => 8),
            array('id' => 60, 'name' => 'Make Payments - Make', 'module_id' => 8),
            array('id' => 61, 'name' => 'Done Payments - View', 'module_id' => 8),
            array('id' => 62, 'name' => 'Done Payments - Paid', 'module_id' => 8),
            array('id' => 63, 'name' => 'Done Payments - Reverted', 'module_id' => 8),

            array('id' => 64, 'name' => 'Pickup Notes Completed', 'module_id' => 9),
            array('id' => 65, 'name' => 'Cargoes Received', 'module_id' => 9),
            array('id' => 66, 'name' => 'Delivery Notes Completed', 'module_id' => 9),
            array('id' => 67, 'name' => 'Return Notes Completed', 'module_id' => 9),
            array('id' => 68, 'name' => 'Outstanding Shipments', 'module_id' => 9),
            array('id' => 69, 'name' => 'Lead Time', 'module_id' => 9),
            array('id' => 70, 'name' => 'Quality of Service', 'module_id' => 9),
            array('id' => 71, 'name' => 'Quality Assurance', 'module_id' => 9),
            array('id' => 72, 'name' => 'Customer Retention Rate', 'module_id' => 9),
            array('id' => 73, 'name' => 'Daily Pickup and Sales', 'module_id' => 9),
            array('id' => 74, 'name' => 'Monthwise Customer Sales', 'module_id' => 9),
            array('id' => 75, 'name' => 'Overall Sales', 'module_id' => 9),

            array('id' => 76, 'name' => 'Stock - View', 'module_id' => 10),
            array('id' => 77, 'name' => 'Stock - Add', 'module_id' => 10),
            array('id' => 78, 'name' => 'Stock - Send', 'module_id' => 10),
            array('id' => 79, 'name' => 'Requests - View', 'module_id' => 10),
            array('id' => 80, 'name' => 'Requests - Dispatch', 'module_id' => 10),

            array('id' => 81, 'name' => 'Users - View', 'module_id' => 11),
            array('id' => 82, 'name' => 'Users - Add', 'module_id' => 11),
            array('id' => 83, 'name' => 'Users - Update', 'module_id' => 11),
            array('id' => 84, 'name' => 'Users - Enable/Disable', 'module_id' => 11),
            array('id' => 85, 'name' => 'Roles - View', 'module_id' => 11),
            array('id' => 86, 'name' => 'Roles - Add', 'module_id' => 11),
            array('id' => 87, 'name' => 'Roles - Update', 'module_id' => 11),

            array('id' => 88, 'name' => 'City Management - View', 'module_id' => 12),
            array('id' => 89, 'name' => 'City Management - Add', 'module_id' => 12),
            array('id' => 90, 'name' => 'City Management - Update', 'module_id' => 12),
            array('id' => 91, 'name' => 'City Management - Enable/Disable', 'module_id' => 12),
            array('id' => 92, 'name' => 'Route Management - View', 'module_id' => 12),
            array('id' => 93, 'name' => 'Route Management - Add', 'module_id' => 12),
            array('id' => 94, 'name' => 'Route Management - Update', 'module_id' => 12),
            array('id' => 95, 'name' => 'Route Management - Enable/Disable', 'module_id' => 12),
            array('id' => 96, 'name' => 'Rider Management - View', 'module_id' => 12),
            array('id' => 97, 'name' => 'Rider Management - Add', 'module_id' => 12),
            array('id' => 98, 'name' => 'Rider Management - Update', 'module_id' => 12),
            array('id' => 99, 'name' => 'Rider Management - Enable/Disable', 'module_id' => 12),

            array('id' => 100, 'name' => 'View', 'module_id' => 13),
            array('id' => 101, 'name' => 'Update', 'module_id' => 13),
            array('id' => 102, 'name' => 'Enable/Disable', 'module_id' => 13),
            array('id' => 103, 'name' => 'Send Custom Email', 'module_id' => 13),

            array('id' => 104, 'name' => 'Pickup Weight', 'module_id' => 14),

            array('id' => 105, 'name' => 'Cash Collection - View', 'module_id' => 6),
            array('id' => 106, 'name' => 'Cash Collection - Collect Cash', 'module_id' => 6),
            array('id' => 107, 'name' => 'Misroute - View', 'module_id' => 6),
            array('id' => 108, 'name' => 'Misroute - Update', 'module_id' => 6)
        ));
    }
}
