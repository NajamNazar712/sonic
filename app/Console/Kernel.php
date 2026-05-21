<?php

namespace App\Console;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\SalesIncentiveDate;
use App\Http\Models\EmployeeShift;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use Carbon\Carbon;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\ReturnConfirmationPendingEmail',
        '\App\Console\Commands\CrmResponseRate',
        '\App\Console\Commands\CrmClosedReasonCron',
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
        'App\Console\Commands\DonePaymentReport',
        'App\Console\Commands\PasswordUpdateForAdminUser',
        'App\Console\Commands\NotPickedShipmentsJourney',
        'App\Console\Commands\LastMileStatusReport',
        'App\Console\Commands\ShipperPaymentCalculation',
        'App\Console\Commands\ReturnSheetReceive',
        'App\Console\Commands\RiderDeactivateAutomatically',
        'App\Console\Commands\RevenueReportMonthlyEmail',
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
        'App\Console\Commands\LeaveCountUpdate',
        'App\Console\Commands\EmployeeConfirmationDays',
        'App\Console\Commands\MonthAverageDestinationReportEmail',
        'App\Console\Commands\ReversionDeliveredShipments',
        'App\Console\Commands\RevenueReportCutOffDays',
        'App\Console\Commands\RevenueReportByDeliveryDateCutOffDays',
        'App\Console\Commands\RevenueReportRemainingDays',
        'App\Console\Commands\RevenueReportByDeliveryDateRemainingDays',
        'App\Console\Commands\RevenueReportDailyBasis',
        'App\Console\Commands\RetailSalesReport',
        'App\Console\Commands\RetailSalesReportCutOffDays',
        'App\Console\Commands\RetailSalesReportRemainingDays',
        'App\Console\Commands\RetailSalesReportByDeliveryDate',
        'App\Console\Commands\RetailSalesReportByDeliveryCutOffDays',
        'App\Console\Commands\RetailSalesReportByDeliveryRemainingDays',
        'App\Console\Commands\DailyAutoCommentForCRMClaims',
        'App\Console\Commands\WeeklyAttendanceSummaryLineManager',
        'App\Console\Commands\LateEmployeePenalty',
        'App\Console\Commands\AttendanceAdjustmentShiftWise',
        'App\Console\Commands\RiderFuelAllocationDeliveryNoteCalculation',
        'App\Console\Commands\VisionSoftApiExcel',
        'App\Console\Commands\CreateInvoiceOriginWise',
        'App\Console\Commands\InvalidEmailVisit',
        'App\Console\Commands\NotificationReturnedDeliveredToShipper',
        // 'App\Console\Commands\AgentUnassignedTicket ',
        'App\Console\Commands\AgentSarNotification',
        'App\Console\Commands\BotCallInitiate',
        'App\Console\Commands\SackBagStatusUpdate',
        'App\Console\Commands\AutoAssignCrmAgentNew',
        // 'App\Console\Commands\ShipperLogisticBookingCron',
        // 'App\Console\Commands\HourlyShipperLogisticBookingEmailCron'

        'App\Console\Commands\CalculateFranchiseCommission',
        'App\Console\Commands\DeleteOldDataFromShortUrlTable',
        'App\Console\Commands\RestartSupervisordProcesses',
        'App\Console\Commands\UpdateArrivalChargesCommand',
        '\App\Console\Commands\RetryJobsInRange',
        '\App\Console\Commands\ForceFullyBotCallInitiate',
        '\App\Console\Commands\MissingFirstCallInitiate',
        '\App\Console\Commands\lastMileAppReportCountUpdate',
        '\App\Console\Commands\RunSpecificJob',
        '\App\Console\Commands\DeleteDuplicateArrival',
        '\App\Console\Commands\UpdateInvoiceChargesMonthly',
        '\App\Console\Commands\UpdateArrivalChargesIssue',
        \App\Console\Commands\AddMissingSegmentLogs::class,
        '\App\Console\Commands\ApolloShipmentFetchStatus',
        '\App\Console\Commands\FinSurgentSonicPaymentSharing',
        '\App\Console\Commands\FailedStatusRePushToWallet',
        '\App\Console\Commands\RerunWalletSettlement',
        '\App\Console\Commands\BulkStatusSharingWithWallet',
        '\App\Console\Commands\WalletUsersMakeToDonePayments',
        '\App\Console\Commands\UpdateShipmentAdditionalCharges',
        'App\Console\Commands\ReceiveDeliveriesReportNew',
        'App\Console\Commands\ReceiveReturnDeliveries',
        'App\Console\Commands\DailyDeliveryNoteHistory',
        'App\Console\Commands\DailyWeightQCReport',
        'App\Console\Commands\DailyOverAllSalesReport',
        'App\Console\Commands\ReceiveDeliveriesReport',

        'App\Console\Commands\QualityOfServiceReport',
        'App\Console\Commands\PendingDeliveriesReportNew',
        'App\Console\Commands\WalletChargesUpdate',
        'App\Console\Commands\BulkStatusSharingWithWalletReplicate',
        'App\Console\Commands\ExportShipmentReport',
        'App\Console\Commands\OptimizeTable',
        'App\Console\Commands\TicketDraftingCRM',
       'App\Console\Commands\UpdateRvShipments',
        'App\Console\Commands\UpdatePendingBanks',
        'App\Console\Commands\DailyOverAllSalesReportKhaddi',
        'App\Console\Commands\ArchiveBookingApiLogs',
        'App\Console\Commands\DeleteDuplicateDonePayment',
        // 'App\Console\Commands\QsrEmail',
        // 'App\Console\Commands\PendingDeliveriesReport',
        'App\Console\Commands\ExpectedShipmentNotMeetPenaltyCharges',
        'App\Console\Commands\RemoveExpiredZeroCodShippers',
        'App\Console\Commands\SyncS3ToMinio',
        '\App\Console\Commands\NonWalletMakeToDonePayment',
        'App\Console\Commands\AutoRejectLeaves',
        'App\Console\Commands\MoveDailyPayableRecordsToLogsTable',

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('bulk:status-sharing-wallet-replicate')->everyTenMinutes()->runInBackground();

        $schedule->command('create:service_ledger')->dailyAt('00:00')->runInBackground();
        // $schedule->command('job:run email 25000')->dailyAt('02:02')->runInBackground();
        $schedule->command('corporate_reimbursement_setting:update')->monthlyOn(1, '00:15')->runInBackground();

        $schedule->command('email:dailyfakestatusreport')->dailyAt('06:00')->runInBackground();
        $schedule->command('crm:response_rate')->dailyAt('18:00')->runInBackground();

        // $schedule->command('email:inactiverideronroutereport')->dailyAt('19:36')->runInBackground();
        $schedule->command('inactive_employee:resign_date')->dailyAt('05:00')->runInBackground();
        $schedule->command('saleperson:numbers')->dailyAt('06:00')->runInBackground();
        $schedule->command('month:average')->dailyAt('06:00')->runInBackground();
        $schedule->command('hubwise:split')->dailyAt('06:00')->runInBackground();
        $schedule->command('lastmile:countupdate')->dailyAt('06:30')->runInBackground();
        $schedule->command('count:pendingpaymentshipments')->dailyAt('06:00')->runInBackground();
        $schedule->command('crm:closed_reason')->dailyAt('23:50')->runInBackground();
        $schedule->command('crm:progress_report')->dailyAt('23:57')->runInBackground();
        $schedule->command('email:onholdshipments')->dailyAt('06:00')->runInBackground();
        $schedule->command('shipper:payment')->twiceDaily('01','13')->runInBackground();
        $schedule->command('email:dailyvisitweeklyreport')->weeklyOn(1, '6:00')->runInBackground();
        $schedule->command('email:invalidemailvisit')->dailyAt('6:00')->runInBackground();
        $schedule->command('month:average-destination')->dailyAt('06:00')->runInBackground();
        $schedule->command('reversion_delivered:report')->dailyAt('04:00')->runInBackground();
        $schedule->command('email:weeklyattendancesummary')->weeklyOn(1,'09:00')->runInBackground();
        $schedule->command('employee:penalty')->monthlyOn(21,'08:00')->runInBackground();

        // Sackback or Canvas schedule
        $schedule->command('sackbag:statusupdate')->dailyAt('06:00')->runInBackground();

        //Operations Report
        //11th of every month
        $schedule->command('reports:operations_performance_monthly')->monthlyOn(11, '23:00')->runInBackground();
        //EveryTuesday
        $schedule->command('reports:operations_performance_weekly')->weeklyOn(2, '10:00')->runInBackground();
        // $schedule->command('reports:operations_performance_weekly test')->weeklyOn(1, '15:00')->runInBackground();

        $shifts = EmployeeShift::whereIn('id', [2,3,4,5,6])->get();
        if($shifts){
            foreach($shifts as $shift)
            {
                // run 1 hour before from the shift ends, to get save from the next day switch as well
                $dailyAt = Carbon::parse($shift->end_time)->subHour(1)->format('H:i:s');
                $schedule->command('employee:attendanceadjustment', [$shift->id, 'web'])
                    ->dailyAt($dailyAt)
                    ->runInBackground();

                // run after 30 mins from the shift starts, to notify employee to mark attendance if forgets
                $dailyAt = Carbon::parse($shift->start_time)->addMinutes(30)->format('H:i:s');
                $schedule->command('employee:attendanceadjustment', [$shift->id, 'app'])
                    ->dailyAt($dailyAt)
                    ->runInBackground();
            }
        }
        //rv agent cron jobs start
        // $employee_shifts = EmployeeShift::where('shift_type_id', 2)->get();
        // if(count($employee_shifts)){
        //     foreach($employee_shifts as $employee_shift)
        //     {
        //         // run after 30 mins from the employee_shift ends, to unassign ticket from the contractual employees
        //         $dailyAt = Carbon::parse($employee_shift->end_time)->addMinutes(30)->format('H:i:s');
        //         $schedule->command('agent:unassignedTicket')->dailyAt('22:30')->runInBackground();
        //     }
        // }

        // $schedule->command('agent:changeStatus')->everyFiveMinutes()->withoutOverlapping()->runInBackground();
        $schedule->command('agent:changeStatus')->twiceDaily('22','00')->runInBackground(); // Dailt at 9:55

        //SarNotification Email Cron
        $agent_sar_settings = GlobalSettings::where('type', 'agent_sar_notification');
        if ($agent_sar_settings->exists()) {
            $sar_setting = $agent_sar_settings->first();
            $agent_sar_notify_time = $sar_setting->setting_value . ':00';
        }else{
            $agent_sar_notify_time = '23:30'; //1:30 am
        }
        $schedule->command('agent:sarnotification')->dailyAt($agent_sar_notify_time)->runInBackground();

        //rv agent cron jobs end

        // rv cron job for the call every two hours execute
        $checkBot = GlobalSettings::where(['type' => 'bot_call_enable_disable', 'setting_value' => 1])->exists();
        if($checkBot)
        {
            $schedule->command('agent:botcallunresponsive')->everyFifteenMinutes()->runInBackground();
           $schedule->command('missingfirst:call')->hourly()->runInBackground();
           $schedule->command('missingfirst:call', [
               '--start' => Carbon::yesterday()->startOfDay()->toDateTimeString(),
               '--end' => Carbon::yesterday()->endOfDay()->toDateTimeString()
           ])
               ->dailyAt('00:30') // runs at 12:30 AM every night
               ->runInBackground();
        }

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

        $schedule->command('archive:returnnoteimage')->dailyAt('02:00')->runInBackground();

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
        $schedule->command('report:retaildonepayment')->dailyAt('17:45')->runInBackground();

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

        // $schedule->command('email:pendingdeliveryreport')->dailyAt('01:00')->runInBackground();
        // $schedule->command('email:receivedeliveryreport')->dailyAt('01:00')->runInBackground();

        $schedule->command('website:leads')->everyFiveMinutes()->runInBackground();
//        $schedule->command('website:pamleads')->hourly()->runInBackground();

        $schedule->command('generate:usersotp')->monthlyOn(1, '00:00')->runInBackground();
//        $schedule->command('email:revenuereport')->monthlyOn(1, '00:00')->runInBackground();
        $schedule->command('email:revenuereport')->monthlyOn(11, '01:00')->runInBackground();
        $schedule->command('email:revenuereportcutoffdays')->monthlyOn(21, '00:00')->runInBackground();
        $schedule->command('email:revenuereportremainingdays')->monthlyOn(1, '00:00')->runInBackground();
        $schedule->command('email:RevenueReportDailyBasis')->dailyAt('06:00')->runInBackground();


        $schedule->command('email:revenuereportbydeliverydate')->monthlyOn(11, '01:00')->runInBackground();
        $schedule->command('email:revenuereportbydeliverycutoffdays')->monthlyOn(21, '00:00')->runInBackground();
        $schedule->command('email:revenuereportbydeliveryremainingdays')->monthlyOn(1, '00:00')->runInBackground();

        $schedule->command('email:retailsalesreport')->monthlyOn(11, '01:00')->runInBackground();
        $schedule->command('email:retailsalesreportcutoffdays')->monthlyOn(21, '00:00')->runInBackground();
        $schedule->command('email:retailsalesreportremainingdays')->monthlyOn(1, '00:00')->runInBackground();


        $schedule->command('email:retailsalesreportbydeliverydate')->monthlyOn(11, '01:00')->runInBackground();
        $schedule->command('email:retailsalesreportbydeliverycutoffdays')->monthlyOn(21, '00:00')->runInBackground();
        $schedule->command('email:retailsalesreportbydeliveryremainingdays')->monthlyOn(1, '00:00')->runInBackground();
//        $schedule->command('verify:usersotp')->monthlyOn(15, '00:00')->runInBackground();
        $schedule->command('auto:birthdaymessage')->dailyAt('00:00')->runInBackground();

        $settings = GlobalSettings::where('type', 'rider_incentive_cron_time');
        if ($settings->exists()) {
            $settings = $settings->first();
            $cut_off_time = $settings->setting_value . ':00';
            $schedule->command('incentive:riders')->dailyAt($cut_off_time)->runInBackground();
        }

        /*$settings = GlobalSettings::where('type', 'dhl_sync_time_1');
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
        }*/

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
//        $schedule->command('crm:autoassign')->dailyAt('17:00')->runInBackground();
        $schedule->command('crm:autoassign_new')->dailyAt('17:00')->runInBackground();
        $schedule->command('crm:autoassign_new')->dailyAt('08:00')->runInBackground();

        $schedule->command('sum:pendingpayments')->dailyAt('6:00')->runInBackground();

        $schedule->command('employee_directory:documents_update')->dailyAt('12:00')->runInBackground();

        $schedule->command('email:dwsarrival')->dailyAt('17:00')->runInBackground();
        $schedule->command('crm:count')->dailyAt('17:30')->runInBackground();
        $schedule->command('crm:autohighaging')->dailyAt('09:00')->runInBackground();
        $schedule->command('shipper:short_of_business')->dailyAt('8:00')->runInBackground();
        $schedule->command('calculate:reattemptpercentage')->dailyAt('19:30')->runInBackground();

        $cron = DB::table('global_settings')->where('type','rcp_sms_cron_time')->select('text')->first();
        $cron_time = isset($cron->text) ? $cron->text : "12:00";
        $schedule->command('sms:rcp_sms_to_consignee_reattempt')->dailyAt($cron_time)->runInBackground();

        $schedule->command('employee:leave_count')->monthlyOn(1, '00:00')->runInBackground();
        $every_first_july = '0 0 1 7 *';
        $schedule->command('employee:leave_count_fiscal')->cron($every_first_july)->runInBackground();
        $schedule->command('employee:confirmation_days')->dailyAt('09:00')->runInBackground();


        $schedule->command('comment:dailycrmclaimshipments')->dailyAt('14:00')->runInBackground();
        $schedule->command('rider:fuel_allocation')->dailyAt('04:00')->runInBackground();
        $schedule->command('api:visionsoftexcel')->dailyAt('07:00')->runInBackground();

        $auto_delivery_note_time = GlobalSettings::where('type', 'delivery_note_auto_verification_time');
        if ($auto_delivery_note_time->exists()) {
            $auto_delivery_note_time = $auto_delivery_note_time->first();
            $hour = $auto_delivery_note_time->text;
            $schedule->command('auto:deliverynoteverification')->dailyAt($hour)->runInBackground();
        }


        $schedule->command('invoice:revenueoriginwise')->weeklyOn(7, '1:00')->runInBackground();

        $schedule->command('sms:returned_delivered_sms')->dailyAt('11:00')->runInBackground();
//		$schedule->command('email:qsrreport')->dailyAt('10:01')->runInBackground(); //ye filhal bnd ki hai due to r2 shutdown issue
        $schedule->command('email:pendingdeliveriesreport')->dailyAt('09:01')->runInBackground();
        $schedule->command('clean:7DaysOlderQrsPDReportStorage')->dailyAt('06:00')->runInBackground();

        // Commission calculation schedule
        $schedule->command('commission:calculate_commission')->monthlyOn(1, '00:00')->runInBackground();
//        $schedule->command('logistic:shipper-bookings')->dailyAt('06:00')->runInBackground();
//        $schedule->command('hourly-logistic:shipper-bookings')->hourly()->runInBackground();
        $schedule->command('delete:short-url-data')->dailyAt('01:00')->runInBackground();
        $schedule->command('supervisord:restart')
//         ->cron('0 9,13,16 * * *')
            ->hourly()
            ->runInBackground();

        $schedule->command('update:zero_arrival_charges')->everyTwoHours()->runInBackground();
        $schedule->command('delete:duplicate_arrival')->hourly()->runInBackground();
        $schedule->command('delete:duplicate_done_delivered')->everyThirtyMinutes()->runInBackground();
        $schedule->command('update_corporate_invoice_charges_issue')->hourly()->runInBackground();
        $schedule->command('update:pending_payment_shipment_arrival_charges')->hourly()->runInBackground();
//        $schedule->command('storage:amazon')->dailyAt('15:05')->runInBackground();

        $schedule->command('email:revenuereport_lastmonth 1')->dailyAt('14:00')->runInBackground();
        $schedule->command('email:revenuereport_lastmonth 2')->dailyAt('14:15')->runInBackground();
        $schedule->command('email:revenuereport_lastmonth 3')->dailyAt('14:30')->runInBackground();

        $schedule->command('update:shipper_segment_logs')->everyFiveMinutes()->runInBackground();
//        $schedule->command('apollo:fetch-shipments-status')->everyFifteenMinutes()->runInBackground();
//        $schedule->command('apollo:fetch-shipments-status')->dailyAt('15:36')->runInBackground();
        $schedule->command('apollo:fetch-shipments-status')->everyThreeHours()->runInBackground();

        $schedule->command('fingsurgent:sonic-payment')->hourly()->runInBackground(); //wallet
//        $schedule->command('status:re-push-wallet')->hourly()->runInBackground(); // wallet no need now after bulk status work
        $schedule->command('rerun:wallet_log_re_push')->hourly()->runInBackground(); // wallet
        $schedule->command('rerun_wallet_settlement')->everySixHours()->runInBackground(); //wallet
        // $schedule->command('bulk:status-sharing-wallet')->withoutOverlapping()->everyFiveMinutes()->runInBackground();
//        $schedule->command('api:visionsoftexcel_multiple')->dailyAt('20:01')->runInBackground();


        // $schedule->command('email:daily_received_deliveries_report')->dailyAt('09:00')->runInBackground();
        // $schedule->command('email:daily_return_received_deliveries_report')->dailyAt('09:00')->runInBackground();
        // $schedule->command('email:daily_delivery_note_history')->dailyAt('09:00')->runInBackground();
        // $schedule->command('email:daily_weight_qc_report')->dailyAt('09:00')->runInBackground();
        // $schedule->command('email:daily_overall_sales_report')->dailyAt('09:00')->runInBackground();
        // $schedule->command('email:qsrreport')->dailyAt('09:00')->runInBackground();
        // $schedule->command('email:qsrreport')->dailyAt('14:00')->runInBackground();
        // $schedule->command('email:pendingdeliveryreport')->dailyAt('11:30')->runInBackground();

        // $schedule->command('email:receivedeliveryreport')->dailyAt('09:00')->runInBackground();

        $settings = DB::table('global_settings')
        ->whereIn('type', [
            'pending_deliveries_report_time',
            'receive_deliveries_report_time',
            'receive_return_deliveries_report_time',
            'delivery_note_history_report_time',
            'weight_qc_report_time',
            'overall_sales_report_time',
            'quality_of_service_report_time',
            'quality_of_service_report_other_time'
        ])
        ->get()
        ->keyBy('type');

        $commands = [
            'email:daily_received_deliveries_report' => ['receive_deliveries_report_time'],
            'email:daily_return_received_deliveries_report' => ['receive_return_deliveries_report_time'],
            'email:daily_delivery_note_history' => ['delivery_note_history_report_time'],
            'email:daily_weight_qc_report' => ['weight_qc_report_time'],
            'email:daily_overall_sales_report' => ['overall_sales_report_time'],
            'email:pending_deliveries_report' => ['pending_deliveries_report_time'],
            'email:quality_of_service_report' => [
                'quality_of_service_report_time',
                'quality_of_service_report_other_time',
            ],
        ];

        foreach ($commands as $command => $settingKeys) {
            foreach ((array) $settingKeys as $settingKey) {
                if (isset($settings[$settingKey]) && $settings[$settingKey]->setting_value == 1) {
                    $timeRaw = trim($settings[$settingKey]->text ?? '');
                    $time = Carbon::createFromFormat('h:i A', $timeRaw)->format('H:i');
                    $schedule->command($command)->dailyAt($time)->runInBackground();
                }
            }
        }
        $schedule->command('update:shipment_additional_charges')->withoutOverlapping()->daily()->runInBackground();
        $schedule->command('wallet-users:make-to-done')->dailyAt('13:25')->runInBackground();
        $schedule->command('revenue_report_by_user_excel')->dailyAt('16:11')->runInBackground();
        $schedule->command('disable_wallet_users')->twiceDaily('13','18')->runInBackground();
        $schedule->command('ticketdraft:crm')
            ->dailyAt('00:15') // runs at 12:15 AM every night
            ->runInBackground();
        $walletChargesUpdate = GlobalSettings::where(['type' => 'wallet_charges_updated', 'setting_value' => 1])->first();
        if ($walletChargesUpdate) {
            $time = $walletChargesUpdate->text; // e.g., '11:00'
            $schedule->command('wallet_charges_update')->dailyAt($time)->runInBackground();
        }
        $schedule->command('shipment:delete_journey')
            ->when(function () {
                // Only run at exactly 6:00 AM on 2nd August 2025
                return Carbon::now()->format('Y-m-d H:i') === '2025-08-15 22:15';
            })
            ->withoutOverlapping();
        $schedule->command('sync:latest-shipments')
            ->when(function () {
                // Only run at exactly 6:00 AM on 2nd August 2025
                return Carbon::now()->format('Y-m-d H:i') === '2025-09-23 03:00';
            })
            ->withoutOverlapping();
        $schedule->command('db:optimize-table')
            ->when(function () {
                // Only run at exactly 6:00 AM on 2nd August 2025
                return Carbon::now()->format('Y-m-d H:i') === '2025-08-10 13:00';
            })
            ->withoutOverlapping();
        // $schedule->command('banks:update-pending')->dailyAt('23:00');
        // $schedule->command('export:shipment-report')
        //     ->dailyAt('14:46')              
        //     ->withoutOverlapping()         // prevent simultaneous runs
        //     ->onOneServer()                // ensures single server execution
        //     ->runInBackground()            // runs non-blocking
        //     ->sendOutputTo(storage_path('logs/shipment_report.log'))
        //     ->emailOutputOnFailure('anas.mazhar@logiserves.com');
        $schedule->command('email:daily_overall_sales_report_khaddi')->dailyAt('09:00')->runInBackground();
        $schedule->command('shipments:update-rv-sar')->dailyAt('05:00');
        $schedule->command('logs:archive-booking-api')->dailyAt('03:00');
        $schedule->command('penalty:expected_shipments_not_meet')->monthlyOn(1, '00:00')->runInBackground();
        $schedule->command('shippers:remove-expired')->everyFiveMinutes()->withoutOverlapping()->runInBackground();
        // $schedule->command('sync:s3-minio 2026-03-05')
        //     ->cron('0 3 5 3 *')
        //     ->withoutOverlapping();
         $schedule->command('non-wallet-users:make-to-done-new-file')->dailyAt('05:00')->withoutOverlapping()->runInBackground();
        $schedule->command('auto:reject-leaves')->everyFiveMinutes();
        $schedule->command('dump:daily-payable-records-to-logs-table')->everyThreeHours()->withoutOverlapping()->runInBackground();

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