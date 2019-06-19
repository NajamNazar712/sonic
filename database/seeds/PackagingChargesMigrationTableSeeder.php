<?php

use Illuminate\Database\Seeder;

class PackagingChargesMigrationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $charges = \App\Http\Models\PackagingCharge::all();
        foreach ($charges as $charge){
            $backup_charges = new \App\Http\Models\BackupPackagingCharge();
            $backup_charges->user_id = $charge->user_id;
            $backup_charges->shipping_mode_id = $charge->shipping_mode_id;
            $backup_charges->sm_flyer = $charge->sm_flyer;
            $backup_charges->md_flyer = $charge->md_flyer;
            $backup_charges->lg_flyer = $charge->lg_flyer;
            $backup_charges->box_flyer = $charge->box_flyer;
            $backup_charges->created_at = $charge->created_at;
            $backup_charges->updated_at = $charge->updated_at;
            $backup_charges->save();
        }

        $history_charges = \App\Http\Models\Rates\HistoryPackagingCharge::all();
        foreach ($history_charges as $hcharge){
            $backup_history_charges = new \App\Http\Models\BackupHistoryPackagingCharge();
            $backup_history_charges->user_id = $hcharge->user_id;
            $backup_history_charges->shipping_mode_id = $hcharge->shipping_mode_id;
            $backup_history_charges->sm_flyer = $hcharge->sm_flyer;
            $backup_history_charges->md_flyer = $hcharge->md_flyer;
            $backup_history_charges->lg_flyer = $hcharge->lg_flyer;
            $backup_history_charges->box_flyer = $hcharge->box_flyer;
            $backup_history_charges->created_at = $hcharge->created_at;
            $backup_history_charges->updated_at = $hcharge->updated_at;
            $backup_history_charges->save();
        }

        $pending_charges = \App\Http\Models\Rates\PendingPackagingCharge::all();
        foreach ($pending_charges as $pcharge){
            $backup_pending_charges = new \App\Http\Models\BackupPendingPackagingCharge();
            $backup_pending_charges->user_id = $pcharge->user_id;
            $backup_pending_charges->shipping_mode_id = $pcharge->shipping_mode_id;
            $backup_pending_charges->sm_flyer = $pcharge->sm_flyer;
            $backup_pending_charges->md_flyer = $pcharge->md_flyer;
            $backup_pending_charges->lg_flyer = $pcharge->lg_flyer;
            $backup_pending_charges->box_flyer = $pcharge->box_flyer;
            $backup_pending_charges->created_at = $pcharge->created_at;
            $backup_pending_charges->updated_at = $pcharge->updated_at;
            $backup_pending_charges->save();
        }

    }
}
