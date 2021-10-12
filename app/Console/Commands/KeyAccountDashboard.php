<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\KeyAccountPendingCrm;
use App\Http\Models\Admin\KeyAccountPendingSummaryCrm;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmSettings;
use App\Http\Models\CRM\CrmTatHolidays;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KeyAccountDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keyaccount:dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update counts of pending crm requests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pending_crm_request = KeyAccountPendingCrm::get();
        if($pending_crm_request){
            foreach ($pending_crm_request as $crm_request_pending){
                $crm_request = CrmRequest::find($crm_request_pending->crm_request_id);
                if($crm_request->status == 4){
                    $crm_request_summary = KeyAccountPendingSummaryCrm::where('id', $pending_crm_request->summary_crm_request_id);
                    if($crm_request_summary->exists()){
                        $crm_request_summary->first();
                        $tat = $crm_request_summary->tat;
                        $count = $crm_request_summary->count;
                        $new_count = $crm_request_summary->count - 1;
                        $new_tat = (($tat * $count) / $new_count);

                        $crm_request_summary->count = $new_count;
                        $crm_request_summary->tat = $new_tat;
                        $crm_request_summary->save();
                    }
                    KeyAccountPendingCrm::where('id', $pending_crm_request->id)->delete();
                }
            }
        }
        $today = Carbon::today()->toDateString();
        $update_crm_request_summary_tat = KeyAccountPendingSummaryCrm::get();
        if($update_crm_request_summary_tat){
            foreach ($update_crm_request_summary_tat as $crm_request_summary_tat){
                $pending_crm_requests = KeyAccountPendingCrm::where('summary_crm_request_id', $crm_request_summary_tat->id)->whereDate('created_at', '!=', $today)->pluck('crm_request_id')->toArray();
                if(count($pending_crm_requests) > 0){
                    $crm_requests = CrmRequest::whereIn('id', $pending_crm_requests);
                    if($crm_requests->exists()){
                        $crm_requests = $crm_requests->get();
                        $total_tat = 0;
                        foreach ($crm_requests as $crm_request){
                            Carbon::setWeekendDays([
                                Carbon::SUNDAY,
                            ]);
                            $launched = Carbon::parse($crm_request->created_at);
                            $first_closed = CrmRequestStatusHistory::where('crm_request_id', $crm_request->id)->where('status_id' ,4)->first();
                            if($first_closed){
                                $current = $first_closed->created_at;
                            }
                            else{
                                $current = Carbon::now();
                            }
                            $time_format = 'H:i';
                            $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                            $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                            $from_formatted = date($time_format, strtotime($time_from->setting_value));
                            $to_formatted = date($time_format, strtotime($time_to->setting_value));
                            $cut_off_check = $crm_request->created_at->format($time_format);
                            $current_tat = $current->diffInWeekdays($launched);
                            $launched_check = $launched->toDateString();
                            $current_check = $current->toDateString();
                            if($launched_check <= $current_check){
                                if($to_formatted < $cut_off_check){
                                    $after_cut_off = $current_tat - 1;
                                    $current_tat = $after_cut_off;
                                }
                            }
                            $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                            foreach($holidays as $holiday){
                                $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                                $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                                $launched_formatted_check = date('Y-m-d', strtotime($launched));
                                if($launched < $holiday_formatted || $current > $holiday_formatted){
                                    if($holiday_formatted_check == $launched_formatted_check){
                                        if($to_formatted < $cut_off_check){
                                            $after_cut_off = $current_tat + 1;
                                            $current_tat = $after_cut_off;
                                        }
                                    }
                                    $after_holidays = $current_tat - 1;
                                    $current_tat = $after_holidays;
                                }
                            }

                            $re_open_counts = CrmRequestStatusHistory::where('crm_request_id', $crm_request->id)->where('status_id' ,5)->get();
                            if($re_open_counts){
                                foreach ($re_open_counts as $re_open_count){
                                    $launched = Carbon::parse($re_open_count->created_at);
                                    $last_closed = CrmRequestStatusHistory::where('crm_request_id', $crm_request->id)->where('status_id' ,4)->where('created_at', '>=', $re_open_count->created_at)->first();
                                    if($last_closed){
                                        $current = $last_closed->created_at;
                                    }
                                    else{
                                        $current = Carbon::now();
                                    }
                                    $time_format = 'H:i';
                                    $time_from = CrmSettings::where('name','TAT Cut-Off Time From')->first();
                                    $time_to = CrmSettings::where('name','TAT Cut-Off Time To')->first();
                                    $from_formatted = date($time_format, strtotime($time_from->setting_value));
                                    $to_formatted = date($time_format, strtotime($time_to->setting_value));
                                    $cut_off_check = $crm_request->created_at->format($time_format);
                                    $additional_tat = $current->diffInWeekdays($launched);
                                    $current_tat = $current_tat + $additional_tat;
                                    $launched_check = $launched->toDateString();
                                    $current_check = $current->toDateString();
                                    if($launched_check <= $current_check){
                                        if($to_formatted < $cut_off_check){
                                            $after_cut_off = $current_tat - 1;
                                            $current_tat = $after_cut_off;
                                        }
                                    }
                                    $holidays = CrmTatHolidays::whereBetween('holiday', [$launched, $current])->get();
                                    foreach($holidays as $holiday){
                                        $holiday_formatted = date('Y-m-d H:i:s', strtotime($holiday->holiday));
                                        $holiday_formatted_check = date('Y-m-d', strtotime($holiday->holiday));
                                        $launched_formatted_check = date('Y-m-d', strtotime($launched));
                                        if($launched < $holiday_formatted || $current > $holiday_formatted){
                                            if($holiday_formatted_check == $launched_formatted_check){
                                                if($to_formatted < $cut_off_check){
                                                    $after_cut_off = $current_tat + 1;
                                                    $current_tat = $after_cut_off;
                                                }
                                            }
                                            $after_holidays = $current_tat - 1;
                                            $current_tat = $after_holidays;
                                        }
                                    }
                                }
                            }
                            $total_tat = $total_tat + $current_tat;
                        }
                        $total_count_pending_crm_requests = KeyAccountPendingCrm::where('summary_crm_request_id', $crm_request_summary_tat->id)->count();
                        $new_tat = $total_tat / $total_count_pending_crm_requests;
                        $crm_request_summary_tat->tat = $new_tat;
                        $crm_request_summary_tat->save();
                    }
                }
            }
        }
    }
}
