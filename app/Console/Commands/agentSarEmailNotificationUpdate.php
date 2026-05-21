<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\RvShipmentAssignAgent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AgentSarEmailNotificationUpdate extends Command
{
    protected $signature = 'agent:sar-email-notification-update';

    protected $description = 'Update agent SAR email notifications';

    public function handle()
    {
        $currentDateTime = now();

        $nowSub16Hours = now()->subHours(16);
        $nowSub24Hours = now()->subHours(24);

        $globalSetting = GlobalSettings::where([
            'type' => 'rv_permanent_disable_shippers',
            'setting_value' => 1
        ])->first();

        $shippers = $globalSetting ? explode(',', $globalSetting->text) : [];

        DB::transaction(function () use (
            $currentDateTime,
            $nowSub16Hours,
            $nowSub24Hours
        ) {

            $sendEmails = RvShipmentAssignAgent::lockForUpdate()
                ->where('rv_assign_agent_status_id', 7)
                ->where('rv_state_id', 2)
                ->where('unresponsive_count', 3)
                ->where('updated_at', '<=', $nowSub16Hours)
                ->where('unresponsive_email_count', '<', 1);

            $sendEmailofRefusalShipments = RvShipmentAssignAgent::lockForUpdate()
                ->where('rv_assign_agent_status_id', 8)
                ->where('rv_state_id', 2)
                ->where('updated_at', '<=', $nowSub24Hours);

            $sendEmail = $sendEmails
                ->union($sendEmailofRefusalShipments)
                ->get();

            if ($sendEmail->isNotEmpty()) {

                foreach ($sendEmail as $shipment) {

                    if (
                        $shipment->rv_assign_agent_status_id == 7 &&
                        $shipment->unresponsive_email_count == 0
                    ) {
                        $shipment->update([
                            'unresponsive_email_count' => 1,
                            'unresponsive_email_time' => $currentDateTime
                        ]);
                    }
                }

                NotificationsController::send(220, $sendEmail);
            }
        });

        $this->info('SAR email notification update completed.');

        return Command::SUCCESS;
    }
}
