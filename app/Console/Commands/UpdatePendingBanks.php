<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\UserDocumentAttachment;
use App\Models\PendingBankAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdatePendingBanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banks:update-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update pending bank accounts after 24 hours and send email notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $banks = PendingBankAccount::join('done_payments', 'done_payments.user_id','pending_bank_accounts.user_id')
            ->where('done_payments.status', 1)
            ->where('pending_bank_accounts.status', 0)
            ->select('pending_bank_accounts.*')
            ->get();
        if ($banks->isEmpty()) {
            $this->info('No pending banks to process.');
            return Command::SUCCESS;
        }
        $processed = [];
        if(!empty($banks)){
            foreach($banks as $bank){
                $userBank = UserBankInfo::find($bank->bank_id);
                $userBank->bank_name = $bank->bank_name;
                $userBank->bank_branch = $bank->bank_branch;
                $userBank->account_no = $bank->account_no;
                $userBank->account_title = $bank->account_title;
                $userBank->iban = $bank->iban_no;
                $userBank->city_id = $bank->bank_city;
                $userBank->user_id = $bank->user_id;
                $userBank->save();
                $user_attachment = UserDocumentAttachment::where('user_id', $bank->user_id)->latest()->first();
                if ($user_attachment->blank_cheque_image != NULL) {
                    Storage::disk('public')->delete('users_attached_documents/' . ($bank->user_id) . '/' . $user_attachment->blank_cheque_image);
                }
                $user_attachment->blank_cheque_image = $bank->blank_cheque_image;
                $user_attachment->save();
                NotificationsController::send(252, $bank->user->email);
                $processed[] = [
                    'user_id' => $bank->user_id,
                    'user_name' => $bank->user->name,
                    'bank_name' => $userBank->bank->name ?? '-',
                    'bank_branch' => $bank->bank_branch,
                    'account_no' => $bank->account_no,
                    'account_title' => $bank->account_title,
                    'iban_no' => $bank->iban_no,
                    'bank_city' => $userBank->city->name ?? '-',
                    'blank_cheque_image' => $bank->blank_cheque_image ?? '-',
                ];
                $bank->status = 1;
                $bank->save();
            }
        }
        return Command::SUCCESS;
    }
}
