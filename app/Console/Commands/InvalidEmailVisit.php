<?php

namespace App\Console\Commands;

use App\DailyVisit;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InvalidEmailVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:invalidemailvisit';

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

        $sale_person_emails = DailyVisit::join('admins as a', 'a.id', 'daily_visits.admin_id')
            ->leftjoin('employees as e', 'e.id', 'a.employee_id')
            ->where('e.is_line_manager', 0)
            ->where('daily_visits.visit_status', 2)
            ->whereBetween('daily_visits.created_at', [$from, $to])
            ->select(
                'daily_visits.id',
                'a.id as sales_person_id',
                'a.name as sales_person_name',
                'daily_visits.customer_name as customer_name',
                'daily_visits.created_at as visit_date_time',
                'a.email as sales_person_email'
            );

        if ($sale_person_emails->exists()) {
            $sale_person_emails = $sale_person_emails->get();

            foreach ($sale_person_emails as $email_table) {
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

                $sales_person_email = $email_table->sales_person_email;
                NotificationsController::send(215, $details, ['sale_person' => $sales_person_email]);
            }
        }

        $line_manager_ids = DailyVisit::join('admins as a', 'a.id', 'daily_visits.admin_id')
            ->leftjoin('employees as e', 'e.id', 'a.employee_id')
            ->leftjoin('employees as lme', 'lme.id', 'e.line_manager_id')
            ->where('e.is_line_manager', 0)
            ->where('daily_visits.visit_status', 2)
            ->whereBetween('daily_visits.created_at', [$from, $to])
            ->groupBy('e.line_manager_id')
            ->pluck(
                'e.line_manager_id'
            )->toArray();

        if (!empty($line_manager_ids)) {
            foreach ($line_manager_ids as $value) {

                $line_manager_emails = DailyVisit::join('admins as a', 'a.id', 'daily_visits.admin_id')
                    ->leftjoin('employees as e', 'e.id', 'a.employee_id')
                    ->leftjoin('employees as lme', 'lme.id', 'e.line_manager_id')
                    ->where('e.is_line_manager', 0)
                    ->where('e.line_manager_id', $value)
                    ->where('daily_visits.visit_status', 2)
                    ->whereBetween('daily_visits.created_at', [$from, $to])
                    ->select(
                        'daily_visits.id',
                        'a.id as sales_person_id',
                        'a.name as sales_person_name',
                        'daily_visits.customer_name as customer_name',
                        'daily_visits.created_at as visit_date_time',
                        'lme.official_email as line_manager_email'
                    );

                if ($line_manager_emails->exists()) {
                    $line_manager_emails = $line_manager_emails->get();
                    $details = '<table style="width:100%;">';
                    $details .= '<thead><tr>
                    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Sales Person</th>
                    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Visit Date & Time</th>
                    <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Customer Name</th>';
                    $details .= '</tr></thead><tbody>';

                    $line_manager_email = '';

                    foreach ($line_manager_emails as $email_table) {

                        $details .= '<tr>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $email_table->sales_person_name . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $email_table->visit_date_time . '</td>';
                        $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;e">' . $email_table->customer_name . '</td>';
                        $details .= '</tr>';

                        $line_manager_email = $email_table->line_manager_email;

                    }
                    $details .= '</tbody></table>';
                    NotificationsController::send(215, $details, ['line' => $line_manager_email]);
                }
            }
        }

        $daily_visits = DailyVisit::leftjoin('admins as a', 'a.id', '=', 'daily_visits.admin_id')
            ->whereBetween('daily_visits.created_at', [$from, $to])
            ->where('daily_visits.visit_status', 2)
            ->select('a.name as sales_person_name', 'daily_visits.created_at as visit_date_time', 'daily_visits.customer_name as customer_name');

        if ($daily_visits->exists()) {
            $daily_visits = $daily_visits->get();

            $details = '<table style="width:100%;">';
            $details .= '<thead><tr>
            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Sales Person</th>
            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Visit Date & Time</th>
            <th style="padding:5px; border: 1px solid black; border-collapse: collapse;">Customer Name</th>';
            $details .= '</tr></thead><tbody>';

            foreach ($daily_visits as $email_table) {

                $details .= '<tr>';
                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse; color: blue; font-weight: bold">' . $email_table->sales_person_name . '</td>';
                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;">' . $email_table->visit_date_time . '</td>';
                $details .= '<td style="padding:5px; border: 1px solid black; border-collapse: collapse;e">' . $email_table->customer_name . '</td>';
                $details .= '</tr>';

                $roleIds = [99, 107, 4];
                $role_id_email = Admin::whereIn('role_id', $roleIds)->pluck('email')->toArray();
            }
            $details .= '</tbody></table>';
            NotificationsController::send(215, $details, ['role' => $role_id_email]);
        }
    }
}