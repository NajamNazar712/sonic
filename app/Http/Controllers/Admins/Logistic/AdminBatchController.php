<?php

namespace App\Http\Controllers\Admins\Logistic;

use App\Http\Models\Admin\Logistic\TraxBookingBatch;
use App\Http\Models\Admin\Logistic\TraxBookingBatchDetail;
use App\Http\Models\Admin\Logistic\TraxBookingBatchAssign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;


class AdminBatchController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function booking_batch_index()
    {
        return view('admin.logistic.batches');
    }
    public function booking_batch_list()
    {
        $booking_batch = TraxBookingBatch::join('trax_booking_batch_statuses as bs', 'bs.id', 'trax_booking_batches.status_id')
            ->select('trax_booking_batches.id', 'trax_booking_batches.total_bookings', 'trax_booking_batches.complete_bookings', 'trax_booking_batches.status_id', 'bs.name as status_name')
            ->where('trax_booking_batches.status_id', 1);

        $datatables = Datatables::of($booking_batch)
            ->editColumn('total_bookings', function ($booking_batch) {
                $consigments = $booking_batch->complete_bookings . ' / ' . $booking_batch->total_bookings .' Completed';
                return $consigments;
            })->addColumn('action', function ($booking_batch) {
                if (session('role_id') == 1 || count(array_intersect([83, 84, 507], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="btn btn-secondary btn-primary btn-sm assign_batch">Select this batch</button>';
                    return $edit_button;
                } else {
                    return '';
                }
            });
        return $datatables->make(true);
    }
    static public function booking_batch_store($city_id,$total_bookings,$status=1,$created_by=346,$assign_by=346)
    {

        $booking_batch = new TraxBookingBatch();
        $booking_batch->city_id = $city_id;
        $booking_batch->total_bookings = $total_bookings;
        $booking_batch->batch_date = Carbon::now()->toDateString();
        $booking_batch->status_id = $status;
        $booking_batch->created_by = $created_by; //global admin id
        $booking_batch->assign_by = $assign_by;
        $booking_batch->save();
        return $booking_batch->id;

    }
    static public function booking_batch_detail_store($batch_id,$booking_id,$created_by=346)
    {
        $batch_detail = new TraxBookingBatchDetail();
        $batch_detail->batch_id = $batch_id;
        $batch_detail->booking_id = $booking_id;
        $batch_detail->created_by = $created_by; //global admin id
        $batch_detail->save();

    }

    public function booking_batch_assign(Request $request)
    {
        $user_id = session('id');
        $validate = Validator::make($request->all(),[
            'batch_id'=>['required','integer']
        ]);
        if ($validate->fails())
        {
            return response()->json(['status'=>1,'error'=>'Batch ID invalid']);
        }

            $batch = TraxBookingBatch::where('id',$request->batch_id)->where('status_id',1);
            if($batch->exists())
            {

                try {
                    DB::beginTransaction();

                    $batch=$batch->first();
                    $batch_user = new TraxBookingBatchAssign();
                    $batch_user->batch_id = $batch->id;
                    $batch_user->user_id = $user_id;
                    $batch_user->created_by = $user_id;
                    $batch_user->save();

                    $batch->status_id=2;
                    $batch->save();

                    DB::commit();
                    return response()->json(['status'=>0,'success'=>'Batch assign successfully']);

                } catch (\Exception $exception){
                    DB::rollBack();
                    return redirect()->back()->with('error','Failed Logistic Shipper Tagging');
                }


            }
            return response()->json(['status'=>1,'error'=>'Batch not found!']);
    }

    public function booking_batch_edit()
    {

    }
    public function booking_batch_update()
    {

    }


}
