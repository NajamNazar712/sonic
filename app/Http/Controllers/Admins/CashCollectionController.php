<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\DeliveryCashCollection;
use App\Http\Models\Admin\DeliveryNote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CashCollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    //Cash collection Status [1 => Cash Collected, 2 => Snatch, 3 => Deduction]
    public function pending_cash_collect_snatch(Request $request){
        $delivery_note_id = $request->delivery_note_id;
        $pending_cash_collection_status = $request->action;
        $delivery_note = DeliveryNote::find($delivery_note_id);
        $amount = str_replace(',', '', $request->amount);
        if($delivery_note){
            $delivery_note->received_cod_amount = $amount;
            $delivery_note->cash_collected_by = Auth::id();
            $delivery_note->cash_collected_at = Carbon::now();
            $delivery_note->cash_collection_status = $pending_cash_collection_status;
            $delivery_note->save();

            $delivery_cash_collection = new DeliveryCashCollection();
            $delivery_cash_collection->delivery_note_id = $delivery_note->id;
            $delivery_cash_collection->amount = $amount;
            $delivery_cash_collection->collected_by = Auth::id();
            $delivery_cash_collection->remarks = $request->remarks;

            $now = Carbon::now();
            $time = $now->year . '_' . $now->month . '_' . $now->hour;

            $filename = 'cash_collection_' . $delivery_note->id . '_' . $time . '.png';
            $image = $request->file('deposit_slip');
            Storage::disk('public')->putFileAs('cash_collection_slips', $image, $filename);

            $delivery_cash_collection->deposit_slip = $filename;
            $delivery_cash_collection->save();

            return redirect()->back()->with('success', 'Deposit Slip uploaded successfully!');
        }
    }
}
