<?php

namespace App\Http\Controllers\Retail;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Admin\Retail\OtherParcelReceiving;
use App\Http\Models\Admin\Retail\OtherParcelReceivingShipment;
use App\Http\Models\Admin\Retail\OtherRetailShipment;
use App\Http\Models\Admin\Retail\RetailParcelReceiving;
use App\Http\Models\Admin\Retail\RetailParcelReceivingShipment;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShippingMode;
use App\Http\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\TraxRetailShipperFlyerRequest;
use Illuminate\Support\Facades\Validator;



class FlyerShipperController extends Controller
{
    
    public function __construct()
    {
    $this->middleware('auth:retail');

//        $this->middleware('Permission');
}
    public function other_index()
    {
       return view('retail.flyer_shipper.flyer_shipper_reports');
       
    }

    private $names = [
        'phone_number' => 'Phone Number',
        'pin' => 'PIN',

        'rider_location_latitude' => 'Rider Location Latitude',
        'rider_location_longtidue' => 'Rider Location Longitude',

        'added_at' => 'Added At',
        'pickup_note_id' => 'Pickup Note ID',
        'pickup_request_id' => 'Pickup Request ID',
        'actual_location_latitude' => 'Location Latitude',
        'actual_location_longtidue' => 'Location Longitude',
        'start_location_latitude' => 'Location Latitude',
        'start_location_longitude' => 'Location Longitude',

        'shipments' => 'Shipments',

        'reason_id' => 'Reason ID',
        'picture' => 'Picture',

        'delivery_note_id' => 'Delivery Note ID',
        'receiver_name' => 'Receiver Name',
        'cnic' => 'CNIC',

        'shipper_status_id' => 'Shipper Status ID',
        'status_reason_id' => 'Status Reason ID',
        'remarks' => 'Remarks',

        'actions' => 'Actions',
        'actions.*' => 'Action',
        'actions.*.logged_at' => 'Logged At',
        'actions.*.type_id' => 'Type ID',
        'actions.*.pickup_note_id' => 'Pickup Note ID',
        'actions.*.pickup_request_id' => 'Pickup Request ID',

        'from_date' => 'From Date'
    ];

    private $messages = [
        'phone_number.regex' => ':attribute format is Invalid, required Format is: 0300-0000000.',
        'integer' => ':attribute must be an Integer.',
        'digits' => ':attribute must be of :digits Digits.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'actual_location_latitude.regex' => ':attribute is Invalid Latitude Coordinates.',
        'actual_location_longitude.regex' => ':attribute is Invalid Longitude Coordinates.',
        'start_location_latitude.regex' => ':attribute is Invalid Latitude Coordinates.',
        'start_location_longitude.regex' => ':attribute is Invalid Longitude Coordinates.',
        'image' => ':attribute must be an Image.',
        'rider_location_latitude.regex' => ':attribute is Invalid Latitude Coordinates.',
        'rider_location_longitude.regex' => ':attribute is Invalid Longitude Coordinates.',
    ];

    public function other_list(Request $request)
    {
    

        if ($request->ajax()) {
            $other_parcel_list = TraxRetailShipperFlyerRequest::select(
                'trax_retail_shipper_flyer_requests.id as id',
                'resh.shipper_name',
                'ptype.type as package_type',
                'typesize.size',
                'tcen.name as center_name',
                'trax_retail_shipper_flyer_requests.qty',
                'trax_retail_shipper_flyer_requests.amount',
                'trax_retail_shipper_flyer_requests.status'
            )
            ->join('retail_shipper_infos as resh', 'resh.id', '=', 'trax_retail_shipper_flyer_requests.retail_shipper_id')
            ->join('packaging_material_type_sizes as typesize', 'typesize.id', '=', 'trax_retail_shipper_flyer_requests.type_size_id')
            ->join('packaging_material_types as ptype', 'ptype.id', '=', 'typesize.type_id')
            ->join('retail_trax_centers as tcen', 'tcen.id', '=', 'trax_retail_shipper_flyer_requests.trax_centre_id')
            ->get();

            // Debugging: Check if $other_parcel_list is not null
            if ($other_parcel_list->isEmpty()) {
                return response()->json(['error' => 'No data found'], 404);
            }

            return DataTables::of($other_parcel_list)
            ->editColumn('status', function($row) {
                    //     // Example: Change status value based on condition
                        if ($row->status == '0') {
                            return '<p style="color:black">Pending</p>';
                        } elseif ($row->status == '1') {
                            return '<p  style="color:blue">Confirmed</p>';
                        } elseif ($row->status == '2') {
                            return '<p style="color:red">Rejected</p>';
                        } elseif ($row->status == '3') {
                            return '<p style="color:green">Completed</p>';
                        } else {
                            return $row->status;
                        }
                    })
                ->addColumn('action', function($row){
                    // Create your action buttons here
                    $dropdown = '';
                    if($row->status==2 || $row->status==3){
                        $dropdown = '';
                    }else {

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';
                        if($row->status==0){
                            $dropdown .= '<button type="button" id="rejected" class="dropdown-item" value=' . $row->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Rejected</div></button>';

                            $dropdown .= '<button type="button" id="confirmed" class="dropdown-item" value=' . $row->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Confirmed</div></button>';
        
                            $dropdown .= '
                                </div>
                              </div>
                            ';
                        }else if($row->status==1){
                            $dropdown .= '<button type="button" id="completed" class="dropdown-item" value=' . $row->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Completed</div></button>';
        
                            $dropdown .= '
                                </div>
                              </div>
                            ';
                        }
                    
                  
                   
                    }
                    return $dropdown;
                })
                ->make(true);
        }
        return view('view');
        
    }

    public function other_generate()
    {
        $other_retail_shipments = OtherRetailShipment::where('retail_user_id', Auth::id())->where('parcel_receiving', 0);
        if ($other_retail_shipments->exists()) {
            $total_cn = 0;
            $other_retail_shipments = $other_retail_shipments->get();
            $other_parcel_receiving = new OtherParcelReceiving();
            $other_parcel_receiving->category = Auth::user()->category;
            $other_parcel_receiving->retail_user_id = Auth::id();
            $other_parcel_receiving->total_cn = 0;
            $other_parcel_receiving->save();

            foreach ($other_retail_shipments as $other_retail_shipment) {
                $total_cn++;
                $other_parcel_receiving_shipment = new OtherParcelReceivingShipment();
                $other_parcel_receiving_shipment->other_parcel_receiving_id = $other_parcel_receiving->id;
                $other_parcel_receiving_shipment->shipment_id = $other_retail_shipment->shipment_id;
                $other_parcel_receiving_shipment->save();

                $other_retail_shipment->parcel_receiving = 1;
                $other_retail_shipment->save();
            }

            $other_parcel_receiving->total_cn = $total_cn;
            $other_parcel_receiving->save();
            return response()->json(['status' => 0, 'success' => 'Other Parcel Receiving Sheet generated with Retail Note: ' . str_pad($other_parcel_receiving->id, 6, '0', STR_PAD_LEFT), 'other_parcel_receiving_id' => $other_parcel_receiving->id]);
        } else {
            return response()->json(['status' => 1, 'error' => 'Shipments not found!']);
        }
    }

    public function shipper_change_status(Request $request){

        $rules = [
            'retail_shipper_request_id' =>  ['required'],
            'status_type'=>['required']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);
        $validate->setAttributeNames($this->names);

        $retail_shipper_request_id = $request->retail_shipper_request_id;
        $result = TraxRetailShipperFlyerRequest::where('id', $retail_shipper_request_id)->first();
        if($result){
            $status_type = $request->status_type;
            if($status_type=='Confirmed'){
                $result->status = "1";
                $result->save();
            }else if($status_type=='Rejected'){
                $result->status = "2";
                $result->save();
            }else if($status_type=='Completed'){
                $result->status = "3";
                $result->save();
            }
            return response()->json(["status" => 0, "message" => "Status Updated!"]);
            
        }else {
            return response()->json(["status" => 1, "message" => "Record not found!"]);
            
        }

    }

    public function other_shipments(Request $request)
    {
        $other_parcel_receiving_id = $request->input('performa_no');
        $parcel_receiving_shipments = OtherParcelReceiving::find($other_parcel_receiving_id)->shipments()->get();
        $shipments = array();
        if ($parcel_receiving_shipments->count() != 0) {
            foreach ($parcel_receiving_shipments as $parcel_receiving_shipment) {
                $shipment = Shipment::find($parcel_receiving_shipment->shipment_id);
                if(!in_array($shipment->shipper_status_id,[17,25])){

                    $shipments[] = $shipment->tracking_number;
                }
            }
            return ['status' => 1, 'success' => 'Other Parcel Receiving Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Parcel Receiving Shipments', 'shipments' => false];
        }
    }

    public function other_print(Request $request)
    {
        $other_parcel_receiving_id = $request->id;
        $other_parcel_receiving = OtherParcelReceiving::find($other_parcel_receiving_id);
        $other_parcel_receiving_shipments = $other_parcel_receiving->shipments;
        
        $other_receiving_total_cns = OtherParcelReceivingShipment::join('shipments as s','s.id','other_parcel_receiving_shipments.shipment_id')
        ->where('other_parcel_receiving_shipments.other_parcel_receiving_id',$other_parcel_receiving_id)
        ->whereNotIn('s.shipper_status_id',[17,25])->count();

        $html = '<!doctype html>
              <html lang="en">
                <head>
                  <meta charset="utf-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                  <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">

                  <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                  <title>Other Parcel Receiving Performa</title>

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
                  width: 12.5% !important;
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

                .border.twice {
                  border-width: 2px !important;
                }

                .border.twice-top {
                  border-top-width: 2px !important;
                }

                .border.twice-bottom {
                  border-bottom-width: 2px !important;
                }

                .border.twice-left {
                  border-left-width: 2px !important;
                }

                .border.twice-right {
                  border-right-width: 2px !important;
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

                .void {
                  top: 0;
                  bottom: 0;
                  right: 0;
                  left: 0;
                  height: 80px;
                  font-size: 5rem;
                  line-height: 3.5rem;
                  opacity: 0.25;
                }
                 div.page
                  {
                      page-break-after: always;
                      page-break-inside: avoid;
                  }
                  .piece_number{
                      font-size: 2.5rem;
                  }
              </style>
                </head>
                <body>
                  <div>';

        $html .= '<div class="container-fluid text-center p-3">
                          <div class="row justify-content-end mb-2">
                              <div class="col">
                                  <img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">
                              </div>
                              <div class="col">
                                  <h2>Other Parcel Receiving Performa</h2>
                              </div>
                          </div>
                          <div class="row justify-content-end mb-2">
                              <div class="col">
                                      <table class="table table-bordered border">
                                          <tbody>
                                          <tr><td class="color primary w-50">Retail Note</td><td class=" w-50">' . str_pad($other_parcel_receiving->id, 6, '0', STR_PAD_LEFT) . '</td></tr>
                                          <tr><td class="color primary w-50">Branch Name</td><td class=" w-50">' . $other_parcel_receiving->user->store->name . '</td></tr>
                                          <tr><td class="color primary w-50">Booking Code</td><td class=" w-50">' . str_pad($other_parcel_receiving->user->id, 6, '0', STR_PAD_LEFT) . '</td></tr>
                                          </tbody>
                                      </table>
                              </div>
                              <div class="col">
                                      <table class="table table-bordered border">
                                          <tbody>
                                          <tr><td class="color primary w-50">Staff</td><td class=" w-50">' . $other_parcel_receiving->user->name . '</td></tr>

                                          <tr><td class="color primary w-50">Code</td><td class=" w-50">' . $other_parcel_receiving->user->store->code . '</td></tr>
                                          <tr><td class="color primary w-50">Date</td><td class=" w-50">' . $other_parcel_receiving->created_at . '</td></tr>
                                          </tbody>
                                      </table>
                              </div>
                          </div>
                          <div class="mb-2">
                              <table class="table table-bordered border">
                                      <tbody>
                                          <tr>
                                              <td class="color primary"><b>Booked At</b></td>
                                              <td class="color primary"><b>Shipper Name</b></td>
                                              <td class="color primary"><b>CN. Number</b></td>
                                          </tr>';

        foreach ($other_parcel_receiving_shipments as $other_parcel_receiving_shipment) {
            $html .= '
                                          <tr>
                                          <td>' . $other_parcel_receiving_shipment->shipment->created_at . '</td>
                                          <td>' . $other_parcel_receiving_shipment->shipment->user->name . '</td>
                                              <td>' . $other_parcel_receiving_shipment->shipment->tracking_number . '</td>
                                          </tr>';
        }

        $html .= '
                                          <tr>
                                              <td><b>Total</b></td>
                                              <td><b>' . $other_receiving_total_cns . '</b></td>
                                          </tr>';

        $html .= '
                                      </tbody>
                                  </table>
                          </div>';

        $html .= '
                              <div class="col mt-5">
                                  <div class="row text-center">
                                      <div class="col-4 mt-3">
                                          <p>Staff Signature</p>
                                      </div>
                                      <div class="col mt-3">
                                          <p>Pickup Rider Sign and Code___________________________________________________</p>
                                      </div>
                                  </div>
                              </div>
                          </div>';
        $html .= '
              <script>
                window.onload = function() {
                  window.print();
                }
              </script>
              ';

        $html .= '
                </body>
              </html>
          ';
        return $html;
    }

    public function shipper_products(Request $request){
        $token = $request->bearerToken();
        
       
            return response()->json(["status" => 0, "message" => "Products Found!","products"=>$products]);
        
    }
}
