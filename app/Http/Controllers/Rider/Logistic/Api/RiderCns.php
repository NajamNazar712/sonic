<?php
namespace App\Http\Controllers\Rider\Logistic\Api;

use App\Http\Models\Admin\Logistic\TraxCnIssueToRider;

trait RiderCns
{
    public  function cn_issue_to_rider_filter($rider_id)
    {

        $arr=[];
        $rider_cns = TraxCnIssueToRider::join('trax_rider_cn_details as rd', 'rd.cn_issue_id', '=', 'trax_cn_issue_to_riders.id')
            ->where('trax_cn_issue_to_riders.rider_id', $rider_id)
            ->where('trax_cn_issue_to_riders.status', 1)
            ->where('rd.is_used', 0)
            ->where('rd.is_hold', 0)
            ->orderBy('rd.cn_number')
            ->select('trax_cn_issue_to_riders.product_id', 'rd.cn_number');

        if($rider_cns->exists())
        {
            $rider_cns= $rider_cns->get();

            $sequences = [];
            $currentSequence = [];

            foreach ($rider_cns as $rider_cn) {

                if (empty($currentSequence)) {
                    $currentSequence['start'] = $rider_cn->cn_number;
                    $currentSequence['end'] = $rider_cn->cn_number;
                } elseif ($rider_cn->cn_number - $currentSequence['end'] == 1) {
                    $currentSequence['end'] = $rider_cn->cn_number;
                } else {
                    $sequences[] = $currentSequence;
                    $currentSequence = ['start' => $rider_cn->cn_number, 'end' => $rider_cn->cn_number];
                }
            }

            // Add the last sequence if it's not empty
            if (!empty($currentSequence)) {
                $sequences[] = $currentSequence;
            }


            // Output the sequences
            foreach ($sequences as $index => $sequence) {
                $arr[]=[
                    'product_id'=>$rider_cns[0]->product_id,
                    'cn_from'=>$sequence['start'],
                    'cn_to'=>$sequence['end']
                ];
            }


        }
        return $arr;

    }
}

?>