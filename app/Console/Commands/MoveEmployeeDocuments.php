<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveEmployeeDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:employees-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'move employees attachments to employee_attachments_deleted';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::transaction(function () {

            // Move data
            DB::table('employee_attachments_deleted')->insertUsing(
                [
                    'employee_id',
                    'cv',
                    'cnic',
                    'photo',
                    'academic',
                    'experience',
                    'last_pay_slip',
                    'nikkah_nama',
                    'cnic_spouse',
                    'child_b_form',
                    'cnic_nominee',
                    'utility_bill',
                    'affidavit',
                    'cheque'
                ],
                DB::table('employee_attachments')
                    ->whereIn('employee_id', function ($query) {
                        $query->select('id')
                            ->from('employees')
                            ->where('created_at', '<=', '2025-11-30');
                    })
                    ->select(
                        'employee_id',
                        'cv',
                        'cnic',
                        'photo',
                        'academic',
                        'experience',
                        'last_pay_slip',
                        'nikkah_nama',
                        'cnic_spouse',
                        'child_b_form',
                        'cnic_nominee',
                        'utility_bill',
                        'affidavit',
                        'cheque'
                    )
            );

            // Delete moved records
            DB::table('employee_attachments')
                ->whereIn('employee_id', function ($query) {
                    $query->select('id')
                        ->from('employees')
                        ->where('created_at', '<=', '2025-11-30');
                })
                ->delete();

            // Update users
            DB::table('employees')
                ->whereDate('created_at', '<=', '2025-11-30')
                ->where('status_id','!=',2)
                ->update(['status_id' => 3]);
        });
    }
}
