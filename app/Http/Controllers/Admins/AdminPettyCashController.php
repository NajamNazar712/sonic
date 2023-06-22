<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\PettyCashAccountHead;
use App\Http\Models\Admin\PettyCashAccountHeadAccountTitle;
use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\Admin\PettyCashConsignee;
use App\Http\Models\Admin\PettyCashConsigneeHub;
use App\Http\Models\Admin\PettyCashSdnLog;
use App\Http\Models\Admin\PettyCashStatement;
use App\Http\Models\Admin\PettyCashStatementAmountLog;
use App\Http\Models\Admin\PettyCashStatementDetail;
use App\Http\Models\Admin\PettyCashStatementDetailDraft;
use App\Http\Models\Admin\PettyCashStatementDraft;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\RetailPickupNoteShipment;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\City;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Admins\ActivityTrailController;


class AdminPettyCashController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    //Petty Cash Statement Status 0 -> Pending,  1 -> Station Approved,  2 -> Operation Approved,  3 -> Finance Approved,  4 -> Paid, 5 -> Adjusted, 6 -> Rejected  7 -> Received Statement
    public function make_petty_cash_statement_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),457);
        $head = PettyCashAccountHead::where('status', 1)->select('id', 'name')->get();
        $zones = Zone::where('business_category_id',1)->select('id','name')->where('status',1)->get();
        $employees = Admin::where('trax_id','!=',null)->where('status',1)->select(['id','trax_id'])->get();
        $operation_managers = Admin::where('role_id',10)->where('status',1)->select(['id','trax_id','name'])->get();
        if (session('role_id') == 1) {
            $sdns = StationDepositNote::where('status','!=', 2)->select('id')->get();
        } else {
            $sdns = StationDepositNote::where('status','!=', 2)->whereIn('hub_id', session('hubs'))->select('id')->get();
        }
        return view('admin.petty_cash.make')->with(['heads' => $head, 'sdns'=>$sdns,'zones'=>$zones,'employees'=>$employees,'operation_managers'=>$operation_managers]);
    }

    public function make_petty_cash_statement_check_destination(Request $request)
    {
            $city_id = $request->hub_id;
            $petty = PettyCashConsigneeHub::where('city_id',$city_id);
            if($petty->exists())
            {
                $data = PettyCashConsignee::where('petty_cash_consignees.id',$petty->first()->petty_cash_consignee_id)
                    ->join('cities','cities.id','=','petty_cash_consignees.hub_id')
                    ->select(['petty_cash_consignees.hub_id as id','cities.name as name'])
                    ->first();

                return response()->json(['status'=>1,'data'=>$data]);
            }
            else{
                return response()->json(['status'=>0]);
            }
    }

    public function make_petty_cash_statement_get_hubs(Request $request)
    {
            $zone_id = $request->zone;
            $hubs = City::where('zone_id',$zone_id)->where('hub',1)->where('status',1);
            if($hubs->exists())
            {
                $data = $hubs->select(['id','name'])->get();
                return response()->json(['status'=>1,'data'=>$data]);
            }
            else{
                return response()->json(['status'=>0]);
            }
    }

    public function make_petty_cash_statement_get_cities(Request $request)
    {
        $hub_id = $request->hub;
        $cities = City::where('hub_id',$hub_id)->where('status',1);
        if($cities->exists())
        {
            $data = $cities->select(['id','name'])->get();
            return response()->json(['status'=>1,'data'=>$data]);
        }
        else{
            return response()->json(['status'=>0]);
        }
    }

    public static function make_petty_cash_statement_get_dncc_static($sdn_id)
    {
        $sdn =  StationDepositNote::where('id',$sdn_id);
        if($sdn->exists())
        {
            $sdn = $sdn->first();
            if($sdn->sdn_type == 1)
            {
                foreach ($sdn->delivery_notes_list as $key => $dncc)
                {
                    $data[$key]['id'] = $dncc->delivery_note_id;
                    $data[$key]['text'] = str_pad($dncc->delivery_note_id, 4, '0', STR_PAD_LEFT);
                    $delivery_note = DeliveryNote::find($dncc->delivery_note_id);
                    if($delivery_note) {
                        $data[$key]['count'] = $delivery_note->delivered_shipments;
                    }
                    else{
                        $data[$key]['count'] = 0;
                    }
                }
            }
            else{
                foreach ($sdn->pickup_notes_list as $key => $pncc)
                {
                    $data[$key]['id'] = $pncc->retail_pickup_note_id;
                    $data[$key]['text'] = str_pad($pncc->retail_pickup_note_id, 4, '0', STR_PAD_LEFT);
                    $pickup_note = RetailPickupNote::find($pncc->retail_pickup_note_id);
                    if($pickup_note)
                    {
                        $data[$key]['count'] = $pickup_note->shipments;
                    }
                    else{
                        $data[$key]['count'] = 0;
                    }
                }
            }
            return ['status'=>1,'data'=>$data];
        }
        else{
            return ['status'=>0];
        }
    }

    public function make_petty_cash_statement_get_dncc(Request $request)
    {
        $sdn_id = $request->sdn_id;
        $response = $this::make_petty_cash_statement_get_dncc_static($sdn_id);
        return response()->json($response);

    }

    public function make_petty_cash_statement_get_employee(Request $request)
    {
        $employee_id = $request->employee;
        $employee = Admin::where('id',$employee_id)->where('status',1);
        if($employee->exists())
        {
            $employee = $employee->first();
            $name = $employee->name;
            $designation = $employee->Edesignation->name ?? $employee->designation;
            return response()->json(['status'=>1,'name'=>$name,'designation'=>$designation]);
        }
        else{
            return response()->json(['status'=>0]);
        }
    }

    public function make_petty_cash_statement_check_reference(Request $request)
    {
        $reference = $request->reference;
        if (PettyCashStatement::where('reference_no', $reference)->exists()) {
            return "true";
        } else {
            return "false";
        }
    }

    public function make_petty_cash_statement_titles(Request $request)
    {
        $account_head = $request->account_head;
        $title_ids = PettyCashAccountHeadAccountTitle::where('petty_cash_account_head_id', $account_head)->join('petty_cash_account_titles as pct', 'pct.id', '=', 'petty_cash_account_head_account_title.petty_cash_account_title_id')->where('pct.status', 1)->select('petty_cash_account_title_id')->get();

        $titles = PettyCashAccountTitle::whereIn('id', $title_ids)->select('id', 'name')->get();
        return response()->json(['status' => 1, 'titles' => $titles]);
    }

    public function make_petty_cash_statement_submit(Request $request)
    {
        if ($request->has('submit_button')) {
            $total_amount = 0;
            if (PettyCashStatement::where('reference_no', '=', $request->reference_no)->exists()) {
                return redirect()->back()->with(['status' => 0, 'error' => 'Reference No. not Unique']);
            }

            $selected_ids = explode(',', $request->input('selected_rows'));

            if ($request->input('submit_button') == 'create') {

                $petty_cash = new PettyCashStatement();
                $petty_cash->zone_id = $request->select_statement_zone;
                $petty_cash->hub_id = $request->select_statement_hub;
                $petty_cash->reference_no = $request->reference_no;
                $petty_cash->date = $request->select_statement_date_formatted;
                $petty_cash->sdn_id = $request->select_statement_sdn;
                $petty_cash->origin_hub_id = Auth::user()->default_hub_id ?? 0;
                $petty_cash->destination_hub_id = Admin::find($request->select_statement_station_manager)->default_hub_id ?? 0;
                $petty_cash->station_manager_id = $request->select_statement_station_manager;
                $petty_cash->created_by = Auth::id();
                $petty_cash->save();
                $petty_cash_statement_id = $petty_cash->id;
                $first = true;
                foreach ($selected_ids as $selected_id) {
                    $total_amount += $request->amount[$selected_id];

                    $petty_detail = new PettyCashStatementDetail();
                    $petty_detail->petty_cash_statement_id = $petty_cash_statement_id;
                    $petty_detail->account_head_id = $request->head[$selected_id];
                    $petty_detail->account_title_id = $request->title[$selected_id];
                    $petty_detail->city_id = $request->city[$selected_id];
                    $petty_detail->employee_id = $request->employee[$selected_id];
                    $petty_detail->employee_name = $request->employee_name[$selected_id];
                    $petty_detail->employee_designation = $request->employee_designation[$selected_id];
                    $petty_detail->dncc_id = $request->dncc[$selected_id] ?? Null;
                    $petty_detail->delivered_shipments = $request->delivered_shipment_count[$selected_id] ?? Null;
                    $petty_detail->expense_details = str_replace(array("\n", "\r"), '', $request->expense[$selected_id]);
                    $petty_detail->amount = $request->amount[$selected_id];
                    $petty_detail->reference_no = $request->reference[$selected_id];
                    $petty_detail->remarks = str_replace(array("\n", "\r"), '', $request->remarks[$selected_id]);
                    $petty_detail->save();

                    if ($request->hasFile('upload_image' . $selected_id)) {

                        $file = $request->file('upload_image' . $selected_id);
                        $filename = 'statement_' . $petty_cash_statement_id . '_detail_' . $petty_detail->id . '.'.$file->getClientOriginalExtension();

                        Storage::disk('public')->putFileAs('petty_cash_statement_details', $file, $filename);

                        $petty_detail->reference_document = $filename;
                        $petty_detail->save();
                    }

                    if ($request->hasFile('upload_2_image' . $selected_id)) {
                        $file = $request->file('upload_2_image' . $selected_id);
                        $filename = 'statement_2_' . $petty_cash_statement_id . '_detail_' . $petty_detail->id . '.'.$file->getClientOriginalExtension();


                        Storage::disk('public')->putFileAs('petty_cash_statement_details', $file, $filename);

                        $petty_detail->reference_document_2 = $filename;
                        $petty_detail->save();
                    }

                }
                PettyCashStatement::where('id', $petty_cash_statement_id)->update(['total_amount' => $total_amount]);

                $shipment_id = $this->create_shipment($petty_cash->id);
                $petty_cash->shipment_id = $shipment_id;
                $petty_cash->save();
                return redirect()->back()->with(['status' => 1, 'success' => 'Petty Cash Statement Successfully Created', 'print' => $shipment_id]);

            } else {

                $petty_cash_draft = new PettyCashStatementDraft();
                $petty_cash_draft->zone_id = $request->select_statement_zone;
                $petty_cash_draft->hub_id = $request->select_statement_hub;
                $petty_cash_draft->reference_no = $request->reference_no;
                $petty_cash_draft->date = $request->select_statement_date_formatted;
                $petty_cash_draft->sdn_id = $request->select_statement_sdn;
                $petty_cash_draft->origin_hub_id = Auth::user()->default_hub_id ?? 0;
                $petty_cash_draft->destination_hub_id = Admin::find($request->select_statement_station_manager)->default_hub_id ?? 0;
                $petty_cash_draft->station_manager_id = $request->select_statement_station_manager;
                $petty_cash_draft->created_by = Auth::id();
                $petty_cash_draft->save();
                foreach ($selected_ids as $selected_id) {
                    $total_amount += $request->amount[$selected_id];

                    $petty_detail_draft = new PettyCashStatementDetailDraft();
                    $petty_detail_draft->petty_cash_statement_draft_id = $petty_cash_draft->id;
                    $petty_detail_draft->account_head_id = $request->head[$selected_id];
                    $petty_detail_draft->account_title_id = $request->title[$selected_id];
                    $petty_detail_draft->city_id = $request->city[$selected_id];
                    $petty_detail_draft->employee_id = $request->employee[$selected_id];
                    $petty_detail_draft->employee_name = $request->employee_name[$selected_id];
                    $petty_detail_draft->employee_designation = $request->employee_designation[$selected_id];
                    $petty_detail_draft->expense_details = str_replace(array("\n", "\r"), '', $request->expense[$selected_id]);
                    $petty_detail_draft->dncc_id = $request->dncc[$selected_id] ?? Null;
                    $petty_detail_draft->delivered_shipments = $request->delivered_shipment_count[$selected_id] ?? Null;
                    $petty_detail_draft->amount = $request->amount[$selected_id];
                    $petty_detail_draft->reference_no = $request->reference[$selected_id];
                    $petty_detail_draft->remarks = str_replace(array("\n", "\r"), '', $request->remarks[$selected_id]);
                    $petty_detail_draft->save();

                    if ($request->hasFile('upload_image' . $selected_id)) {
                        $file = $request->file('upload_image' . $selected_id);
                        $filename = 'statement_' . $petty_cash_draft->id . '_detail_' . $petty_detail_draft->id . '.'.$file->getClientOriginalExtension();

                        Storage::disk('public')->putFileAs('petty_cash_statement_details_draft', $file, $filename);
//                        $file->move(public_path('uploads/petty_cash'), $filename);

                        $petty_detail_draft->reference_document = $filename;
                        $petty_detail_draft->save();
                    }

                    if ($request->hasFile('upload_2_image' . $selected_id)) {
                        $file = $request->file('upload_2_image' . $selected_id);
                        $filename = 'statement_2_' . $petty_cash_draft->id . '_detail_' . $petty_detail_draft->id . '.'.$file->getClientOriginalExtension();


                        Storage::disk('public')->putFileAs('petty_cash_statement_details_draft', $file, $filename);

                        $petty_detail_draft->reference_document_2 = $filename;
                        $petty_detail_draft->save();
                    }

                }
                PettyCashStatementDraft::where('id', $petty_cash_draft->id)->update(['total_amount' => $total_amount]);
                return redirect()->back()->with(['status' => 1, 'success' => 'Petty Cash Statement saved to Drafts Successfully!']);

            }

        } else {
            return redirect()->back()->with(['error' => 'Request not submitted properly!']);
        }
    }

    public function edit_petty_cash_statement_index(Request $request, $id)
    {
        $petty = PettyCashStatement::find($id);
        if(!in_array($petty->status ,[0,1,2,7,6]))
        {
            return redirect()->route('admin.petty_cash.approved.view',$id);
        }
        $head = PettyCashAccountHead::select('id', 'name')->get();

        $zones = Zone::where('business_category_id',1)->select('id','name')->where('status',1)->get();
        $hub_array = array();
        $city_array = array();
        $dncc_array = "";
        foreach ($zones as $zone) {
            if($zone->id == $petty->zone_id) {
                $hub_array[$zone->id] = $zone->zone_cities->where('hub', 1);
            }
        }

        $employees = Admin::where('trax_id','!=',null)->where('status',1)->select(['id','trax_id'])->get();
        $operation_managers = Admin::where('role_id',10)->where('status',1)->select(['id','trax_id','name'])->get();
        if (session('role_id') == 1) {
            $sdns = StationDepositNote::where('status','!=', 2)->select('id')->get();
        } else {
            $sdns = StationDepositNote::where('status','!=', 2)->whereIn('hub_id', session('hubs'))->select('id')->get();
        }

        return view('admin.petty_cash.edit')->with(['heads' => $head, 'petty_statement' => $petty,'zones'=>$zones,'sdns'=>$sdns,'employees'=>$employees,'operation_managers'=>$operation_managers,'hub_array'=>$hub_array]);
    }

    public function edit_petty_cash_statement_list(Request $request, $id)
    {
        $petty_details = PettyCashStatementDetail::leftjoin('cities as h', 'h.id', '=', 'petty_cash_statement_details.hub_id')
            ->leftjoin('admins as op','op.id','petty_cash_statement_details.operation_manager_id')
            ->leftjoin('cities as c', 'c.id', '=', 'petty_cash_statement_details.city_id')
            ->leftjoin('zones as z', 'z.id', '=', 'petty_cash_statement_details.zone_id')
            ->leftjoin('admins as a', 'a.id', '=', 'petty_cash_statement_details.employee_id')
            ->join('petty_cash_statements as pcs', 'pcs.id', '=', 'petty_cash_statement_details.petty_cash_statement_id')
            ->select('petty_cash_statement_details.id as statement_detail_id', 'h.name as hub','c.name as city','z.name as zone','petty_cash_statement_details.employee_id', 'pcs.hub_id', 'pcs.sdn_id','petty_cash_statement_details.account_head_id', 'petty_cash_statement_details.account_title_id', 'petty_cash_statement_details.date', 'petty_cash_statement_details.expense_details', 'petty_cash_statement_details.amount', 'petty_cash_statement_details.reference_no', 'petty_cash_statement_details.remarks', 'petty_cash_statement_details.status', 'pcs.status as petty_status', 'petty_cash_statement_details.station_amount', 'petty_cash_statement_details.operation_amount', 'petty_cash_statement_details.finance_amount', 'petty_cash_statement_details.reference_document as reference_document', 'petty_cash_statement_details.reference_document_2 as reference_document_2', 'petty_cash_statement_details.created_at','petty_cash_statement_details.employee_name as employee_name_data','petty_cash_statement_details.employee_designation as employee_designation_data','petty_cash_statement_details.zone_id','petty_cash_statement_details.city_id','op.name as op_name','op.trax_id as op_trax_id','op.id as op_id','petty_cash_statement_details.dncc_id','petty_cash_statement_details.delivered_shipments')
            ->where('petty_cash_statement_details.petty_cash_statement_id', $id);
        return Datatables::of($petty_details)
            ->setRowAttr([
                'status' => function ($petty_details) {
                    return $petty_details->status;
                },
            ])
            ->addColumn('account_head', function ($petty_details) {

                $heads = PettyCashAccountHead::select('id', 'name')->get();
                $drops = '';
                $selected = '';
                foreach ($heads as $status) {
                    if ($status->id == $petty_details->account_head_id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $drops .= '<option value="' . $status->id . '" ' . $selected . '>' . $status->name . '</option>';
                }
                $select = '<select class="form-control form-control-sm select2 head_select" disabled name="head[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Account Head is required">' . $drops . '</select>';
                return $select;

            })
            ->addColumn('account_title', function ($petty_details) {
                $account_head_id = $petty_details->account_head_id;
                $petty_cash_account_title_ids = PettyCashAccountHeadAccountTitle::where('petty_cash_account_head_id', $account_head_id)->pluck('petty_cash_account_title_id')->toArray();
                $titles = PettyCashAccountTitle::select('id', 'name')->whereIn('id', $petty_cash_account_title_ids)->get();
                $drops = '';
                $selected = '';
                foreach ($titles as $status) {
                    if ($status->id == $petty_details->account_title_id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $drops .= '<option value="' . $status->id . '" ' . $selected . '>' . $status->name . '</option>';
                }
                $select = '<select class="form-control form-control-sm select2 title_select" disabled name="title[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Account Title is required">' . $drops . '</select>';
                return $select;

            })
            ->addColumn('city_name', function ($petty_details) {
                $hub_id = $petty_details->hub_id;
                $city_id = $petty_details->city_id;
                $cities = City::where('status',1)->where('hub_id',$hub_id)->select('id','name')->get();
                $drops = '';
                $selected = '';
                foreach ($cities as $city) {
                    if ($city->id == $city_id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $drops .= '<option value="' . $city->id . '" ' . $selected . '>' . $city->name . '</option>';
                }
                $select = '<select class="form-control form-control-sm select2 city_select" disabled name="city[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="City is required">' . $drops . '</select>';
                return $select;
            })
            ->addColumn('employee_trax_id', function ($petty_details) {
                $employee_id = $petty_details->employee_id;
                $employees = Admin::where('status',1)->where('trax_id','!=',null)->select('id','trax_id')->get();
                $drops = '<option value=""></option>';
                $selected = '';
                foreach ($employees as $employee) {
                    if ($employee->id == $employee_id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $drops .= '<option value="' . $employee->id . '" ' . $selected . '>' . $employee->trax_id . '</option>';
                }
                $select = '<select class="form-control form-control-sm select2 employee_select" disabled name="employee[' . $petty_details->statement_detail_id . ']">' . $drops . '</select>';
                return $select;
            })
            ->addColumn('employee_name',function ($petty_details){
                $input = '<input class="form-control form-control-sm" disabled value="' . $petty_details->employee_name_data . '" name="employee_name[' . $petty_details->statement_detail_id . ']">';
                return $input;
            })
            ->addColumn('employee_designation',function ($petty_details){
                $input = '<input class="form-control form-control-sm" disabled value="' . $petty_details->employee_designation_data . '" name="employee_designations[' . $petty_details->statement_detail_id . ']" >';
                return $input;
            })
            ->addColumn('dncc', function ($petty_details) {
                $sdn_id = $petty_details->sdn_id;
                $dncc_id = $petty_details->dncc_id;
                $drops = '';
                $selected = '';
                $response = $this::make_petty_cash_statement_get_dncc_static($sdn_id);
                $drops .= "<option value='' data-count='' selected></option>";
                if($response['status'] == 1)
                {
                    foreach ($response['data'] as $data)
                    {
                        $id = $data['id'];
                        $count = $data['count'];
                        $text = $data['text'];
                        if ($id == $dncc_id) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        $drops .= "<option value='" . $id . "' data-count='" . $count . "' ".$selected.">" . $text . "</option>";
                    }
                }
                $select = '<select class="form-control form-control-sm select2 dncc_select" disabled name="dncc[' . $petty_details->statement_detail_id . ']">' . $drops . '</select>';
                return $select;
            })
            ->editColumn('delivered_shipments',function ($petty_details){
                $input = '<input class="form-control form-control-sm" placeholder="Delivered Shipments" readonly value="' . $petty_details->delivered_shipments . '" name="delivered_shipment_count[' . $petty_details->statement_detail_id . ']" >';
                return $input;
            })
            ->editColumn('expense_details', function ($petty_details) {
                $expense = '<textarea class="form-control form-control-sm" rows="6" disabled name="expense[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Expense Detail is required">' . $petty_details->expense_details . '</textarea>';
                return $expense;
            })
            ->editColumn('amount', function ($petty_details) {
                $selected_amount = '';
                if ($petty_details->finance_amount !== null) {
                    $selected_amount = $petty_details->finance_amount;
                } else if ($petty_details->operation_amount !== null) {
                    $selected_amount = $petty_details->operation_amount;
                } else if ($petty_details->station_amount !== null) {
                    $selected_amount = $petty_details->station_amount;
                } else {
                    $selected_amount = $petty_details->amount;
                }

                $amount = '<div class="input-group" style="min-width: 100px;"><input type="text" class="form-control form-control-sm amount_input" disabled value="' . $selected_amount . '" name="amount[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Amount is required"><div class="input-group-append amount_log"><span class="input-group-text p-0 pl-sm-1 pr-sm-1"><i class="ft-align-justify font-medium-4"></i></span></div></div>';
                return $amount;
            })
            ->editColumn('reference_no', function ($petty_details) {
                $reference = '<input class="form-control form-control-sm" disabled placeholder="Enter Reference No." value="' . $petty_details->reference_no . '" name="reference[' . $petty_details->statement_detail_id . ']" >';
                return $reference;
            })
            ->editColumn('remarks', function ($petty_details) {
                $remarks = '<textarea class="form-control form-control-sm" placeholder="Enter Remarks" rows="6" disabled name="remarks[' . $petty_details->statement_detail_id . ']">' . $petty_details->remarks . '</textarea>';
                return $remarks;
            })
            ->editColumn('reference_document', function ($petty_details) {
                $reference_document = '<div class="text-center">';
                if ($petty_details->reference_document != null) {
                    $reference_document .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.petty_cash.statements.reference_document', [$petty_details->reference_document]) . ' target="_blank">View</a></button>';
                }
                $reference_document .= '<input class="form-control form-control-sm" style="min-width: 200px;" type="file" name="upload_image' . $petty_details->statement_detail_id . '" disabled data-rule-extension="jpeg|jpg|png|xls|xlsx|pdf" data-msg-extension="Only file with extension jpeg, jpg, pdf, xls, xlsx or png allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">';
                if ($petty_details->reference_document_2 != null) {
                    $reference_document .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.petty_cash.statements.reference_document', [$petty_details->reference_document_2]) . ' target="_blank">View</a></button>';
                }
                $reference_document .= '<input class="form-control form-control-sm" style="min-width: 200px;" type="file" name="upload_2_image' . $petty_details->statement_detail_id . '" disabled data-rule-extension="jpeg|jpg|png|xls|xlsx|pdf" data-msg-extension="Only file with extension jpeg, jpg, pdf, xls, xlsx or png allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."></div>';
                return $reference_document;
            })
            ->editColumn('status', function ($petty_details) {
                if ($petty_details->status == 0) {
                    return "Pending";
                } else
                    if ($petty_details->status == 1) {
                        return "Rejected";
                    } else if ($petty_details->status == 2) {
                        return "Approved";
                    }
            })
            ->addColumn('action', function ($petty) {
                $dropdown = '';
                if ($petty->petty_status != 6) {
                    if ((session('role_id') == 1 || ($petty->petty_status == 2 && (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14)) || (($petty->petty_status == 0 || $petty->petty_status == 1) && (session('role_id') == 8 || session('role_id') == 10))) && ($petty->status != 1)) {
                        $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

//                    $dropdown .= '<button type="button" class="dropdown-item reference_document" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Reference Document</div></button>';
                        $dropdown .= '<button type="button" class="dropdown-item approve" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Approve</div></button>';
                        $dropdown .= '<button type="button" class="dropdown-item reject" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">Reject</div></button>';

                    }
                }
                return $dropdown;
            })
            ->make(true);
    }

    public function petty_cash_statements_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),39);
        if (session('role_id') == 1) {
            $hubs = City::where('hub', 1)->where('status',1)->get();
        } else {
            $hubs = City::where('hub', 1)->where('status',1)->whereIn('id', session('hubs'))->get();
        }
        $zones = Zone::where('status', 1)->get();
        return view('admin.petty_cash.statements')->with(['hubs' => $hubs,'zones'=>$zones]);
    }

    public function reference_document($reference_document)
    {
        $file = File::glob(public_path() . '/storage/petty_cash_statement_details/' . $reference_document);
        if ($file) {
            $url = Storage::url('petty_cash_statement_details/' . $reference_document);
        } else {
            $url = Storage::disk('s3')->temporaryUrl('petty_cash_statement_images/' . $reference_document, now()->addMinutes(5));
        }

        return redirect($url);
        return view('admin.petty_cash.reference_document')->with(['url' => $url]);
    }

    public function draft_reference_document($reference_document)
    {
        $url = Storage::url('petty_cash_statement_details_draft/' . $reference_document);

        return view('admin.petty_cash.reference_document')->with(['url' => $url]);
    }

    public function sdn_log(Request $request)
    {
        $logs = PettyCashSdnLog::where('petty_cash_statement_id',$request->statement_id);
        if($logs->exists())
        {
            $logs = $logs->get();
            $data = array();
            foreach ($logs as $key => $log)
            {
                $data[$key]['sdn'] = str_pad($log->previous_sdn_id, 6, '0', STR_PAD_LEFT);
                $data[$key]['admin'] = $log->admin->name;
                $data[$key]['timestamp'] = (string)$log->created_at;
            }

            return response()->json(['status'=>0,'logs'=>$data]);
        }
        else{
            return response()->json(['status'=>1,'error'=>'No Logs Found']);
        }

    }

    public function petty_cash_statements_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),99);
        }

        $petty = PettyCashStatement::leftjoin('cities as h', 'h.id', '=', 'petty_cash_statements.hub_id')
            ->leftjoin('cities as o', 'o.id', '=', 'petty_cash_statements.origin_hub_id')
            ->leftjoin('cities as d', 'd.id', '=', 'petty_cash_statements.destination_hub_id')
            ->join('admins as cb', 'cb.id', '=', 'petty_cash_statements.created_by')
            ->leftjoin('admins as sab', 'sab.id', '=', 'petty_cash_statements.station_approved_by')
            ->leftjoin('admins as oab', 'oab.id', '=', 'petty_cash_statements.operation_approved_by')
            ->leftjoin('admins as fab', 'fab.id', '=', 'petty_cash_statements.finance_approved_by')
            ->leftjoin('admins as pccb', 'pccb.id', '=', 'petty_cash_statements.checked_by')
            ->leftjoin('shipments', 'shipments.id', '=', 'petty_cash_statements.shipment_id')
            ->select('petty_cash_statements.id as statement_id', 'petty_cash_statements.id as statement_link', 'h.name as hub_name','o.name as origin_hub_name','d.name as destination_hub_name', 'petty_cash_statements.reference_no', 'petty_cash_statements.from', 'petty_cash_statements.to', 'cb.name as created_by', 'petty_cash_statements.created_at', 'sab.name as station_approved_by', 'petty_cash_statements.station_approved_at', 'oab.name as operation_approved_by', 'petty_cash_statements.operation_approved_at', 'fab.name as finance_approved_by', 'petty_cash_statements.finance_approved_at', 'petty_cash_statements.status', 'petty_cash_statements.total_amount','sab.name as finance_apprved_by', 'petty_cash_statements.finance_approved_at', 'petty_cash_statements.checked_at', 'pccb.name as checked_by','petty_cash_statements.date')
            ->whereIn('petty_cash_statements.status', [0, 1, 2, 7]);

        if (session('role_id') != 1) {
            $petty = $petty->where(function ($query) {
                    $query->whereIn('petty_cash_statements.origin_hub_id', session('hubs'))
                    ->orWhereIn('petty_cash_statements.destination_hub_id', session('hubs'))
                    ->orWhere('petty_cash_statements.created_by', Auth::id())
                    ->orWhereIn('petty_cash_statements.hub_id', session('hubs'));
                });
        }

        $petty = Datatables::of($petty)
            ->editColumn('statement_link', function ($petty) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . $petty->statement_link . '</span></button>';
            })
            ->addColumn('sdn_update_logs', function ($petty) {
                $log = PettyCashSdnLog::where('petty_cash_statement_id',$petty->statement_id);
                if($log->exists())
                {
                    return '<button class="btn btn-sm btn-outline-info align-middle sdn_logs font-medium-1"><i class="ft-align-justify align-middle"></i></button>';
                }
                return "-";
            })
            ->editColumn('total_amount', function ($shipment) {
                return number_format($shipment->total_amount);
            })
            ->editColumn('date', function ($petty) {
                if($petty->from != null && $petty->to != null) {
                    return Carbon::parse($petty->from)->toDateString() . ' - ' . Carbon::parse($petty->to)->toDateString();
                }

                if($petty->date != null)
                {
                    return Carbon::parse($petty->date)->toDateString();
                }

                return "-";
            })
            ->editColumn('status', function ($petty) {
                $status = '';
                if ($petty->status == 0) {
                    $status = 'Created';
                } else if ($petty->status == 1) {
                    $status = 'Station Approved';
                } else if ($petty->status == 2) {
                    $status = 'Operation Approved';
                } else if ($petty->status == 7) {
                    $status = 'Received Statement';
                }
                return $status;
            })
            ->addColumn('action', function ($petty) {
                $route = route('admin.petty_cash.statements.edit', ['id' => $petty->statement_id]);
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<a href="' . $route . '" class="dropdown-item" ><i class="ft-eye"></i> View Details</a>';

//                if((session('role_id') == 1) || ($petty->status == 0 && (session('role_id') == 9) || session('role_id') == 10) || ($petty->status == 1 && (session('role_id') == 3) || session('role_id') == 8 || session('role_id') == 20) || ($petty->status == 2 && (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14))){
                if ((session('role_id') == 1) || in_array(173, session('permissions')) || in_array(190, session('permissions')) || in_array(191, session('permissions'))) {
                    if ((session('role_id') == 1) || ($petty->status == 0 && session('department_id') == 6) || ($petty->status == 2 && session('department_id') == 4) || ($petty->status == 7 && session('department_id') == 4)) {
                        if (in_array($petty->status,[1,2])) {
                            $dropdown .= '<button type="button" class="dropdown-item approve" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div>Receive</button>';
                        }else {
                            $dropdown .= '<button type="button" class="dropdown-item approve" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div>Approve</button>';
                        }
                    }
                }
                return $dropdown;
            });
        if ($hub = $request->get('search_hub')) {
            $petty->where('h.id', '=', $hub);
        }

        if ($zone = $request->get('search_zone')) {
            $petty->where('h.zone_id', '=', $zone);
        }
        if ($search_date = $request->get('search_creation_date')) {
            $petty->whereDate('petty_cash_statements.created_at', $search_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $petty->whereBetween('petty_cash_statements.created_at', [$from, $to]);
        }

        return $petty->make(true);
    }

    public function petty_cash_statements_approve(Request $request)
    {
        $statement_id = $request->statement_id;
        $petty = PettyCashStatement::find($statement_id);
        if ($petty) {
            if ($petty->station_approved_by == null) {
                if (session('role_id') == 1 || in_array(190, session('permissions'))) {
                    $petty->station_approved_by = Auth::id();
                    $petty->station_approved_at = Carbon::now();
                    $petty->status = 1;
                    $petty->save();
                    $this->petty_cash_statement_details_auto_approve($request->statement_id);
                } else {
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Request can not approve at current status!']);
                }

            }
            //   if ($petty->operation_approved_by == null) {
            //     if (session('role_id') == 1 || in_array(191, session('permissions'))) {
            //         $petty->operation_approved_by = Auth::id();
            //         $petty->operation_approved_at = Carbon::now();
            //         $petty->status = 2;
            //         $petty->save();
            //         $this->petty_cash_statement_details_auto_approve($request->statement_id);
            //     } else {
            //         return response()->json(['status' => 0, 'error' => 'Petty Cash Request can not approve at current status!']);
            //     }
            // } 
            else if ($petty->finance_received_statement_by == null) {
                if (session('role_id') == 1 || in_array(173, session('permissions'))) {
                    $petty->finance_received_statement_by = Auth::id();
                    $petty->finance_received_statement_at = Carbon::now();
                    $petty->status = 7;
                    $petty->save();
                }
            } else if ($petty->finance_approved_by == null) {
                if (session('role_id') == 1 || in_array(173, session('permissions'))) {
                    $petty->finance_approved_by = Auth::id();
                    $petty->finance_approved_at = Carbon::now();
                    $petty->status = 3;
                    $petty->save();
                    $this->petty_cash_statement_details_auto_approve($request->statement_id);
                } else {
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Request can not approve at current status!']);
                }
            }
            return response()->json(['status' => 1, 'success' => 'Petty Cash Request Successfully Approved!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement not found!']);

        }

    }

    public function petty_cash_statement_details_auto_approve($id)
    {
        $details = PettyCashStatementDetail::where('petty_cash_statement_id', $id)->where('status', 0);
        if ($details->exists()) {
            $details = $details->get();
            foreach ($details as $row) {
                $row->status = 2;
                $row->save();
            }
        }
    }

    public function edit_petty_cash_statements_approve(Request $request)
    {
        $id = $request->detail_id;
        if ($request->has('approve_all')) {
            if ($id) {
                $petty_details = PettyCashStatementDetail::where('petty_cash_statement_id', $id)->get();
                if ($petty_details) {
                    $flag = false;
                    foreach ($petty_details as $petty_detail) {
                        if ($petty_detail['status'] != 1) {
                            PettyCashStatementDetail::where('id', $petty_detail['id'])->update([
                                'status' => 2,
                                'updated_by' => Auth::id()
                            ]);
                            $flag = true;
                        }
                    }
                    $petty = PettyCashStatement::find($id);
                    if ($petty) {
                        if ($petty->station_approved_by == null) {
                            if (session('role_id') == 1 || in_array(190, session('permissions'))) {
                                $petty->station_approved_by = Auth::id();
                                $petty->station_approved_at = Carbon::now();
                                $petty->status = 1;
                                $petty->save();
                            }

                        } 
                        // else if ($petty->operation_approved_by == null) {
                        //     if (session('role_id') == 1 || in_array(191, session('permissions'))) {
                        //         $petty->operation_approved_by = Auth::id();
                        //         $petty->operation_approved_at = Carbon::now();
                        //         $petty->status = 2;
                        //         $petty->save();

                        //     }
                        // } 
                        else if ($petty->finance_received_statement_by == null) {
                            if (session('role_id') == 1 || in_array(173, session('permissions'))) {
                                $petty->finance_received_statement_by = Auth::id();
                                $petty->finance_received_statement_at = Carbon::now();
                                $petty->status = 7;
                                $petty->save();
                            }
                        } else if ($petty->finance_approved_by == null) {
                            if (session('role_id') == 1 || in_array(173, session('permissions'))) {
                                $petty->finance_approved_by = Auth::id();
                                $petty->finance_approved_at = Carbon::now();
                                $petty->status = 3;
                                $petty->save();
                            }
                        }
                    }

                    if ($flag = false) {
                        return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Already Approved!']);
                    } else {
                        return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Successfully Approved!']);
                    }
                } else {
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Details not found!']);
                }
            } else {
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Detail ID not found!']);
            }
        } else {
            if ($id) {
                $petty_details = PettyCashStatementDetail::find($id);
                if ($petty_details) {
                    if ($petty_details->status == 0) {
                        $petty_details->status = 2;
                        $petty_details->updated_by = Auth::id();
                        $petty_details->save();
                        return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Successfully Approved!']);
                    } else if ($petty_details->status == 2) {
                        return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Already Approved!']);
                    } else {
                        return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Details rejected so it can\'t be changed!']);
                    }

                } else {
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Details not found!']);
                }
            } else {
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Detail ID not found!']);
            }
        }
    }

    public function edit_petty_cash_statements_reject(Request $request)
    {
        $id = $request->detail_id;
        if ($id) {
            $petty_details = PettyCashStatementDetail::find($id);
            if ($petty_details) {
                $sub_amount = 0;
                if ($petty_details->finance_amount != null) {
                    $sub_amount = $petty_details->finance_amount;
                } else if ($petty_details->operation_amount != null) {
                    $sub_amount = $petty_details->operation_amount;
                } else if ($petty_details->station_amount != null) {
                    $sub_amount = $petty_details->station_amount;
                } else {
                    $sub_amount = $petty_details->amount;
                }
                if ($petty_details->status == 0 || $petty_details->status == 2) {
                    $petty_details->status = 1;
                    $petty_details->updated_by = Auth::id();
                    $petty_details->save();
                    $statement_id = $petty_details->petty_cash_statement_id;
                    $statement = PettyCashStatement::find($statement_id);
                    $statement->total_amount = $statement->total_amount - $sub_amount;
                    $statement->save();
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Detail Successfully Rejected!']);
                } else if ($petty_details->status == 1) {
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Details Already Rejected!']);
                }

            } else {
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Details not found!']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Detail ID not found!']);
        }

    }

    public function edit_petty_cash_statements_submit(Request $request)
    {
        $selected_ids = explode(',', $request->input('selected_rows'));
        $statement_id = $request->petty_statement_id;
        $petty_cash = PettyCashStatement::find($statement_id);
        $total_amount = 0;
        if ($petty_cash) {
            if(!in_array($petty_cash->status ,[0,1,2,7]))
            {
                return redirect()->route('admin.petty_cash.approved.view',$statement_id);
            }
            foreach ($selected_ids as $selected_id) {
                $total_amount += $request->amount[$selected_id];
                $petty_detail = PettyCashStatementDetail::where('petty_cash_statement_id', $petty_cash->id)->where('id', $selected_id)->first();

                if (session('role_id') == 1 || (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14)) {
                    $petty_detail->account_head_id = $request->head[$selected_id];
                    $petty_detail->account_title_id = $request->title[$selected_id];
                }

                $petty_detail->expense_details = $request->expense[$selected_id];
                $petty_detail->reference_no = $request->reference[$selected_id];
                $petty_detail->remarks = $request->remarks[$selected_id];
                if ($petty_cash->status == 0) {
                    $petty_detail->station_amount = $request->amount[$selected_id];
                    $petty_detail->dncc_id = $request->dncc[$selected_id] ?? null;
                    $petty_detail->delivered_shipments = $request->delivered_shipment_count[$selected_id] ?? null;
                } else if ($petty_cash->status == 1) {
                    $petty_detail->operation_amount = $request->amount[$selected_id];
                } else {
                    $petty_detail->finance_amount = $request->amount[$selected_id];
                }
                if ($petty_detail->amount != $request->amount[$selected_id]) {
                    $amount_log = new PettyCashStatementAmountLog();
                    $amount_log->petty_cash_statement_detail_id = $petty_detail->id;
                    $amount_log->admin_id = Auth::id();
                    $amount_log->changed_amount = $request->amount[$selected_id];
                    $amount_log->save();
                }
                $petty_detail->save();
                if ($request->hasFile('upload_image' . $petty_detail->id)) {
                    $file = $request->file('upload_image' . $petty_detail->id);
                    $filename = 'statement_' . $petty_cash->id . '_detail_' . $petty_detail->id.'.'.$file->getClientOriginalExtension();
                    Storage::disk('public')->delete('petty_cash_statement_details/' . $filename);


                    Storage::disk('public')->putFileAs('petty_cash_statement_details', $file, $filename);

                    $petty_detail->reference_document = $filename;
                    $petty_detail->save();
                }

                if ($request->hasFile('upload_2_image' . $petty_detail->id)) {
                    $file = $request->file('upload_2_image' . $petty_detail->id);
                    $filename = 'statement_2_' . $petty_cash->id . '_detail_' . $petty_detail->id.'.'.$file->getClientOriginalExtension();
                    Storage::disk('public')->delete('petty_cash_statement_details/' . $filename);


                    Storage::disk('public')->putFileAs('petty_cash_statement_details', $file, $filename);

                    $petty_detail->reference_document_2 = $filename;
                    $petty_detail->save();
                }
            }
            if($request->has('select_statement_sdn') && $request->select_statement_sdn != $petty_cash->sdn_id)
            {
                $sdn_log = new PettyCashSdnLog();
                $sdn_log->petty_cash_statement_id = $petty_cash->id;
                $sdn_log->previous_sdn_id = $petty_cash->sdn_id;
                $sdn_log->admin_id = Auth::id();
                $sdn_log->save();

                $petty_cash->sdn_id = $request->select_statement_sdn;

            }
            $petty_cash->total_amount = $total_amount;
            $petty_cash->save();
            return redirect()->back()->with(['status' => 1, 'success' => 'Petty Cash Statement Successfully Updated!']);
        } else {
            return redirect()->back()->with(['status' => 0, 'error' => 'Petty Cash Statement With This ID Not Found!']);
        }

    }

    public function approved_petty_cash_statements_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),40);
        return view('admin.petty_cash.approved');
    }

    public function approved_petty_cash_statements_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),100);
        }

        $petty = PettyCashStatement::leftjoin('cities as h', 'h.id', '=', 'petty_cash_statements.hub_id')
            ->leftjoin('cities as o', 'o.id', '=', 'petty_cash_statements.origin_hub_id')
            ->leftjoin('cities as d', 'd.id', '=', 'petty_cash_statements.destination_hub_id')
            ->join('admins as cb', 'cb.id', '=', 'petty_cash_statements.created_by')
            ->leftjoin('admins as sab', 'sab.id', '=', 'petty_cash_statements.station_approved_by')
            ->leftjoin('admins as oab', 'oab.id', '=', 'petty_cash_statements.operation_approved_by')
            ->leftjoin('admins as fab', 'fab.id', '=', 'petty_cash_statements.finance_approved_by')
            ->leftjoin('shipments', 'shipments.id', '=', 'petty_cash_statements.shipment_id')
            ->select('petty_cash_statements.id as statement_id', 'petty_cash_statements.id as statement_link', 'h.name as hub_name', 'petty_cash_statements.reference_no','o.name as origin_hub_name','d.name as destination_hub_name', 'petty_cash_statements.from', 'petty_cash_statements.to', 'cb.name as created_by', 'petty_cash_statements.created_at', 'sab.name as station_approved_by', 'petty_cash_statements.station_approved_at', 'oab.name as operation_approved_by', 'petty_cash_statements.operation_approved_at', 'fab.name as finance_approved_by', 'petty_cash_statements.finance_approved_at', 'petty_cash_statements.status', 'petty_cash_statements.total_amount','petty_cash_statements.date')
            ->whereIn('petty_cash_statements.status', [3, 4, 5]);

        if (session('role_id') != 1) {
            $petty = $petty->where(function ($query) {
                $query->whereIn('petty_cash_statements.origin_hub_id', session('hubs'))
                ->orWhereIn('petty_cash_statements.destination_hub_id', session('hubs'))
                ->orWhere('petty_cash_statements.created_by', Auth::id())
                ->orWhereIn('petty_cash_statements.hub_id', session('hubs'));
            });
        }

        $petty = Datatables::of($petty)
            ->editColumn('statement_link', function ($petty) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . $petty->statement_link . '</span></button>';
            })
            ->addColumn('sdn_update_logs', function ($petty) {
                $log = PettyCashSdnLog::where('petty_cash_statement_id',$petty->statement_id);
                if($log->exists())
                {
                    return '<button class="btn btn-sm btn-outline-info align-middle sdn_logs font-medium-1"><i class="ft-align-justify align-middle"></i></button>';
                }
                return "-";
            })
            ->editColumn('total_amount', function ($shipment) {
                return number_format($shipment->total_amount);
            })
            ->editColumn('date', function ($petty) {
                if($petty->from != null && $petty->to != null) {
                    return Carbon::parse($petty->from)->toDateString() . ' - ' . Carbon::parse($petty->to)->toDateString();
                }

                if($petty->date != null)
                {
                    return Carbon::parse($petty->date)->toDateString();
                }

                return "-";
            })
            ->editColumn('status', function ($petty) {
                $status = '';
                if ($petty->status == 3) {
                    $status = 'Finance Approved';
                } else if ($petty->status == 4) {
                    $status = 'Paid';
                } else if ($petty->status == 5) {
                    $status = 'Adjusted';
                }
                return $status;
            })
            ->addColumn('action', function ($petty) {
                $dropdown = '';
                if (session('role_id') == 1 || (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14 || in_array(461, session('permissions')))) {
                        if((session('role_id') == 1 || in_array(461, session('permissions'))) || $petty->status == 3) {
                            $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                            if (session('role_id') == 1 || in_array(461, session('permissions'))) {

                                $view_route = route('admin.petty_cash.approved.view', $petty->statement_id);
                                $dropdown .= '<a href=' . $view_route . '><button type="button" class="dropdown-item view" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">View/Edit</div></button></a>';

                            }
                            if ($petty->status == 3) {
                                $dropdown .= '<button type="button" class="dropdown-item paid" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Paid</div></button>';

                                $dropdown .= '<button type="button" class="dropdown-item adjusted" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-clipboard"></i></div><div class="col-9 offset-1">Adjusted</div></button>';
                            }
                        }
                }
                return $dropdown;
            });

            if ($request->get('search_date_from') && $request->get('search_date_to')) {
                $from = $request->get('search_date_from');
                $to = $request->get('search_date_to');
                $petty->whereBetween('petty_cash_statements.finance_approved_at', [$from,$to]);
            }
        return $petty->make(true);
    }

    public function approved_petty_cash_statements_view(Request $request,$id)
    {
        $petty = PettyCashStatement::where('petty_cash_statements.id',$id)
            ->leftJoin('cities as hubs','hubs.id','=','petty_cash_statements.hub_id')
            ->leftJoin('zones as z','z.id','=','petty_cash_statements.zone_id')
            ->leftJoin('admins as sm','sm.id','=','petty_cash_statements.station_manager_id')
            ->select('petty_cash_statements.*','hubs.name as hub_name','z.name as zone_name','sm.name as station_manager_name');

        if (session('role_id') != 1) {
            $petty = $petty->where(function ($query) {
                $query->whereIn('petty_cash_statements.origin_hub_id', session('hubs'))
                ->orWhereIn('petty_cash_statements.destination_hub_id', session('hubs'))
                ->orWhere('petty_cash_statements.created_by', Auth::id())
                ->orWhereIn('petty_cash_statements.hub_id', session('hubs'));
            });
        }

        if(!$petty->exists())
        {
            return back()->with(['error'=>"No Data Found!!"]);
        }
        $petty = $petty->first();
        $head = PettyCashAccountHead::select('id', 'name')->get();
        $cities = City::select('id', 'name')->where('status',1)->get();
        return view('admin.petty_cash.view')->with(['heads' => $head, 'cities' => $cities, 'petty_statement' => $petty]);
    }

    public function approved_petty_cash_statements_view_list(Request $request,$id)
    {
        $petty_details = PettyCashStatementDetail::leftjoin('cities as h', 'h.id', '=', 'petty_cash_statement_details.hub_id')
            ->leftjoin('admins as op','op.id','petty_cash_statement_details.operation_manager_id')
            ->leftjoin('cities as c', 'c.id', '=', 'petty_cash_statement_details.city_id')
            ->leftjoin('zones as z', 'z.id', '=', 'petty_cash_statement_details.zone_id')
            ->leftjoin('admins as a','a.id','petty_cash_statement_details.employee_id')
            ->leftjoin('admins as ad','ad.id','petty_cash_statement_details.edit_by')
            ->join('petty_cash_statements as pcs', 'pcs.id', '=', 'petty_cash_statement_details.petty_cash_statement_id')
            ->select('petty_cash_statement_details.id as statement_detail_id', 'h.name as hub', 'petty_cash_statement_details.hub_id', 'petty_cash_statement_details.account_head_id', 'petty_cash_statement_details.account_title_id', 'petty_cash_statement_details.date', 'petty_cash_statement_details.expense_details', 'petty_cash_statement_details.amount', 'petty_cash_statement_details.reference_no', 'petty_cash_statement_details.remarks', 'petty_cash_statement_details.status', 'pcs.status as petty_status', 'petty_cash_statement_details.station_amount', 'petty_cash_statement_details.operation_amount', 'petty_cash_statement_details.finance_amount', 'petty_cash_statement_details.reference_document as reference_document', 'petty_cash_statement_details.created_at','a.trax_id as employee_trax_id','z.name as zone_name','c.name as city_name','petty_cash_statement_details.employee_name','petty_cash_statement_details.employee_designation','petty_cash_statement_details.reference_document_2','op.name as op_name','op.trax_id as op_trax_id','petty_cash_statement_details.dncc_id','petty_cash_statement_details.delivered_shipments','petty_cash_statement_details.edit_by as edit_by_admin','ad.name as edit_by','petty_cash_statement_details.edit_at as edit_at')
            ->where('petty_cash_statement_details.petty_cash_statement_id', $id);
        return Datatables::of($petty_details)
            ->setRowAttr([
                'status' => function ($petty_details) {
                    return $petty_details->status;
                },
            ])
            ->addColumn('account_head', function ($petty_details) {
                $head =  PettyCashAccountHead::where('id',$petty_details->account_head_id)->first();
                return $head->name;
            })
            ->addColumn('account_title', function ($petty_details) {

                $titles = PettyCashAccountTitle::where('id',$petty_details->account_title_id)->first();
                return $titles->name;
            })
            ->addColumn('hub_name', function ($petty_details) {
                if ($petty_details->hub_id != null) {
                    $hubs = City::where('id',$petty_details->hub_id)->first();
                    return $hubs->name;
                }
                return '';

            })
            ->addColumn('operation_manager_name', function ($petty_details) {
                if ($petty_details->operation_manager_id != null) {
                    return $petty_details->op_name ." ( ".$petty_details->op_trax_id." )";
                }
                return '';

            })
            ->addColumn('dncc', function ($petty_details) {
                if ($petty_details->dncc_id != null) {
                    return str_pad($petty_details->dncc_id, 4, '0', STR_PAD_LEFT);
                }
                return '';

            })
            ->editColumn('date', function ($petty_details) {
                return Carbon::parse($petty_details->date)->toDateString();
            })
            ->editColumn('expense_details', function ($petty_details) {

                return $petty_details->expense_details;
            })
            ->editColumn('amount', function ($petty_details) {
                $selected_amount = '';
                if ($petty_details->finance_amount !== null) {
                    $selected_amount = $petty_details->finance_amount;
                } else if ($petty_details->operation_amount !== null) {
                    $selected_amount = $petty_details->operation_amount;
                } else if ($petty_details->station_amount !== null) {
                    $selected_amount = $petty_details->station_amount;
                } else {
                    $selected_amount = $petty_details->amount;
                }

                $amount = '<div class="input-group" style="min-width: 100px;">' . $selected_amount . '<div class="input-group-append amount_log"><span class="input-group-text p-0 ml-1"><i class="ft-align-justify font-medium-4"></i></span></div></div>';
                return $amount;
            })
            ->editColumn('reference_no', function ($petty_details) {
                return $petty_details->reference_no;
            })
            ->editColumn('remarks', function ($petty_details) {
                return $petty_details->remarks;
            })
            ->editColumn('reference_document', function ($petty_details) {
                $reference_document = '<div class="text-center">';
                if ($petty_details->reference_document != null) {
                    $reference_document .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.petty_cash.statements.reference_document', [$petty_details->reference_document]) . ' target="_blank">View</a></button>';
                }
                if ($petty_details->reference_document_2 != null) {
                    $reference_document .= '<br><button type="button" class="btn btn-primary btn-sm mt-1"><a class="white" href=' . route('admin.petty_cash.statements.reference_document', [$petty_details->reference_document_2]) . ' target="_blank">View</a></button>';
                }
                $reference_document .= '</div>';
                return $reference_document;
            })
            ->editColumn('status', function ($petty_details) {
                if ($petty_details->status == 0) {
                    return "Pending";
                } else
                    if ($petty_details->status == 1) {
                        return "Rejected";
                    } else if ($petty_details->status == 2) {
                        return "Approved";
                    }
            })
            ->addColumn('edit_by_admin', function ($petty_details) {
                if ($petty_details->edit_by == null) {
                    return "-";
                }
                else
                {
                    return $petty_details->edit_by;
                }

            })
            ->addColumn('edit_at', function ($petty_details) {
                if ($petty_details->edit_at == null) {
                    return "-";
                }
                else
                {
                    return $petty_details->edit_at;
                }

            })
            ->addColumn('action', function ($petty) {
                if ((session('role_id') == 1) || in_array(840, session('permissions')) || in_array(841, session('permissions')))
                {

                $dropdown = '
                <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
                ';

                if ((session('role_id') == 1) || in_array(840, session('permissions'))) {
                    $dropdown .= '<button data-account_title_id="'.$petty->account_title_id.'" data-account_head_id="'.$petty->account_head_id.'" data-id="'.$petty->statement_detail_id.'" data-target="#edit_petty_cash_fields" data-toggle="modal" type="button" class="dropdown-item edit_fields" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div>Edit</button>';
                }
                if ((session('role_id') == 1) || in_array(841, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item edit_amount" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit-3"></i></div>Edit Amount</button>';
                }
                    return $dropdown;
                }
                else
                {
                    $dropdown = '--';
                }
                })->make(true);
    }

    public function rejected_petty_cash_statements_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),41);
        return view('admin.petty_cash.rejected');
    }

    public function rejected_petty_cash_statements_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),101);
        }

        $petty = PettyCashStatement::leftjoin('cities as h', 'h.id', '=', 'petty_cash_statements.hub_id')
            ->leftjoin('cities as o', 'o.id', '=', 'petty_cash_statements.origin_hub_id')
            ->leftjoin('cities as d', 'd.id', '=', 'petty_cash_statements.destination_hub_id')
            ->join('admins as cb', 'cb.id', '=', 'petty_cash_statements.created_by')
            ->leftjoin('admins as sab', 'sab.id', '=', 'petty_cash_statements.station_approved_by')
            ->leftjoin('admins as oab', 'oab.id', '=', 'petty_cash_statements.operation_approved_by')
            ->leftjoin('admins as fab', 'fab.id', '=', 'petty_cash_statements.finance_approved_by')
            ->select('petty_cash_statements.id as statement_id', 'petty_cash_statements.id as statement_link', 'h.name as hub_name', 'petty_cash_statements.reference_no','o.name as origin_hub_name','d.name as destination_hub_name', 'petty_cash_statements.from', 'petty_cash_statements.to', 'cb.name as created_by', 'petty_cash_statements.created_at', 'sab.name as station_approved_by', 'petty_cash_statements.station_approved_at', 'oab.name as operation_approved_by', 'petty_cash_statements.operation_approved_at', 'fab.name as finance_approved_by', 'petty_cash_statements.finance_approved_at', 'petty_cash_statements.status', 'petty_cash_statements.total_amount', 'petty_cash_statements.date')
            ->where('petty_cash_statements.status', 6);

        if (session('role_id') != 1) {
            $petty = $petty->where(function ($query) {
                $query->whereIn('petty_cash_statements.origin_hub_id', session('hubs'))
                ->orWhereIn('petty_cash_statements.destination_hub_id', session('hubs'))
                ->orWhere('petty_cash_statements.created_by', Auth::id())
                ->orWhereIn('petty_cash_statements.hub_id', session('hubs'));
            });
        }

        $petty = Datatables::of($petty)
            ->editColumn('statement_link', function ($petty) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . $petty->statement_link . '</span></button>';
            })
            ->editColumn('total_amount', function ($shipment) {
                return number_format($shipment->total_amount);
            })
            ->editColumn('date', function ($petty) {
                if($petty->from != null && $petty->to != null) {
                    return Carbon::parse($petty->from)->toDateString() . ' - ' . Carbon::parse($petty->to)->toDateString();
                }

                if($petty->date != null)
                {
                    return Carbon::parse($petty->date)->toDateString();
                }

                return "-";
            })
            ->editColumn('status', function ($petty) {
                $status = '';
                if ($petty->status == 3) {
                    $status = 'Finance Approved';
                } else if ($petty->status == 4) {
                    $status = 'Paid';

                } else if ($petty->status == 5) {
                    $status = 'Adjusted';
                }
                return $status;
            })
            ->addColumn('action', function ($petty) {
                $route = route('admin.petty_cash.statements.edit', ['id' => $petty->statement_id]);
                $dropdown = '';
                if (session('role_id') == 1 || (session('role_id') == 2 || session('role_id') == 7 || session('role_id') == 14)) {
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                    $dropdown .= '<a href="' . $route . '" class="dropdown-item" ><i class="ft-eye"></i> View Details</a>';

                }
                return $dropdown;
            })
            ->make(true);
        return $petty;
    }

    public function approved_petty_cash_statements_paid(Request $request)
    {
        $id = $request->statement_id;
        if ($id) {
            $petty_details = PettyCashStatement::find($id);
            if ($petty_details) {
                if ($petty_details->status == 3) {
                    $petty_details->status = 4;
                    $petty_details->save();
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Successfully Paid!']);
                } else if ($petty_details->status == 4) {
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Already Paid!']);
                } else if ($petty_details->status == 5) {
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Is Adjust!']);
                }

            } else {
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement not found!']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement ID not found!']);
        }
    }

    public function approved_petty_cash_statements_adjusted(Request $request)
    {
        $id = $request->statement_id;
        if ($id) {
            $petty_details = PettyCashStatement::find($id);
            if ($petty_details) {
                if ($petty_details->status == 3) {
                    $petty_details->status = 5;
                    $petty_details->save();
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Successfully Adjust!']);
                } else if ($petty_details->status == 5) {
                    return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Already Adjust!']);
                } else if ($petty_details->status == 4) {
                    return response()->json(['status' => 0, 'error' => 'Petty Cash Statement Is Paid!']);
                }

            } else {
                return response()->json(['status' => 0, 'error' => 'Petty Cash Statement not found!']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Petty Cash Statement ID not found!']);
        }
    }

    public function approved_petty_cash_statements_bulk_adjusted(Request $request)
    {
        foreach ($request->statement_ids as $statement_id) {
            if ($statement_id) {
                $petty_details = PettyCashStatement::find($statement_id);
                if ($petty_details) {
                    if ($petty_details->status == 3) {
                        $petty_details->status = 5;
                        $petty_details->save();
                    }
                }
            }
        }
        return response()->json(['status' => 1, 'success' => 'Petty Cash Statement Successfully Adjust!']);
    }

    public function statement_print(Request $request)
    {
        $statement_id = $request->id;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Petty Cash Statement</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }

                      td.replacement span {
                        width: 22px;
                      }

                      td.replacement span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
                      }
                  
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $petty_cash_statement = PettyCashStatement::where('id', $statement_id);
        if ($petty_cash_statement->exists()) {
            $total_statements = 0;
            $statement_details = PettyCashStatementDetail::where('petty_cash_statement_id', $statement_id)->where('status', '!=', 1)->get();

            $petty_statement_details = '
                      <table class="table table-sm table-bordered border" style=" display: table-row-group;page-break-inside:avoid; page-break-after:auto;">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Chart of Account</strong></td>
                            <td class="color primary"><strong>Account Title</strong></td>
                            <td class="color primary"><strong>Details</strong></td>
                            <td class="color primary"><strong>Zone</strong></td>
                            <td class="color primary"><strong>Hub</strong></td>
                            <td class="color primary"><strong>Location</strong></td>
                            <td class="color primary"><strong>Reference No.</strong></td>
                            <td class="color primary"><strong>Employee Id</strong></td>
                            <td class="color primary"><strong>Employee Name</strong></td>
                            <td class="color primary"><strong>Employee Designation</strong></td>
                            <td class="color primary"><strong>Amount</strong></td>
                            <td class="color primary"><strong>Remarks</strong></td>
                          </tr>
        ';


            $petty_cash_statement = $petty_cash_statement->first();
            foreach ($statement_details as $detail) {
                $total_statements++;
                $detain_amount = 0;
                if ($detail->finance_amount !== null) {
                    $detain_amount = $detail->finance_amount;
                } else if ($detail->operation_amount !== null) {
                    $detain_amount = $detail->operation_amount;
                } else if ($detail->station_amount !== null) {
                    $detain_amount = $detail->station_amount;
                } else {
                    $detain_amount = $detail->amount;
                }

                $zone_name = "";
                $hub_name = "";
                $city_name = "";
                $employee_id = "";
                if($detail->zone_id != null)
                {
                    $zone_name = $detail->zone->name;
                }
                else{
                    $zone_name = $petty_cash_statement->zone->name;
                }

                if($detail->hub_id != null)
                {
                    $hub_name = $detail->location->name;
                }
                else{
                    $hub_name = $petty_cash_statement->hub->name;
                }

                if($detail->city_id != null)
                {
                    $city_name = $detail->city->name;
                }

                if($detail->employee_id != null)
                {
                    $employee_id = $detail->employee->trax_id;
                }
                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_statements . '</td>
                            <td>' . $detail->heads->name . '</td>
                            <td>' . $detail->titles->name . '</td>
                            <td>' . $detail->expense_details . '</td>
                            <td>' . $zone_name . '</td>
                            <td>' . $hub_name . '</td>
                            <td>' . $city_name . '</td>
                            <td>' . $detail->reference_no . '</td>
                            <td>' . $employee_id . '</td>
                            <td>' . $detail->employee_name . '</td>
                            <td>' . $detail->employee_designation . '</td>
                            <td>Rs ' . number_format($detain_amount) . '</td>
                            <td><p style="width:70px;overflow-wrap: break-word; display: inline-block">' . $detail->remarks . '</p></td>
                ';

                $shipment_details_row_start .= '
                          </tr>
                ';
                $petty_statement_details .= $shipment_details_row_start;
            }
            $petty_statement_details .= '
                        </tbody>
                      </table>
        ';
            $hub_name = "";

            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Petty Cash Statement</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Statement ID</strong></td>
                            <td>' . $petty_cash_statement->id . '</td>
                            <td rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>';
            if($petty_cash_statement->hub_id != null)
            {
                $hub_name = $petty_cash_statement->hub->name;
                $main_details .= '<tr>
                            <td class="color secondary"><strong>Hub</strong></td>
                            <td>' . $hub_name . '</td>
                          </tr>';
            }
            if($petty_cash_statement->sdn_id != null)
            {
                $main_details .= '<tr>
                            <td class="color secondary"><strong>SDN No.</strong></td>
                            <td>' . str_pad($petty_cash_statement->sdn_id, 6, '0', STR_PAD_LEFT) . '</td>
                          </tr>';
            }


            $main_details .= '<tr>
                            <td class="color secondary"><strong>Reference No.</strong></td>
                            <td>' . $petty_cash_statement->reference_no . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Period</strong></td>
                            <td>' . Carbon::parse($petty_cash_statement->date)->toDateString() .'</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Amount</strong></td>
                            <td>' . $petty_cash_statement->total_amount . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Statement Details</strong></td>
                            <td>' . $total_statements . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $petty_statement_details;

        }


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;
    }

    public function edit_petty_cash_statements_amount(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $detail = PettyCashStatementDetail::find($id);

            if ($detail) {
                $log_data = array();

                $log_data['actual'] = ($detail->amount !== null) ? $detail->amount : '';
                $log_data['station'] = ($detail->station_amount !== null) ? $detail->station_amount : '';
                $log_data['ope'] = ($detail->operation_amount !== null) ? $detail->operation_amount : '';
                $log_data['finance'] = ($detail->finance_amount !== null) ? $detail->finance_amount : '';

                return response()->json(['status' => 0, 'amount' => $log_data]);
            } else {
                return response()->json(['status' => 1, 'error' => 'No Details found!']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'No Petty Cash Statement Detail ID selected!']);

        }
    }

    public function petty_cash_statements_reject_all(Request $request)
    {
        $statement_id = $request->statement_id;
        $statement = PettyCashStatement::find($statement_id);
        if (in_array($statement->status, [0, 1, 2])) {
            $statement->status = 6;
            $statement->rejected_by = Auth::id();
            $statement->rejected_at = Carbon::now();
            $statement->save();

            $details = PettyCashStatementDetail::where('petty_cash_statement_id', $statement->id)->get();
            if ($details) {
                foreach ($details as $detail) {
                    PettyCashStatementDetail::where('id', $detail->id)->update(['status' => 1]);
                }
            }
            return response()->json(['status' => 0, 'success' => 'Statement Successfully rejected']);
        } else {
            return response()->json(['status' => 1, 'error' => 'Statement is already updated and can\'t be rejected!']);
        }
    }

    public function draft_petty_cash_statements_index()
    {
        return view('admin.petty_cash.draft.index');
    }

    public function draft_petty_cash_statements_list(Request $request)
    {

        $petty = PettyCashStatementDraft::leftjoin('cities as h', 'h.id', '=', 'petty_cash_statement_drafts.hub_id')
            ->leftjoin('cities as o', 'o.id', '=', 'petty_cash_statement_drafts.origin_hub_id')
            ->leftjoin('cities as d', 'd.id', '=', 'petty_cash_statement_drafts.destination_hub_id')
            ->join('admins as cb', 'cb.id', '=', 'petty_cash_statement_drafts.created_by')
            ->select('petty_cash_statement_drafts.id as draft_id', 'h.name as hub_name','o.name as origin_hub_name','d.name as destination_hub_name', 'petty_cash_statement_drafts.reference_no', 'petty_cash_statement_drafts.from', 'petty_cash_statement_drafts.to', 'cb.name as created_by', 'petty_cash_statement_drafts.created_at', 'petty_cash_statement_drafts.total_amount','petty_cash_statement_drafts.date');

        if (session('role_id') != 1) {
            $petty = $petty->where(function ($query) {
                $query->whereIn('petty_cash_statement_drafts.origin_hub_id', session('hubs'))
                ->orWhereIn('petty_cash_statement_drafts.destination_hub_id', session('hubs'))
                ->orWhere('petty_cash_statement_drafts.created_by', Auth::id())
                ->orWhereIn('petty_cash_statement_drafts.hub_id', session('hubs'));
            });
        }

        $petty = Datatables::of($petty)
            ->editColumn('total_amount', function ($shipment) {
                return number_format($shipment->total_amount);
            })
            ->addColumn('date', function ($petty) {
                if($petty->from != null && $petty->to != null) {
                    return Carbon::parse($petty->from)->toDateString() . ' - ' . Carbon::parse($petty->to)->toDateString();
                }

                if($petty->date != null)
                {
                    return Carbon::parse($petty->date)->toDateString();
                }

                return "-";
            })
            ->addColumn('action', function ($petty) {
                $route = route('admin.petty_cash.draft.edit', ['id' => $petty->draft_id]);
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                if ((session('role_id') == 1) || in_array(239, session('permissions'))) {
                    $dropdown .= '<a href="' . $route . '" class="dropdown-item" ><i class="ft-edit"></i> Edit</a>';
                }
                return $dropdown;
            });
        return $petty->make(true);
    }

    public function draft_edit_petty_cash_statement_index(Request $request, $id)
    {
        $draft = PettyCashStatementDraft::find($id);
        if ($draft) {
            $head = PettyCashAccountHead::select('id', 'name')->get();
            $titles_array = array();
            foreach ($head as $title) {
                $titles_array[$title->id] = $title->account_titles;
            }

            $zones = Zone::where('business_category_id',1)->select('id','name')->where('status',1)->get();
            $hub_array = array();
            $city_array = array();
            $dncc_array = "";
            foreach ($zones as $zone) {
                if($zone->id == $draft->zone_id) {
                    $hub_array[$zone->id] = $zone->zone_cities->where('hub', 1);
                    foreach ($zone->zone_cities as $hub) {
                        if($hub->id == $draft->hub_id) {
                            $city_array[$hub->id] = $hub->hub_cities_including_self;
                        }
                    }
                }
            }

            $employees = Admin::where('trax_id','!=',null)->where('status',1)->select(['id','trax_id'])->get();
            $operation_managers = Admin::where('role_id',10)->where('status',1)->select(['id','trax_id','name'])->get();
            if (session('role_id') == 1) {
                $sdns = StationDepositNote::where('status','!=', 2)->select('id')->get();
            } else {
                $sdns = StationDepositNote::where('status','!=', 2)->whereIn('hub_id', session('hubs'))->select('id')->get();
            }

            foreach ($sdns as $sdn)
            {
                if($sdn->id == $draft->sdn_id)
                {
                    $response = $this::make_petty_cash_statement_get_dncc_static($sdn->id);
                    if($response['status'] == 1)
                    {
                        foreach ($response['data'] as $data)
                        {
                            $id = $data['id'];
                            $count = $data['count'];
                            $text = $data['text'];
                            $dncc_array .= "<option value='" . $id . "' data-count='" . $count . "'>" . $text . "</option>";
                        }
                    }
                }
            }

            return view('admin.petty_cash.draft.edit')->with(['heads' => $head, 'titles' => $titles_array, 'petty_statement_draft' => $draft,'zones'=>$zones,'employees'=>$employees,'sdns'=>$sdns,'hub_array'=>$hub_array,'city_array'=>$city_array,'operation_managers'=>$operation_managers,'dncc_array'=>$dncc_array]);
        } else {
            return redirect()->route('admin.petty_cash.draft.index')->with('error', 'Draft not found!');
        }
    }

    public function draft_edit_petty_cash_statement_list(Request $request, $id)
    {
        $petty_details = PettyCashStatementDetailDraft::leftjoin('cities as h', 'h.id', '=', 'petty_cash_statement_detail_drafts.hub_id')
            ->join('petty_cash_statement_drafts as pcs', 'pcs.id', '=', 'petty_cash_statement_detail_drafts.petty_cash_statement_draft_id')
            ->select('petty_cash_statement_detail_drafts.id as draft_detail_id', 'h.name as hub', 'petty_cash_statement_detail_drafts.hub_id', 'petty_cash_statement_detail_drafts.account_head_id', 'petty_cash_statement_detail_drafts.account_title_id', 'petty_cash_statement_detail_drafts.date', 'petty_cash_statement_detail_drafts.expense_details', 'petty_cash_statement_detail_drafts.amount', 'petty_cash_statement_detail_drafts.reference_no', 'petty_cash_statement_detail_drafts.remarks', 'petty_cash_statement_detail_drafts.reference_document as reference_document')
            ->where('petty_cash_statement_detail_drafts.petty_cash_statement_draft_id', $id);
//        dd($petty_details);
        return Datatables::of($petty_details)
            ->addColumn('account_head', function ($petty_details) {

                $heads = PettyCashAccountHead::select('id', 'name')->get();
                $drops = '';
                $selected = '';
                foreach ($heads as $status) {
                    if ($status->id == $petty_details->account_head_id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $drops .= '<option value="' . $status->id . '" ' . $selected . '>' . $status->name . '</option>';
                }
                $select = '<select class="form-control form-control-sm select2 head_select" name="head[' . $petty_details->petty_cash_statement_draft_id . ']" data-rule-required="true" data-msg-required="Account Head is required">' . $drops . '</select>';
                return $select;

            })
            ->addColumn('account_title', function ($petty_details) {

                $account_head_id = $petty_details->account_head_id;
                $petty_cash_account_title_ids = PettyCashAccountHeadAccountTitle::where('petty_cash_account_head_id', $account_head_id)->pluck('petty_cash_account_title_id')->toArray();
                $titles = PettyCashAccountTitle::select('id', 'name')->whereIn('id', $petty_cash_account_title_ids)->get();
                $drops = '';
                $selected = '';
                foreach ($titles as $status) {
                    if ($status->id == $petty_details->account_title_id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $drops .= '<option value="' . $status->id . '" ' . $selected . '>' . $status->name . '</option>';
                }
                $select = '<select class="form-control form-control-sm select2 title_select" name="title[' . $petty_details->petty_cash_statement_draft_id . ']" data-rule-required="true" data-msg-required="Account Title is required">' . $drops . '</select>';
                return $select;

            })
            ->addColumn('hub_name', function ($petty_details) {
                if ($petty_details->hub_id != null) {
                    $hubs = City::select('id', 'name')->where('status',1)->get();
                    $drops = '';
                    $selected = '';
                    foreach ($hubs as $hub) {
                        if ($hub->id == $petty_details->hub_id) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        $drops .= '<option value="' . $hub->id . '" ' . $selected . '>' . $hub->name . '</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 hub_select" name="hub[' . $petty_details->petty_cash_statement_draft_id . ']" data-rule-required="true" data-msg-required="Hub is required">' . $drops . '</select>';
                    return $select;
                } else {
                    $hubs = City::where('hub', 1)->select('id', 'name')->where('status',1)->get();
                    $drops = '';

                    $drops .= '<option value="" selected></option>';
                    foreach ($hubs as $hub) {
                        $drops .= '<option value="' . $hub->id . '">' . $hub->name . '</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 hub_select" name="hub[' . $petty_details->petty_cash_statement_draft_id . ']" data-rule-required="true" data-msg-required="Hub is required">' . $drops . '</select>';
                    return $select;
                }

            })
            ->editColumn('date', function ($petty_details) {
                return Carbon::parse($petty_details->date)->toDateString();
            })
            ->editColumn('expense_details', function ($petty_details) {
                $expense_detail = str_replace(array("\n", "\r"), '', $petty_details->expense_details);
                $expense = '<textarea class="form-control form-control-sm" name="expense[' . $petty_details->statement_detail_id . ']" data-rule-required="true" data-msg-required="Expense Detail is required">' . $expense_detail . '</textarea>';
                return $expense;
            })
            ->editColumn('amount', function ($petty_details) {
                $selected_amount = '';

                $selected_amount = $petty_details->amount;


                $amount = '<input type="text" class="form-control form-control-sm amount" value="' . $selected_amount . '" name="amount[' . $petty_details->petty_cash_statement_draft_id . ']" data-rule-required="true" data-msg-required="Amount is required">';
                return $amount;
            })
            ->editColumn('reference_no', function ($petty_details) {
                $reference = '<input class="form-control form-control-sm" value="' . $petty_details->reference_no . '" name="reference[' . $petty_details->petty_cash_statement_draft_id . ']" data-rule-required="true" data-msg-required="Reference No. is required">';
                return $reference;
            })
            ->editColumn('remarks', function ($petty_details) {
                $rem = str_replace(array("\n", "\r"), '', $petty_details->remarks);
                $remarks = '<textarea class="form-control form-control-sm" name="remarks[' . $petty_details->petty_cash_statement_draft_id . ']">' . $rem . '</textarea>';
                return $remarks;
            })
            ->editColumn('reference_document', function ($petty_details) {
                $reference_document = '<div class="text-center">';
                if ($petty_details->reference_document != null) {
                    $reference_document .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href=' . route('admin.petty_cash.statements.reference_document', [$petty_details->reference_document]) . ' target="_blank">View</a></button>';
                }
                $reference_document .= '<input class="form-control form-control-sm" style="min-width: 200px;" type="file" name="upload_image' . $petty_details->statement_detail_id . '" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)."></div>';
                return $reference_document;
            })
            ->addColumn('action', function ($petty) {
                $dropdown = '';
                $dropdown .= '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';
                return $dropdown;
            })
            ->make(true);
    }

    public function draft_edit_petty_cash_statements_submit(Request $request)
    {
        if ($request->has('submit_button')) {
            $selected_ids = explode(',', $request->input('selected_rows'));
            $draft_id = $request->petty_draft_id;
            $petty_cash_draft = PettyCashStatementDraft::find($draft_id);
            if ($petty_cash_draft) {
                $petty_cash_draft->petty_cash_statement_draft_details()->delete();
            }
            $total_amount = 0;

            if ($petty_cash_draft) {
                if ($request->input('submit_button') == 'draft') {
                    foreach ($selected_ids as $selected_id) {
                        $total_amount += $request->amount[$selected_id];
                        $petty_cash_draft_detail = new PettyCashStatementDetailDraft();

                        $petty_cash_draft_detail->petty_cash_statement_draft_id = $draft_id;
                        $petty_cash_draft_detail->account_head_id = $request->head[$selected_id];
                        $petty_cash_draft_detail->account_title_id = $request->title[$selected_id];
                        $petty_cash_draft_detail->city_id = $request->city[$selected_id];
                        $petty_cash_draft_detail->employee_id = $request->employee[$selected_id];
                        $petty_cash_draft_detail->employee_name = $request->employee_name[$selected_id];
                        $petty_cash_draft_detail->employee_designation = $request->employee_designation[$selected_id];
                        $petty_cash_draft_detail->dncc_id = $request->dncc[$selected_id] ?? Null;
                        $petty_cash_draft_detail->delivered_shipments = $request->delivered_shipment_count[$selected_id] ?? Null;
                        $petty_cash_draft_detail->expense_details = $request->expense[$selected_id];
                        $petty_cash_draft_detail->reference_no = $request->reference[$selected_id];
                        $petty_cash_draft_detail->remarks = $request->remarks[$selected_id];
                        $petty_cash_draft_detail->amount = $request->amount[$selected_id];
                        $petty_cash_draft_detail->save();
                        $image_key = "image_$selected_id";
                        if ($request->hasFile('upload_image' . $selected_id)) {

                            $file = $request->file('upload_image' . $selected_id);
                            $filename = 'statement_' . $petty_cash_draft->id . '_detail_' . $petty_cash_draft_detail->id . '.'.$file->getClientOriginalExtension();


                            if ($request->has($image_key)) {

                                Storage::disk('public')->delete('petty_cash_statement_details_draft/' . $request->input($image_key));
                            }
                            Storage::disk('public')->putFileAs('petty_cash_statement_details_draft/', $file, $filename);

                            $petty_cash_draft_detail->reference_document = $filename;

                        } else {
                            if ($request->has($image_key)) {
                                $extension = explode('.',$request->input($image_key));
                                $filename = 'statement_' . $petty_cash_draft->id . '_detail_' . $petty_cash_draft_detail->id . '.'.end($extension);
                                //                            return $request->input($image_key);
                                Storage::disk('public')->move('petty_cash_statement_details_draft/' . $request->input($image_key), 'petty_cash_statement_details_draft/' . $filename);

                                $petty_cash_draft_detail->reference_document = $filename;
                            }
                        }

                        $image_2_key = "image_2_$selected_id";
                        if ($request->hasFile('upload_2_image' . $selected_id)) {

                            $file = $request->file('upload_2_image' . $selected_id);
                            $filename = 'statement_2_' . $petty_cash_draft->id . '_detail_' . $petty_cash_draft_detail->id . '.'.$file->getClientOriginalExtension();


                            if ($request->has($image_2_key)) {

                                Storage::disk('public')->delete('petty_cash_statement_details_draft/' . $request->input($image_2_key));
                            }
                            Storage::disk('public')->putFileAs('petty_cash_statement_details_draft/', $file, $filename);

                            $petty_cash_draft_detail->reference_document_2 = $filename;

                        } else {
                            if ($request->has($image_2_key)) {
                                $extension = explode('.',$request->input($image_2_key));
                                $filename = 'statement_2_' . $petty_cash_draft->id . '_detail_' . $petty_cash_draft_detail->id . '.'.end($extension);
                                Storage::disk('public')->move('petty_cash_statement_details_draft/' . $request->input($image_2_key), 'petty_cash_statement_details_draft/' . $filename);

                                $petty_cash_draft_detail->reference_document_2 = $filename;
                            }
                        }

                        $petty_cash_draft_detail->save();

                    }
                    $petty_cash_draft->total_amount = $total_amount;
                    $petty_cash_draft->sdn_id = $request->select_statement_sdn;
                    $petty_cash_draft->zone_id = $request->select_statement_zone;
                    $petty_cash_draft->hub_id = $request->select_statement_hub;
                    $petty_cash_draft->date = $request->select_statement_date_formatted;
                    $petty_cash_draft->destination_hub_id = Admin::find($request->select_statement_station_manager)->default_hub_id ?? 0;
                    $petty_cash_draft->station_manager_id = $request->select_statement_station_manager;
                    $petty_cash_draft->save();
                    return redirect()->back()->with(['status' => 1, 'success' => 'Petty Cash Statement Draft Successfully Updated!']);
                } else {
                    $petty_cash = new PettyCashStatement();
                    $petty_cash->zone_id = $request->select_statement_zone;
                    $petty_cash->hub_id =  $request->select_statement_hub;
                    $petty_cash->reference_no = $petty_cash_draft->reference_no;
                    $petty_cash->date = $request->select_statement_date_formatted;
                    $petty_cash->sdn_id = $request->select_statement_sdn;
                    $petty_cash->origin_hub_id = Auth::user()->default_hub_id ?? 0;
                    $petty_cash->destination_hub_id = Admin::find($request->select_statement_station_manager)->default_hub_id ?? 0;
                    $petty_cash->station_manager_id = $request->select_statement_station_manager;
                    $petty_cash->created_by = Auth::id();
                    $petty_cash->save();
                    foreach ($selected_ids as $selected_id) {
                        $total_amount += $request->amount[$selected_id];

                        $petty_detail = new PettyCashStatementDetail();
                        $petty_detail->petty_cash_statement_id = $petty_cash->id;
                        $petty_detail->account_head_id = $request->head[$selected_id];
                        $petty_detail->account_title_id = $request->title[$selected_id];
                        $petty_detail->city_id = $request->city[$selected_id];
                        $petty_detail->employee_id = $request->employee[$selected_id];
                        $petty_detail->employee_name = $request->employee_name[$selected_id];
                        $petty_detail->employee_designation = $request->employee_designation[$selected_id];
                        $petty_detail->dncc_id = $request->dncc[$selected_id] ?? Null;
                        $petty_detail->delivered_shipments = $request->delivered_shipment_count[$selected_id] ?? Null;
                        $petty_detail->expense_details = $request->expense[$selected_id];
                        $petty_detail->amount = $request->amount[$selected_id];
                        $petty_detail->reference_no = $request->reference[$selected_id];
                        $petty_detail->remarks = $request->remarks[$selected_id];
                        $petty_detail->save();
                        $image_key = "image_$selected_id";

                        if ($request->hasFile('upload_image' . $selected_id)) {
                            $file = $request->file('upload_image' . $selected_id);
                            $filename = 'statement_' . $petty_cash->id . '_detail_' . $petty_detail->id . '.'.$file->getClientOriginalExtension();

                            if ($request->has($image_key)) {
                                Storage::disk('public')->delete('petty_cash_statement_details_draft' . $request->input($image_key));
                            }
                            Storage::disk('public')->putFileAs('petty_cash_statement_details', $file, $filename);

                            $petty_detail->reference_document = $filename;
                            $petty_detail->save();
                        } else {
                            if ($request->has($image_key)) {

                                $extension = explode('.',$request->input($image_key));
                                $filename = 'statement_' . $petty_cash->id . '_detail_' . $petty_detail->id . '.'.end($extension);
                                Storage::disk('public')->move('petty_cash_statement_details_draft/' . $request->input($image_key), 'petty_cash_statement_details/' . $filename);

                                $petty_detail->reference_document = $filename;
                                $petty_detail->save();


                            }
                        }

                        $image_2_key = "image_2_$selected_id";

                        if ($request->hasFile('upload_2_image' . $selected_id)) {
                            $file = $request->file('upload_2_image' . $selected_id);
                            $filename = 'statement_2_' . $petty_cash->id . '_detail_' . $petty_detail->id . '.'.$file->getClientOriginalExtension();

                            if ($request->has($image_2_key)) {
                                Storage::disk('public')->delete('petty_cash_statement_details_draft' . $request->input($image_2_key));
                            }
                            Storage::disk('public')->putFileAs('petty_cash_statement_details', $file, $filename);

                            $petty_detail->reference_document_2 = $filename;
                            $petty_detail->save();
                        } else {
                            if ($request->has($image_2_key)) {

                                $extension = explode('.',$request->input($image_2_key));
                                $filename = 'statement_2_' . $petty_cash->id . '_detail_' . $petty_detail->id . '.'.end($extension);
                                Storage::disk('public')->move('petty_cash_statement_details_draft/' . $request->input($image_2_key), 'petty_cash_statement_details/' . $filename);

                                $petty_detail->reference_document_2 = $filename;
                                $petty_detail->save();
                            }
                        }

                    }
                    $petty_cash_draft->delete();
                    PettyCashStatement::where('id', $petty_cash->id)->update(['total_amount' => $total_amount]);

                    $shipment_id = $this->create_shipment($petty_cash->id);
                    $petty_cash->shipment_id = $shipment_id;
                    $petty_cash->save();

                    return redirect()->route('admin.petty_cash.statements.index')->with(['status' => 1, 'success' => 'Petty Cash Statement Successfully Created']);
                }
            } else {
                return redirect()->back()->with(['status' => 0, 'error' => 'Petty Cash Statement Draft With This ID Not Found!']);
            }
        }
    }

    static public function archive_directory()
    {
        $files = File::glob(public_path() . '/storage/petty_cash_statement_details/*.*');
        $now = Carbon::now();
        foreach ($files as $file) {
            $created = date("F d Y H:i:s.", filemtime($file));
            $file_name = pathinfo($file);
            if ($now->diffInDays($created) > 1) {
                Storage::disk('s3')->put('petty_cash_statement_images/' . $file_name['basename'], file_get_contents($file));
                if (Storage::disk('s3')->exists('petty_cash_statement_images/' . $file_name['basename'])) {
                    File::delete($file);
                }
            }
        }
    }

    public function create_shipment($petty_cash_statement_id)
    {
        $user_id = 1690;
        $user = User::find($user_id);

        if ($petty_cash_statement_id) {
            $petty_cash_statement = PettyCashStatement::find($petty_cash_statement_id);
            $city_id = Auth::user()->default_hub_id;
            $consignee = Admin::find($petty_cash_statement->station_manager_id);
            $consignee_name = $consignee->name;
            $consignee_number = $consignee->phone_number;
            $consignee_email = $consignee->email ?? "-";
            $consignee_city_id = $consignee->default_hub_id ?? $petty_cash_statement->hub_id;
            $consignee_city_name = City::where('id',$consignee_city_id)->pluck("name")->first();


            $special_instructions = 'Petty Cash Statement # ' . $petty_cash_statement_id;
            $pickup = UserShippingInfo::where('user_id', $user_id)->where('city_id', $city_id);
            $pickup_address_id = '';
            if ($pickup->exists()) {
                $pickup = $pickup->first();
                $pickup_address_id = $pickup->id;
            } else {
                $poc = $user->name;
                $poc_phone = $user->phone;
                $poc_email = $user->email;
                $city_name = City::where('id',$city_id)->pluck("name")->first();
                $address = 'Trax office ' . $city_name;
                $pickup_address_id = $this->add_pickup_address($user_id, $address, $poc, $poc_phone, $poc_email, $city_id);
            }

            $shipment = $this->book($user_id, 1, $pickup_address_id, 1, $consignee_city_id,$consignee_name, 'Trax Office '.$consignee_city_name, $consignee_number, NULL, $consignee_email, NULL, 0, Carbon::now(), $special_instructions, 1, 1, NULL, 0, 1, 2, 2);

            $tracking_number = $this->generate_tracking_number($shipment->id, $city_id, $consignee_city_id);
            $this->add_item($shipment->id, 24, $special_instructions, 1, null, 0, 0);
            ShipmentsJourneyController::add($shipment->id, 1, 1, NULL, NULL, NULL, Auth::id(), $petty_cash_statement_id);
            ShipmentsJourneyController::add($shipment->id, 2, 2, NULL, NULL, NULL, Auth::id(), $petty_cash_statement_id);
            return $shipment->id;
        }
    }

    public function add_pickup_address($user_id, $address, $person_of_contact, $phone_number, $email_address, $city_id)
    {

        $user_shipping_info = new UserShippingInfo();

        $user_shipping_info->user_id = $user_id;
        $user_shipping_info->pickup_address = $address;
        $user_shipping_info->poc = $person_of_contact;
        $user_shipping_info->phone = $phone_number;
        $user_shipping_info->email = $email_address;
        $user_shipping_info->city_id = $city_id;

        $user_shipping_info->save();

        return $user_shipping_info->id;
    }

    private function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $shipper_status_id, $consignee_status_id)
    {
        $shipment = new Shipment();

        $shipment->user_id = $user_id;
        $shipment->booking_type_id = $service_type_id;
        $shipment->pickup_address_id = $pickup_address_id;
        $shipment->information_display = $information_display;

        $shipment->consignee_city_id = $consignee_city_id;
        $shipment->consignee_name = $consignee_name;
        $shipment->consignee_address = $consignee_address;
        $shipment->consignee_phone_number_1 = $consignee_phone_number_1;
        $shipment->consignee_phone_number_2 = $consignee_phone_number_2;
        $shipment->consignee_email = $consignee_email_address;

        $shipment->order_id = $order_id;
        $shipment->package_type = $package_type;
        $shipment->pickup_date = $pickup_date;
        $shipment->special_instructions = $special_instructions;


        $shipment->estimated_weight = $estimated_weight;
        $shipment->shipping_mode_id = $shipping_mode_id;
        $shipment->same_day_timing_id = $same_day_timing_id;

        $shipment->amount = $amount;
        $shipment->payment_mode_id = $payment_mode_id;
        $shipment->shipper_status_id = $shipper_status_id;
        $shipment->consignee_status_id = $consignee_status_id;

        $shipment->save();

        return $shipment;
    }

    private function add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type)
    {
        $shipment_item = new ShipmentItem();

        $shipment_item->shipment_id = $shipment_id;
        $shipment_item->product_type_id = $product_type_id;
        $shipment_item->description = $item_description;
        $shipment_item->quantity = $item_quantity;
        $shipment_item->price = $price;
        $shipment_item->insurance = $insurance;
        $shipment_item->type = $type;

        $shipment_item->save();
    }

    private function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id)
    {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        return $tracking_number;
    }

    public function petty_cash_station_operation_finance_approved_all(Request $request)
    {
        $action = $request->action;
        $flag = FALSE;
        $statement_ids = $request->statement_ids;
        if (count($statement_ids) > 0) {
            foreach ($statement_ids as $statement_id) {
                $petty = PettyCashStatement::find($statement_id);
                if ($petty)
                {
                    if($action == 'station'){
                        if ($petty->station_approved_by == null) {
                            if (session('role_id') == 1 || in_array(190, session('permissions'))) {
                                $petty->station_approved_by = Auth::id();
                                $petty->station_approved_at = Carbon::now();
                                $petty->status = 1;
                                $petty->save();
                                $this->petty_cash_statement_details_auto_approve($request->statement_id);
                                $flag = TRUE;
                            }
                        }
                    }
                    else if($action == 'operation'){
                        if ($petty->station_approved_by != null && $petty->operation_approved_by == null) {
                            if (session('role_id') == 1 || in_array(191, session('permissions'))) {
                                $petty->operation_approved_by = Auth::id();
                                $petty->operation_approved_at = Carbon::now();
                                $petty->status = 2;
                                $petty->save();
                                $this->petty_cash_statement_details_auto_approve($request->statement_id);
                                $flag = TRUE;
                            }
                        }
                    }
                    else if($action == 'finance'){
                        if ($petty->finance_received_statement_by != null && $petty->finance_received_by == null) {
                            if (session('role_id') == 1 || in_array(173, session('permissions'))) {
                                $petty->finance_approved_by = Auth::id();
                                $petty->finance_approved_at = Carbon::now();
                                $petty->status = 3;
                                $petty->save();
                                $this->petty_cash_statement_details_auto_approve($request->statement_id);
                                $flag = TRUE;
                            }
                        }
                    }
                }
            }
            if ($flag) {
                return response()->json(['status' => 0, 'success' => 'Approved!']);
            }
            else {
                return response()->json(['status' => 1, 'error' => 'Already approved / Previous status not updated!']);
            }
        }
        return response()->json(['status' => 1, 'error' => 'No Statement Ids selected!']);
    }

    public function petty_cash_statement_check(Request $request){
        $statement_ids = $request->statement_ids;
        if (count($statement_ids) > 0) {
            foreach ($statement_ids as $statement_id) {
                $petty = PettyCashStatement::find($statement_id);
                if ($petty)
                {
                    if($petty->checked_by == null){
                        $petty->checked_by = Auth::id();
                        $petty->checked_at = Carbon::now();
                        $petty->save();
                    }
                }
            }
            return response()->json(['status' => 0, 'success' => 'Checked!']);
        }
        return response()->json(['status' => 1, 'error' => 'No Statement Ids selected!']);
    }

    public function edit_petty_cash(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'head_id' => 'required|int|max:255',
                'title_id' => 'required|int|max:255',
                'reference_document' => 'image|mimes:jpeg,png,jpg,gif',
                'reference_document2' => 'image|mimes:jpeg,png,jpg,gif',
            ],
            [
                'head_id.required' => 'The head field is required.',
                'title_id.required' => 'The title field is required.',
                'reference_document' => 'The email field must be a valid email address.',
                'reference_document2' => 'The email address has already been taken.',
            ]);

        if ($validator->fails()) {

            return redirect()->back()->with('error',$validator->errors()->first());
        }

        $petty_cash_detail_id = $request->petty_cash_id;
        $head_id = $request->head_id;
        $title_id = $request->title_id;
        $reference_document = $request->reference_document;
        $reference_document2 = $request->reference_document_2;

        $existing_petty_detail = PettyCashStatementDetail::where('id', $petty_cash_detail_id)
            ->select('id','account_head_id','account_title_id', 'petty_cash_statement_id', 'reference_document', 'reference_document_2');

        if ($existing_petty_detail->exists()) {
            $petty_detail = $existing_petty_detail->first();

            $update_petty_cash_detail = PettyCashStatementDetail::where('id', $petty_cash_detail_id)
                ->update(['account_head_id' => $head_id, 'account_title_id' => $title_id,'edit_by'=>Auth::id(),'edit_at'=>Carbon::now()]);


            if ($request->file('reference_document')) {
                //todo: remove image first usin veriable $petty_detail
                //todo: remove image first using veriable $petty_detail end

                $file = $request->file('reference_document');
                $filename = 'statement_' . $petty_detail->petty_cash_statement_id . '_detail_' . $petty_cash_detail_id . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('petty_cash_statement_details/', $file, $filename);

                $update_petty_cash = PettyCashStatementDetail::where('id', $petty_cash_detail_id)->update(['reference_document' => $filename]);
            }

            if ($request->file('reference_document_2')) {
                //todo: remove image first usin veriable $petty_detail
                //todo: remove image first usin veriable $petty_detail end

                $file = $request->file('reference_document_2');
                $filename = 'statement_2_' . $petty_detail->petty_cash_statement_id . '_detail_' . $petty_cash_detail_id . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('petty_cash_statement_details/', $file, $filename);

                $update_petty_cash = PettyCashStatementDetail::where('id', $petty_cash_detail_id)->update(['reference_document_2' => $filename]);
            }

            return redirect()->back()->with('success','Updated !');

        } else {

            return redirect()->back()->with('error','Petty Cash Details Not Found !!');
        }
    }

    public function edit_petty_cash_amount(Request $request)
    {
        $petty_cash_detail_id = $request->petty_cash_id;
        $amount = $request->amount;

        $existing_petty_detail = PettyCashStatementDetail::where('id', $petty_cash_detail_id)
            ->select('id','amount', 'petty_cash_statement_id', 'reference_document', 'reference_document_2');
        if ($existing_petty_detail->exists()) {
            $petty_detail = $existing_petty_detail->first();

            $update_petty_cash_detail = PettyCashStatementDetail::where('id', $petty_cash_detail_id)
                ->update(['amount' => $amount,'edit_by'=>Auth::id(),'edit_at'=>Carbon::now()]);

            $existing_petty_cash = PettyCashStatement::where('id',$petty_detail->petty_cash_statement_id)
                ->select('total_amount')
                ->first();

            $update_petty_cash = PettyCashStatement::where('id',$petty_detail->petty_cash_statement_id)
                ->update(['total_amount'=>$existing_petty_cash->total_amount - $petty_detail->amount + $amount]);

//            $data = response()->json([
//                'status' => 1,
//                'message' => 'Updated !!',
//            ]);
            return redirect()->back()->with('success','Updated !');
        } else {
//            $data = response()->json([
//                'status' => 0,
//                'message' => 'Petty Cash Details Not Found !!',
//            ]);
            return redirect()->back()->with('error','Petty Cash Details Not Found !!');
        }
//        return $data;
    }
}
