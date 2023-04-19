<?php

namespace App\Console\Commands;

use App\DailyVisit;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InvalidEmailVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:invalidemailvisit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $from = Carbon::today()->subDay()->toDateTimeString();
        $to = Carbon::parse($from)->endOfDay()->toDateTimeString();

        $emails = DailyVisit::join('admins as a', 'a.id', 'daily_visits.admin_id')
            ->leftjoin('employees as e', 'e.id', 'a.employee_id')
            ->leftjoin('employees as lme', 'lme.id', 'e.line_manager_id')
            ->where('e.is_line_manager', 0)
            ->where('daily_visits.visit_status', 2)
            ->whereBetween('daily_visits.created_at', [$from, $to])
            ->select(
                'daily_visits.id',
                'a.id as sales_person_id',
                'a.name as sales_person_name',
                'daily_visits.customer_name as customer_name',
                'daily_visits.created_at as visit_date_time',
                'lme.official_email as line_manager_email',
                'a.email as sales_person_email',
                'e.line_manager_id as line_manager_id'
            );

        if ($emails->exists()) {
            $emails = $emails->get();

            foreach ($emails as $email_table) {
                $details = '<table style="width:100%;">';
                $details .= '<thead><tr>
                    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Sales Person</th>
                    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Visit Date & Time</th>
                    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Customer Name</th>';
                $details .= '</tr></thead><tbody>';

                $details .= '<tr>';
                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $email_table->sales_person_name . '</td>';
                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $email_table->visit_date_time . '</td>';
                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;e">' . $email_table->customer_name . '</td>';
                $details .= '</tr>';

                $details .= '</tbody></table>';

                $line_manager_email = $email_table->line_manager_email;
                $sales_person_email = $email_table->sales_person_email;

                NotificationsController::send(215, $details, ['line' => $line_manager_email]);
                NotificationsController::send(215, $details, ['sale_person' => $sales_person_email]);
            }
        }
    }

}