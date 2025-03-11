<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TelenorApiErrorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('telenor_api_errors')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('telenor_api_errors')->insert(array(
            array('id' => 1, 'code' => 'Error 001', 'text' => 'Session not found', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 2, 'code' => 'Error 002', 'text' => 'Failed to generate session', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 3, 'code' => 'Error 200', 'text' => 'Failed login. Username and password do not match', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 4, 'code' => 'Error 201', 'text' => 'Unknown MSISDN, Please Check Format i.e. 92345xxxxxxx', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 5, 'code' => 'Error 100', 'text' => 'Out of credit.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 6, 'code' => 'Error 101', 'text' => 'Field or input parameter missing', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 7, 'code' => 'Error 102', 'text' => 'Invalid session ID or the session has expired. Login again.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 8, 'code' => 'Error 103', 'text' => 'Invalid Mask', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 9, 'code' => 'Error 104', 'text' => 'Invalid operator ID', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 10, 'code' => 'Error 204', 'text' => 'Sub user permission not allowed', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 11, 'code' => 'Error 211', 'text' => 'Unknown Message ID', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 12, 'code' => 'Error 300', 'text' => 'Account has been blocked/suspended', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 13, 'code' => 'Error 400', 'text' => 'Duplicate list name.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 14, 'code' => 'Error 401', 'text' => 'List name is missing.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 15, 'code' => 'Error 411', 'text' => 'Invalid MSISDN in the list.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 16, 'code' => 'Error 412', 'text' => 'List ID is missing.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 17, 'code' => 'Error 413', 'text' => 'No MSISDNs in the list.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 18, 'code' => 'Error 414', 'text' => 'List could not be updated. Unknown error.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 19, 'code' => 'Error 415', 'text' => 'Invalid List ID.. Unknown error.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 20, 'code' => 'Error 500', 'text' => 'Duplicate campaign name.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 21, 'code' => 'Error 501', 'text' => 'Campaign name is missing.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 22, 'code' => 'Error 502', 'text' => 'SMS text is missing.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 23, 'code' => 'Error 503', 'text' => 'No list selected or one of the list IDs is invalid.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 24, 'code' => 'Error 504', 'text' => 'Invalid schedule time for campaign.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 25, 'code' => 'Error 506', 'text' => 'Cannot send message at the specified time. Please specify a different time.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 26, 'code' => 'Error 507', 'text' => 'Campaign could not be saved. Unknown Error.', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 27, 'code' => 'Error 600', 'text' => 'Campaign ID is missing', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 28, 'code' => 'Error 700', 'text' => 'File ID is missing', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 29, 'code' => 'Error 701', 'text' => 'File not available or not ready', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 30, 'code' => 'Error 702', 'text' => 'Invalid value for max retries', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 31, 'code' => 'Error 703', 'text' => 'Invalid value for Call ID', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 32, 'code' => 'Error 704', 'text' => 'Invalid Mask for IVR', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 33, 'code' => 'Error 301', 'text' => 'Incoming SMS feature is not available for current user', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 34, 'code' => 'Error 302', 'text' => 'In valid action attribute value', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 35, 'code' => 'Error 303', 'text' => 'User has entered date and is not valid date', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 36, 'code' => 'Error 304', 'text' => 'API throughput limit reached for TPS Control mode', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 37, 'code' => 'Error 305', 'text' => 'User SMS/recipients exceeds than allowed throughput', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 38, 'code' => 'Error 402', 'text' => 'Invalid list name', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 39, 'code' => 'Error 508', 'text' => 'Invalid start time for voice campaign', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 40, 'code' => 'Error 509', 'text' => 'Invalid end time for voice campaign', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 41, 'code' => 'Error 510', 'text' => 'Invalid end Date in campaign', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 42, 'code' => 'Error 511', 'text' => 'Invalid campaign name', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 43, 'code' => 'Error 512', 'text' => 'Message Text for voice campaign length greater than allowed length', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 44, 'code' => 'Error 601', 'text' => 'Invalid campaign ID for voice campaign', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 45, 'code' => 'Error 602', 'text' => 'Filename missing for audio upload', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 46, 'code' => 'Error 603', 'text' => 'Invalid audio File name already exists', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 47, 'code' => 'Error 604', 'text' => 'Invalid request File not uploaded', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 48, 'code' => 'Error 605', 'text' => 'Audio File larger than size allowed', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 49, 'code' => 'Error 606', 'text' => 'Invalid File Encoding', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 50, 'code' => 'Error 607', 'text' => 'Invalid file audio channels', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 51, 'code' => 'Error 608', 'text' => 'Invalid file audio sample rate', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 52, 'code' => 'Error 609', 'text' => 'Invalid file audio bit rate', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 53, 'code' => 'Error 610', 'text' => 'File not uploaded unknown error', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 54, 'code' => 'Error 611', 'text' => 'Invalid File extension', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 55, 'code' => 'Error 612', 'text' => 'Invalid recording name', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 56, 'code' => 'Error 705', 'text' => 'DTMF valid options not provided or invalid', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 57, 'code' => 'Error 706', 'text' => 'File not available or not ready to be used for valid feedback option', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 58, 'code' => 'Error 707', 'text' => 'File not available or not ready to be used for Invalid feedback option', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 59, 'code' => 'Error 708', 'text' => 'Dynamic Campaign language option missing or invalid', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 60, 'code' => 'Error 709', 'text' => 'Dynamic Campaign pronunciation option missing or invalid', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 61, 'code' => 'Error 710', 'text' => 'Dynamic Campaign voice gender option missing or invalid', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 62, 'code' => 'Error 711', 'text' => 'File not available or not ready to be used for Ending recording in Dynamic IVR', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 63, 'code' => 'Error 712', 'text' => 'Invalid Audio File ID', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
            array('id' => 64, 'code' => 'Error 713', 'text' => 'Audio File not ready', 'created_at'=>$timestamp, 'updated_at'=>$timestamp),
        ));
    }
}
