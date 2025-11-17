<?php

namespace App\Models;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Segment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalespersonTargetSegment extends Model
{
    use HasFactory;

    protected $table = 'salesperson_target_segments';

    protected $fillable = [
        'salesperson_id',
        'segment_id',
        'start_date',
        'end_date',
        'target_shipments_day',
        'revenue_target_day',
        'target_shipments_month',
        'revenue_target_month',
        'average_rps',
        'average_rpk',
        'achieved_shipments_day',
        'achieved_revenue_day',
        'achieved_shipments_month',
        'achieved_revenue_month',
        'achieved_rps',
        'achieved_rpk',
    ];

    /**
     * Relationship: Salesperson (User)
     */
    public function salesperson()
    {
        return $this->belongsTo(Admin::class, 'salesperson_id');
    }

    /**
     * Relationship: Segment
     */
    public function segment()
    {
        return $this->belongsTo(Segment::class, 'segment_id');
    }
}
