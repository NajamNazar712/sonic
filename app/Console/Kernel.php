<?php

namespace App\Console;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\SalesIncentiveDate;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\ReturnConfirmationPendingEmail',
        '\App\Console\Commands\ReturnConfirmEmail',
        '\App\Console\Commands\ShipmentReAttemptEmail',
        '\App\Console\Commands\AutoDisableShipperAccount',
        '\App\Console\Commands\DailyPickupSalesEmail',
        '\App\Console\Commands\GenerateInvoice',
        '\App\Console\Commands\ClearSMS',
        '\App\Console\Commands\OperationForecastHourlyUpdate',
        '\App\Console\Commands\ReturnNoteImageArchive',
        '\App\Console\Commands\StationDepositNoteImageArchive',
        '\App\Console\Commands\ReturnDeliveredToShipperEmail',
        '\App\Console\Commands\DebriefingEmail',
        '\App\Console\Commands\ClearPickupRequest',
        '\App\Console\Commands\ClearPickupNote',
        '\App\Console\Commands\SalePersonShipmentNumbers',
        '\App\Console\Commands\MonthAverageReportEmail',
        '\App\Console\Commands\HubWiseSplitEmail',
        '\App\Console\Commands\PettyCashImageArchive',
        '\App\Console\Commands\DailyFakeStatusReportEmail',
        '\App\Console\Commands\NegativeBalanceShipperSalesPerson',
        '\App\Console\Commands\ClearDefaultBankDuration',
//        '\App\Console\Commands\OvernightCargoReport',
//        '\App\Console\Commands\OverlandCargoReport',
        '\App\Console\Commands\AccountReconciliationReportFromStart',
        '\App\Console\Commands\AccountReconciliationReportCurrent',
        '\App\Console\Commands\BusinessProjectionAndRetention',
        '\App\Console\Commands\CRMDelayInDelivery',
        '\App\Console\Commands\CRMPaymentComplains',
        '\App\Console\Commands\RiderDeliveryImageArchive',
        '\App\Console\Commands\BlacklistConsigneeRatioCalculation',
        '\App\Console\Commands\CancelledShipmentEmail',
        '\App\Console\Commands\ArrivalAutoNotPicked',
        '\App\Console\Commands\PickupCancel',
        '\App\Console\Commands\PickupRegenerate',
        '\App\Console\Commands\PickupReport',
        '\App\Console\Commands\CancelledPickupRequestEmail',
        '\App\Console\Commands\CompletedAgingReport',
        '\App\Console\Commands\PendingCashCollectionReport',
        '\App\Console\Commands\ZeroChargesReport',
        '\App\Console\Commands\StationRecoveryReport',
        '\App\Console\Commands\V2PickupCleanDuplicateData',
        '\App\Console\Commands\QAReportPettyCash',
        '\App\Console\Commands\SelfCollection',
        '\App\Console\Commands\OutstandingShipmentEmail',
        'App\Console\Commands\ShipmentPieceOnHold',
        'App\Console\Commands\PendingPaymentShipmentsCount',
        'App\Console\Commands\VisionSoftApi',
        'App\Console\Commands\OutstandingSDNReport',
        'App\Console\Commands\TelenorSalesReport',
        'App\Console\Commands\KeyAccountDashboard',
        'App\Console\Commands\ReversePickupSummary',
        'App\Console\Commands\OverallVendorPickup',
        'App\Console\Commands\NotPickedShippersSummary',
        'App\Console\Commands\Escalation',
        'App\Console\Commands\EscalationTagging',
        //'App\Console\Commands\TelenorCall',
        //'App\Console\Commands\TelenorCallResponse',
        'App\Console\Commands\OnHoldShipmentEmail',
        'App\Console\Commands\OverlandAgingReport',
        'App\Console\Commands\PendingDeliveriesReport',
        'App\Console\Commands\ReceiveDeliveriesReport',
        'App\Console\Commands\WebsiteLead',
        'App\Console\Commands\UserOTPGenerate',
        'App\Console\Commands\UserOTPVerifiy',

        'App\Console\Commands\DailyPickupSalesIndividualEmail',
        'App\Console\Commands\DailyPickupSalesRMEmail',

        'App\Console\Commands\SalePersonShipmentNumbersRM',
        'App\Console\Commands\SalePersonShipmentNumbersIndividual',

        'App\Console\Commands\MonthAverageIndividual',
        'App\Console\Commands\MonthAverageRM',
        'App\Console\Commands\RiderIncentiveCalculate',
        'App\Console\Commands\DHLTrackingSync',
        'App\Console\Commands\StatusBookedEmailKhaddi',
        'App\Console\Commands\RiderWisePickupEmail',
        'App\Console\Commands\InactiveRiderReport',
        'App\Console\Commands\EmailsOfReturnConfirmToKams',
		'App\Console\Commands\RetailDonePaymentReport',
        'App\Console\Commands\PasswordUpdateForAdminUser',
        'App\Console\Commands\NotPickedShipmentsJourney',
        'App\Console\Commands\LastMileStatusReport',
		'App\Console\Commands\ShipperPaymentCalculation',
        'App\Console\Commands\ReturnSheetReceive',
        'App\Console\Commands\RiderDeactivateAutomatically',
        'App\Console\Commands\RevenueReportMonthlyByDeliveryDate',
		'App\Console\Commands\SaleIncentiveReport',
        'App\Console\Commands\AutoAssignCrmAgent',
        'App\Console\Commands\PendingPaymentCalculationJob',

        '\App\Console\Commands\EmployeeDocumentsUpdateNotification',
        'App\Console\Commands\AutoEmailDwsArrival',
        
        'App\Console\Commands\RCPSMSToConsigneeReattempt',
        'App\Console\Commands\CRMCount',
        '\App\Console\Commands\ReattemptRatioCalculate',
        'App\Console\Commands\ShortOfBusinessShippers',
        'App\Console\Commands\BirthdayMessage',
		'App\Console\Commands\AutoComplaintHighAging',
        ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('corporate_reimbursement_setting:update')->monthlyOn(1, '00:15')->runInBackground();

        $schedule->command('email:dailyfakestatusreport')->dailyAt('06:00')->runInBackground();
        // $schedule->command('email:inactiverideronroutereport')->dailyAt('19:36')->runInBackground();
        $schedule->command('saleperson:numbers')->dailyAt('06:00')->runInBackground();
        $schedule->command('month:average')->dailyAt('06:00')->runInBackground();
        $schedule->command('hubwise:split')->dailyAt('06:00')->runInBackground();
        $schedule->command('count:pendingpaymentshipments')->dailyAt('06:00')->runInBackground();
        $schedule->command('email:onholdshipments')->dailyAt('06:00')->runInBackground();
        $schedule->command('shipper:payment')->twiceDaily(1,13)->runInBackground();

        $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $arrival_cut_off_time = $settings->setting_value . ':00';
        }
        else {
            $arrival_cut_off_time = FALSE;
        }

        $settings = GlobalSettings::where('type', 'daily_pickup_sales_cron_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $daily_pickup_sales_cron_time = $settings->setting_value . ':00';
        }
        else {
            $daily_pickup_sales_cron_time = FALSE;
        }

        if ($arrival_cut_off_time) {
            $schedule->command('arrival:autonotpicked')->dailyAt($arrival_cut_off_time);
        }

        if ($daily_pickup_sales_cron_time) {
            $schedule->command('email:dailypickupsalesreport')->dailyAt($daily_pickup_sales_cron_time);
        }

        if ($arrival_cut_off_time) {
            $schedule->command('pickup:notpickedjourney')->dailyAt($arrival_cut_off_time);
            $schedule->command('pickup:autocancel')->dailyAt($arrival_cut_off_time);
            $schedule->command('pickup:regenerate')->dailyAt($arrival_cut_off_time);
            $schedule->command('pickuprequest:cancel')->dailyAt($arrival_cut_off_time);
        }

        if ($arrival_cut_off_time) {
            $schedule->command('pickup:report')->dailyAt($arrival_cut_off_time);
        }

        if ($daily_pickup_sales_cron_time) {
//            $schedule->command('email:dailypickupsalesreportrm')->dailyAt($daily_pickup_sales_cron_time)->runInBackground();
//            $schedule->command('email:dailypickupsalesreportindividual')->dailyAt($daily_pickup_sales_cron_time)->runInBackground();
//            $schedule->command('email:dailypickupsalesreportindividualforkae')->dailyAt($daily_pickup_sales_cron_time)->runInBackground();
        }

        $schedule->command('attendance:markabsent')->dailyAt('12:30')->runInBackground();
        $schedule->command('telenor:shipmentStatus')->dailyAt('08:00')->runInBackground();


        $schedule->command('email:activitytraillog')->dailyAt('2:00')->runInBackground();
        $schedule->command('sms:clear')->everyTenMinutes()->withoutOverlapping()->runInBackground();
        $schedule->command('email:returnconfirmationpending')->dailyAt('10:00')->runInBackground();
        $schedule->command('email:returnconfirm')->dailyAt('15:00')->runInBackground();
        $schedule->command('email:shipmentreattempt')->dailyAt('08:00')->runInBackground();
        $schedule->command('shipment:cancel')->dailyAt('00:00')->runInBackground();
        $schedule->command('shipper:disable')->dailyAt('00:00')->runInBackground();
        $schedule->command('email:outstandingshipments')->dailyAt('10:00')->runInBackground();
        $schedule->command('keyaccount:dashboard')->dailyAt('4:00')->runInBackground();

        $schedule->command('saleperson:numbersrm')->dailyAt('07:30')->runInBackground();
        $schedule->command('saleperson:numbersindividual')->dailyAt('07:30')->runInBackground();

        $schedule->command('month:averagerm')->dailyAt('07:30')->runInBackground();
        $schedule->command('month:averageindividual')->dailyAt('07:30')->runInBackground();
        $schedule->command('auto:endSession')->dailyAt('22:00')->runInBackground();


        $schedule->command('reimbursement_invoice:generate')->monthlyOn(1, '00:30')->runInBackground();

        $settings = GlobalSettings::where('type', 'auto_invoice_generation_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $time = $settings->setting_value . ':00';

            $schedule->command('invoice:generate')->dailyAt($time)->runInBackground();
        }

        $settings = GlobalSettings::where('type', 'not_attempted_cron_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $time = $settings->setting_value . ':00';

            $schedule->command('email:notattemptedagingreport')->dailyAt($time)->runInBackground();
        }
        // $schedule->command('hourlyupdate:operationforecast')->cron('0 */2 * * *')->withoutOverlapping()->runInBackground();
        $schedule->command('email:shortreceivedhubwise')->cron('0 * * * *')->withoutOverlapping()->runInBackground();

        $schedule->command('archive:returnnoteimage')->dailyAt('00:00')->runInBackground();

        $schedule->command('archive:stationdepositnoteimage')->dailyAt('00:00')->runInBackground();

        $schedule->command('archive:pettycashimage')->dailyAt('00:00')->runInBackground();
        $schedule->command('email:debriefingemail')->dailyAt('01:00')->runInBackground();
        $schedule->command('qareport:pettycash')->dailyAt('10:00')->runInBackground();
        $schedule->command('shipments:self_collection')->dailyAt('09:00')->runInBackground();

        $settings = GlobalSettings::where('type', 'return_delivered_to_shipper_cut_off_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $rdts_time = $settings->setting_value . ':00';

            $schedule->command('email:returndeliveredtoshipper')->dailyAt($rdts_time)->runInBackground();
        }

//        $schedule->command('pickuprequest:clear')->everyFifteenMinutes()->withoutOverlapping()->runInBackground();
//        $schedule->command('pickupnote:clear')->everyThirtyMinutes()->withoutOverlapping()->runInBackground();


        $schedule->command('email:negativebalanceshippersalesperson')->weeklyOn(1, '8:00')->runInBackground();
        $schedule->command('email:weeklyincompletedocumentsshipper')->weeklyOn(1, '8:00')->runInBackground();

//        $schedule->command('overnight:cargo_report')->dailyAt('12:00')->runInBackground();
//        $schedule->command('overland:cargo_report')->dailyAt('16:00')->runInBackground();

//		$schedule->command('accounts:reconciliationcurrent')->monthly()->days([1,14,28])->runInBackground();
//      $schedule->command('accounts:reconciliationcurrent')->cron('0 0 1,14,28 * *'); //another solution

//        $schedule->command('business:projectionandretention')->dailyAt('08:00')->runInBackground();
        $schedule->command('crm:delayindelivery')->dailyAt('08:00')->runInBackground();
        $schedule->command('crm:paymentcomplainautomation')->dailyAt('08:00')->runInBackground();

        $schedule->command('archive:riderdeliveryimage')->dailyAt('08:00')->runInBackground();

        $schedule->command('blacklist:consigneeratiocalculate')->weeklyOn(7, '5:00')->runInBackground();

        $schedule->command('shipmentemail:cancel')->dailyAt('8:00')->runInBackground();

        $schedule->command('report:donepayment')->dailyAt('17:30')->runInBackground();
        $schedule->command('report:retaildonepayment')->dailyAt('17:30')->runInBackground();

        $settings = GlobalSettings::where('type', 'completed_aging_report_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $completed_aging_report_time = $settings->setting_value . ':00';
            $schedule->command('completedAging:report')->dailyAt($completed_aging_report_time)->runInBackground();
            $schedule->command('pendingCashCollection:report')->dailyAt($completed_aging_report_time)->runInBackground();
        }

        $settings = GlobalSettings::where('type', 'zero_charges_report_time');

        if ($settings->exists()) {
            $settings = $settings->first();

            $zero_charges_report_time = $settings->setting_value . ':00';
            $schedule->command('zeroCharges:report')->dailyAt($zero_charges_report_time)->runInBackground();
        }
//        $settings = GlobalSettings::where('type', 'station_recovery_cron_time');
//
//        if ($settings->exists()) {
//            $settings = $settings->first();
//
//            $station_recovery_cron_time = $settings->setting_value . ':00';
//            $schedule->command('report:stationrecovery')->dailyAt($station_recovery_cron_time);
//        }
        $schedule->command('shipment:onholdtoshipper')->dailyAt('01:00')->runInBackground();
        $schedule->command('email:outstandingsdnreport')->dailyAt('09:00')->runInBackground();
        $schedule->command('email:telenorsalesreport')->dailyAt('09:00')->runInBackground();
//        $schedule->command('api:visionsoft')->dailyAt('04:00')->runInBackground();
        $settings = GlobalSettings::where('type', 'pickup_request_cut_off_time');
        if ($settings->exists()) {
            $settings = $settings->first();
            $cut_off_time = $settings->setting_value . ':00';
            $schedule->command('summary:reversepickup')->dailyAt($cut_off_time)->runInBackground();
            $schedule->command('overall:vendorpickup')->dailyAt($cut_off_time)->runInBackground();
        }
        $schedule->command('email:notpickedshipperssummary')->dailyAt('08:00')->runInBackground();
//        $schedule->command('telenor:call')->twiceDaily(13, 16)->runInBackground();
//        $schedule->command('telenor:callresponse')->twiceDaily(15, 18)->runInBackground();
        $schedule->command('email:overlandagingreport')->dailyAt('12:00')->runInBackground();

        $schedule->command('email:pendingdeliveryreport')->dailyAt('01:00')->runInBackground();
        $schedule->command('email:receivedeliveryreport')->dailyAt('01:00')->runInBackground();

        $schedule->command('website:leads')->hourly()->runInBackground();
        $schedule->command('website:pamleads')->hourly()->runInBackground();

        $schedule->command('generate:usersotp')->monthlyOn(1, '00:00')->runInBackground();
//        $schedule->command('email:revenuereport')->monthlyOn(1, '00:00')->runInBackground();
        $schedule->command('email:revenuereport')->monthlyOn(1, '00:00')->runInBackground();
        $schedule->command('email:revenuereportbydeliverydate')->monthlyOn(2, '01:00')->runInBackground();
//        $schedule->command('verify:usersotp')->monthlyOn(15, '00:00')->runInBackground();
        $schedule->command('auto:birthdaymessage')->dailyAt('00:00')->runInBackground();

        $settings = GlobalSettings::where('type', 'rider_incentive_cron_time');
        if ($settings->exists()) {
            $settings = $settings->first();
            $cut_off_time = $settings->setting_value . ':00';
            $schedule->command('incentive:riders')->dailyAt($cut_off_time)->runInBackground();
        }

        $settings = GlobalSettings::where('type', 'dhl_sync_time_1');
        if ($settings->exists()) {
            $settings = $settings->first();
            $time_1 = $settings->setting_value . ':00';
            $schedule->command('dhl:shipmentstatussync')->dailyAt( $time_1)->runInBackground();
        }
        $settings = GlobalSettings::where('type', 'dhl_sync_time_2');
        if ($settings->exists()) {
            $settings = $settings->first();
            $time_2 = $settings->setting_value . ':00';
            $schedule->command('dhl:shipmentstatussync')->dailyAt($time_2)->runInBackground();
        }

        $schedule->command('crm:escalation')->dailyAt('06:00')->runInBackground();
        $schedule->command('crm:escalationtagging')->dailyAt('06:00')->runInBackground();

        $schedule->command('email:shipmentbookedkhaddi')->hourly()->runInBackground();

        $schedule->command('email:riderwisepickup')->dailyAt('08:00')->runInBackground();
        $schedule->command('email:inactiveriderreport')->dailyAt('08:00')->runInBackground();
        $schedule->command('email:emailofreturnconfirmtokams')->dailyAt('03:00')->runInBackground();
        $schedule->command('returnsheet:receive')->dailyAt('05:00')->runInBackground();

        $schedule->command('sms:retry_otp')->everyMinute()->withoutOverlapping()->runInBackground();
//        $schedule->command('Reset:AdminPasswordMonthly')->monthlyOn(1, '06:00')->runInBackground();
        $schedule->command('email:RiderDeactivateAutomaticallyAndGenerateEmail')->dailyAt('03:30')->runInBackground();

        $settings = GlobalSettings::where('type', 'last_mile_cron_time');
        if ($settings->exists()) {
            $settings = $settings->first();
            $hour = $settings->setting_value;
            $hourly = '0 */'. $hour .' * * *';
            $schedule->command('report:lastmilestatus')->cron($hourly)->withoutOverlapping()->runInBackground();
        }

		//$incentive_date = SalesIncentiveDate::first();
		//        if($incentive_date){
		//            $schedule->command('report:SalesIncentive')->monthlyOn($incentive_date->cron_day, '03:00')->runInBackground();
		//        }
        $schedule->command('crm:autoassign')->dailyAt('17:00')->runInBackground();

        $schedule->command('sum:pendingpayments')->dailyAt('6:00')->runInBackground();

        $schedule->command('employee_directory:documents_update')->dailyAt('12:00')->runInBackground();

        $schedule->command('email:dwsarrival')->dailyAt('17:00')->runInBackground();
        $schedule->command('crm:count')->dailyAt('17:30')->runInBackground();
        $schedule->command('crm:autohighaging')->dailyAt('09:00')->runInBackground();
        $schedule->command('shipper:short_of_business')->dailyAt('8:00')->runInBackground();
        $schedule->command('calculate:reattemptpercentage')->dailyAt('19:30')->runInBackground();

        $cron = DB::table('rcp_sms_cron_time')->first();
        $cron_time = isset($cron->seting_value) ? $cron->seting_value : "12:00";
        $schedule->command('sms:rcp_sms_to_consignee_reattempt')->dailyAt($cron_time)->runInBackground();

    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}