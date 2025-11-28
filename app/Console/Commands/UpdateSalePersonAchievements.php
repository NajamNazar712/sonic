<?php

namespace App\Console\Commands;

use App\Models\SalespersonTargetSegment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSalePersonAchievements extends Command
{
    protected $signature = 'salesperson:update-achievements';
    protected $description = 'Update achievements in salesperson_target_segments using shipments and shipper_segment_logs';

    public function handle()
    {
        $today = Carbon::today();

        // Skip Sunday
        if ($today->isSunday()) {
            $this->info("⏭️ Skipped: Today is Sunday ({$today->toDateString()})");
            return 0;
        }

        // Determine 10-day period (1–10, 11–20, 21–end)
        $day = $today->day;
        if ($day <= 10) {
            $start = $today->copy()->startOfMonth();
            $end   = $today->copy()->startOfMonth()->addDays(9)->endOfDay();
        } elseif ($day <= 20) {
            $start = $today->copy()->startOfMonth()->addDays(10);
            $end   = $today->copy()->startOfMonth()->addDays(19)->endOfDay();
        } else {
            $start = $today->copy()->startOfMonth()->addDays(20);
            $end   = $today->copy()->endOfMonth();
        }
        $period = CarbonPeriod::create($start, $end);

        // Count working days (exclude Sundays)
        $workingDays = 0;
        foreach ($period as $date) {
            if (!$date->isSunday()) $workingDays++;
        }
        $this->info("🕒 Updating achievements for period: {$start->toDateString()} → {$end->toDateString()}");

        // Aggregate shipment performance by salesperson + segment
        $achievements = DB::table('shipper_segment_logs AS ssl')
            ->join('shipments AS s', 'ssl.shipment_id', '=', 's.id')
            ->join('users AS u', 's.user_id', '=', 'u.id') // shipper linked to salesperson
            ->join('sale_person_tags AS t', 'u.id', '=', 't.user_id') // join tags
            ->select(
                't.admin_id as salesperson_id',
                'ssl.sub_segment_id',
                DB::raw('COUNT(s.id) as total_shipments'),
                DB::raw('SUM(s.amount) as total_revenue'),
                DB::raw('SUM(s.actual_weight) as total_weight'),
                DB::raw('GROUP_CONCAT(DISTINCT t.admin_id) as tag_ids') // optional: get tags
            )
            ->whereBetween('s.created_at', [$start, $end])
            ->groupBy('t.admin_id', 'ssl.sub_segment_id')
            ->get();
                
        foreach ($achievements as $data) {
            $rps = $data->total_shipments > 0 ? $data->total_revenue / $data->total_shipments : 0;
            $rpk = $data->total_weight > 0 ? $data->total_revenue / $data->total_weight : 0;

            // Calculate per-day shipment/revenue
            $perDayShipments = round(($data->total_shipments / $workingDays));
            $perDayRevenue   = $data->total_revenue / $workingDays;
            // Loop through each day and update/create rsecords
            // foreach ($period as $date) {
                // if ($date->isSunday()) continue;
                $updated = SalespersonTargetSegment::where('salesperson_id', $data->salesperson_id)
                ->where('segment_id', $data->sub_segment_id)
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_date', '<=', $end)
                    ->where('end_date', '>=', $start);
                })->first();
                if (isset($updated->achieved_shipments_day) && $updated->achieved_shipments_day != $perDayShipments) {
                        $updated->update([
                            'achieved_shipments_day'   => DB::raw("COALESCE(achieved_shipments_day, 0) +  $perDayShipments"),
                            'achieved_revenue_day'     => DB::raw("COALESCE(achieved_revenue_day, 0) + $perDayRevenue"),
                            'achieved_shipments_month' => DB::raw("COALESCE(achieved_shipments_month, 0) + $data->total_shipments"),
                            'achieved_revenue_month'   => DB::raw("COALESCE(achieved_revenue_month, 0) +  $data->total_revenue"),
                            'achieved_rps'             => DB::raw("COALESCE(achieved_rps, 0) +  $rps"),
                            'achieved_rpk'              => DB::raw("COALESCE(achieved_rpk, 0) +   $rpk"),
                            'is_active'                => 1,
                            'updated_at'               => now(),
                        ]);

                        if ($updated)
                                $this->info("✅ Updated salesperson_id={$data->salesperson_id}, segment={$data->sub_segment_id}");
                }
            // }
        }    
        $this->info('🏁 Achievements updated successfully.');
        return 0;
    }
}
