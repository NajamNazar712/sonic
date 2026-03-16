<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class MoveUserDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:shipper-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::transaction(function () {

            // Move data
            DB::table('user_document_attachments_deleted')->insertUsing(
                [
                    'user_id',
                    'filled_and_signed_pdf',
                    'signed_acknowledgement_pdf',
                    'cnic_front_image',
                    'cnic_back_image',
                    'blank_cheque_image',
                    'uploaded_at',
                    'uploaded_by',
                    'approved_at',
                    'approved_by',
                    'rejected_at',
                    'rejected_by',
                    'e_sign_image'
                ],
                DB::table('user_document_attachments')
                    ->whereIn('user_id', function ($query) {
                        $query->select('id')
                            ->from('users')
                            ->where('created_at', '<=', '2025-11-30');
                    })
                    ->select(
                        'user_id',
                        'filled_and_signed_pdf',
                        'signed_acknowledgement_pdf',
                        'cnic_front_image',
                        'cnic_back_image',
                        'blank_cheque_image',
                        'uploaded_at',
                        'uploaded_by',
                        'approved_at',
                        'approved_by',
                        'rejected_at',
                        'rejected_by',
                        'e_sign_image'
                    )
            );

            // Delete moved records
            DB::table('user_document_attachments')
                ->whereIn('user_id', function ($query) {
                    $query->select('id')
                        ->from('users')
                        ->where('created_at', '<=', '2025-11-30');
                })
                ->delete();

            // Update users
            DB::table('users')
                ->where('created_at', '<=', '2025-11-30')
                ->update(['documents_status' => 0, 'agreement_signed' => 0]);
        });
    }

}
