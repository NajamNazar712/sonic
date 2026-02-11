<?php

namespace App\Http\Controllers\Admins\LocalFleet;

use App\Http\Controllers\Controller;
use App\Http\Models\City;
use App\Models\LocalFleetVehicle;
use App\Models\LocalFleetVehicleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use DNS2D;
use Yajra\DataTables\DataTables;

class AdminLocalFleetVehicleController extends Controller
{

    public function __construct()
    {   $this->middleware('auth:admin');
        $this->middleware('Permission');
    }
    public function index()
    {
        $cities = City::select('id','name')->get();
        return view('admin.local_fleet.vehicle_index', compact('cities'));
    }



    // ----------------------------
    // DATATABLE LIST
    // ----------------------------
    public function list(Request $request)
    {
        $data = LocalFleetVehicle::join('cities as c','c.id','=','local_fleet_vehicles.city_id')
            ->select(
                'local_fleet_vehicles.id',
                'local_fleet_vehicles.vehicle_number',
                'local_fleet_vehicles.make',
                'local_fleet_vehicles.city_id',
                'c.name as city_name',
                'local_fleet_vehicles.vendor_name',
                'local_fleet_vehicles.vendor_type',
                'local_fleet_vehicles.driver_name',
                'local_fleet_vehicles.capacity',
                'local_fleet_vehicles.mileage_per_liter',
                'local_fleet_vehicles.vehicle_type',
                'local_fleet_vehicles.rent_type',
                'local_fleet_vehicles.rent_amount',
                'local_fleet_vehicles.fueling_responsibility',
                'local_fleet_vehicles.status',
                'local_fleet_vehicles.created_at',
            );

        if(session('role_id') != 1) {
            $data->whereIn('c.hub_id',session('hubs'));
        }

        return DataTables::of($data)
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A');
            })
            ->editColumn('vehicle_type', function($v){
                return $v->vehicle_type == 1 ? 'Permanent' : 'Temporary';
            })
            ->editColumn('vendor_type', function($v){
                return $v->vendor_type == 1 ? 'Vendor' : 'Self';
            })
            ->editColumn('rent_type', function($v){
                return $v->rent_type == 1 ? 'Daily' : 'Monthly';
            })
            ->editColumn('fueling_responsibility', function($v){
                return $v->fueling_responsibility == 1 ? 'SlgTrax' : 'Vendor';
            })
            ->editColumn('status', function($v){
                return $v->status == 1 ? 'Active' : 'Inactive';
            })
            ->addColumn('action', function ($roles) {
                if (session('role_id') == 1 || in_array(663, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item upload-document"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-upload"></i></div><div class="col-9 offset-1">Upload Document</div></button>
                    <button type="button" class="dropdown-item view-documents"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-eye"></i></div><div class="col-9 offset-1">View Documents</div></button>
                    <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                    <button type="button" class="dropdown-item generate-barcode"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-bookmark"></i></div><div class="col-9 offset-1">Generate Barcode</div></button>
                    ';
//                    // $dropdown .=' <button type="button" class="dropdown-item delete"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Delete</div></button>';
//                    if ($roles->status == 1) {
//
//                        $dropdown .= ' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-minus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
//                    } else {
//
//                        $dropdown .= ' <button type="button" class="dropdown-item enable_disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
//                    }

                    $dropdown .= '</div>  </div>';

                    return $dropdown;
                } else {
                    return '';
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    // ----------------------------
    // RETURN EDIT DATA AS JSON
    // ----------------------------
    public function edit($id)
    {
        $vehicle = LocalFleetVehicle::find($id);

        return response()->json($vehicle);
    }


    // ----------------------------
    // STORE VEHICLE (SAVE METHOD)
    // ----------------------------
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_number'      => 'required|unique:local_fleet_vehicles|max:20',
            'city_id'             => 'required|numeric',
            'vendor_type' => 'required|in:1,2',
            'vehicle_type' => 'required|in:1,2',
            'rent_type' => 'required|in:1,2',
            'fueling_responsibility' => 'required|in:1,2',
            'mileage_per_liter' => 'required',
            'driver_name' => 'required|max:20',
            'vendor_name' => 'required|max:20',
            'rent_amount' => 'required|numeric',
            'capacity'=> 'nullable|integer|max:25',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $vehicle = new LocalFleetVehicle();
        $vehicle->vehicle_number        = $request->vehicle_number;
        $vehicle->make                  = $request->make;
        $vehicle->city_id               = $request->city_id;
        $vehicle->vendor_name           = $request->vendor_name;
        $vehicle->vendor_type           = $request->vendor_type;
        $vehicle->driver_name           = $request->driver_name;
        $vehicle->capacity              = $request->capacity;
        $vehicle->mileage_per_liter     = $request->mileage_per_liter;
        $vehicle->vehicle_type          = $request->vehicle_type;
        $vehicle->rent_type             = $request->rent_type;
        $vehicle->rent_amount           = $request->rent_amount;
        $vehicle->fueling_responsibility = $request->fueling_responsibility;
        $vehicle->status                = 1;
        $vehicle->created_by            = Auth::id();
        $vehicle->save();

        return back()->with('success','Vehicle added successfully');
    }


    // ----------------------------
    // UPDATE VEHICLE (SAVE METHOD)
    // ----------------------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required|integer',
            'vendor_type' => 'required|in:1,2',
            'vehicle_type' => 'required|in:1,2',
            'rent_type' => 'required|in:1,2',
            'fueling_responsibility' => 'required|in:1,2',
            'mileage_per_liter' => 'required',
            'driver_name' => 'required',
            'vendor_name' => 'required',
            'rent_amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $vehicle = LocalFleetVehicle::find($request->id);
        $vehicle->make                  = $request->make;
        $vehicle->vendor_name           = $request->vendor_name;
        $vehicle->vendor_type           = $request->vendor_type;
        $vehicle->driver_name           = $request->driver_name;
        $vehicle->capacity              = $request->capacity;
        $vehicle->mileage_per_liter     = $request->mileage_per_liter;
        $vehicle->vehicle_type          = $request->vehicle_type;
        $vehicle->rent_type             = $request->rent_type;
        $vehicle->rent_amount           = $request->rent_amount;
        $vehicle->fueling_responsibility = $request->fueling_responsibility;
        $vehicle->updated_by            = session('id');
        $vehicle->save();

        return back()->with('success','Vehicle updated successfully');
    }

//    public function qr_code_print(Request $request)
//    {
//        $vehicle_ids = [$request->vehicle_id];
//
//        $html = '<!doctype html>
//    <html lang="en">
//      <head>
//        <meta charset="utf-8">
//        <title>Vehicle QR Code</title>
//
//        <style>
//          * {
//            -webkit-print-color-adjust: exact !important;
//            color-adjust: exact !important;
//            box-sizing: border-box;
//          }
//
//          html, body {
//            width: 100%;
//            height: 100%;
//            margin: 0;
//            padding: 0;
//          }
//
//          @page {
//            margin: 0;
//          }
//
//          /* ABSOLUTE CENTER FIX */
//          .print-center {
//            position: fixed;
//            top: 50%;
//            left: 50%;
//            transform: translate(-50%, -50%);
//          }
//
//          .pwrapper {
//            text-align: center;
//          }
//        </style>
//      </head>
//      <body>
//    ';
//
//        $stickers = '';
//
//        foreach ($vehicle_ids as $id) {
//
//            $vehicle = LocalFleetVehicle::find(1); // replace with $id later
//            if (!$vehicle) continue;
//
//            $stickers .= '
//        <div class="print-center">
//            <div class="pwrapper">
//                <div class="qr">
//                    <img src="data:image/png;base64,' .
//                DNS2D::getBarcodePNG(
//                    (string) $vehicle->id,
//                    "QRCODE",
//                    12, 12
//                ) .
//                '" class="d-block mx-auto">
//
//                </div>
//
//                <div style="margin-top:10px;">
//                    <img src="' . asset('img/slg_logo.png') . '" width="300">
//                </div>
//            </div>
//        </div>
//        ';
//        }
//
//        $html .= $stickers;
//
//        $html .= '
//        <script>
//          window.onload = function () {
//            window.print();
//          }
//        </script>
//      </body>
//    </html>';
//
//        return $html;
//    }

    public function qr_code_print(Request $request)
    {
        $vehicle_ids = [$request->vehicle_id]; // future bulk ready

        $html = '<!doctype html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
            <title>Vehicle QR Code</title>
            <style type="text/css">
              * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
              }
              body {
                background: none !important;
                color: #000 !important;
              }
              .pwrapper {margin: auto; page-break-inside: avoid;}
              .logo {margin-bottom:5px;}
              .logo img {margin-bottom:2.5px; filter: brightness(0);}
              .logo span {font-size: 8px;}
              .qr span {font-size: 12px;}

              @media print {
               html, body {min-width:auto!important; min-height:auto!important;}
               @page {margin:0 !important; size: landscape;}
               .pwrapper {margin: auto; page-break-inside: avoid;}
               .logo span {font-size: 8px;}
               .qr span {font-size: 12px;}
              }
            </style>
          </head>
          <body>
    ';

        $stickers = '';

        foreach ($vehicle_ids as $id) {

            $vehicle = LocalFleetVehicle::find($id);
            if (!$vehicle) continue;

            $stickers .= '
            <div class="text-center pwrapper p-1">
                <div class="qr" style="margin: 50px">
                    <img src="data:image/png;base64,' .
                     DNS2D::getBarcodePNG( (string) $vehicle->id, "QRCODE", 12, 12 ) .
                '" class="d-block mx-auto">
                    <span class="d-block" style="margin-top: 10px">  <img src="' . asset('img/slg_logo.png') . '" width="300" class="d-block mx-auto"></span>
                </div>
            </div>
        ';
        }

        $html .= $stickers;

        $html .= '
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

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|integer',
            'document_name' => 'required|string',
            'document_file' => 'required|file'
        ]);

        $path = null;
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $timestamp  = now()->format('YmdHis');
            $filename = 'vehicle_' . $request->vehicle_id . '_' . $timestamp . '.png';
            $directory = 'local_fleet/vehicle_documents';
            Storage::disk('public')->putFileAs($directory,$file,$filename);
            $path = $directory . '/' . $filename;
        }
        $vehicle_document = new LocalFleetVehicleDocument();
        $vehicle_document->vehicle_id =  $request->vehicle_id;
        $vehicle_document->document_name =  $request->document_name;
        $vehicle_document->document_path = $path;
        $vehicle_document->save();

        return response()->json(['status'=>0,'message' => 'Document uploaded successfully']);
    }

    public function listDocuments($vehicle_id)
    {
        $docs = LocalFleetVehicleDocument::where('vehicle_id', $vehicle_id)
            ->orderBy('id','desc')
            ->get()
            ->map(function ($d) {
                return [
                    'document_name' => $d->document_name,
                    'document_path' => asset('storage/'.$d->document_path),
                    'date' => $d->created_at->format('d M Y, h:i A'),
                ];
            });

        return response()->json([
            'status' => 0,
            'data' => $docs
        ]);
    }

}
