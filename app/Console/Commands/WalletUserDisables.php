<?php

namespace App\Console\Commands;

use App\Http\Models\DonePayment;
use App\Http\Models\WalletUser;
use App\Models\WalletShipperSetting;
use App\Models\WalletUserDisable;
use Illuminate\Console\Command;

class WalletUserDisables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disable_wallet_users {user_id?}';

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
        if ($this->hasArgument('user_id') && null !== $this->argument('user_id')) {
            $userId = explode(',', $this->argument('user_id'));
        } else {
            $userId = [5039,5018];
        }
        $wallet_users = WalletUser::whereIn('user_id', $userId)->get();
        $success_delete = [];
        foreach ($wallet_users as $user) {
            $done_payment = DonePayment::where('user_id', $user->user_id)
                ->where('status', 0)
                ->where('is_wallet_payment', 1)
                ->first();
            if (empty($done_payment)) {
                $wallet_user_disable = $user->replicate();
                $wallet_user_disable->setTable('wallet_users_disables');

                if ($wallet_user_disable->save()) {
                    $user->delete();
                    WalletShipperSetting::where('user_id',$user->user_id)->delete();
                    $success_delete[] = $user->user_id;
                }
            }
        }

        $this->info('Wallet users disabled and deleted: ' . implode(', ', $success_delete));
        return Command::SUCCESS;
    }

}
