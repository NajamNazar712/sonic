<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyDeliveryNoteHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily_delivery_note_history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily yesterday delivery note history';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $setting = DB::table('global_settings')
        ->where('type', 'delivery_note_history_report_time')
        ->value('setting_value');

        if ($setting == 1) {
            $day = Carbon::yesterday()->toDateString();
            $response = AdminReportsEmailController::delivery_note_history($day);
            NotificationsController::send(238, $response);
        }
    }
}
