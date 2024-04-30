<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\BarcodeType;
use App\Http\Models\BarcodeGenerator;
use App\Http\Controllers\Admins\ActivityTrailController;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;


class BarcodeGeneratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {   $this->middleware('auth:admin');
        $this->middleware('Permission');
    }
     public function index()
    {
        
        ActivityTrailController::createActivityTrailLog(Auth::id(), 980);
        $types = BarcodeType::get();
        return view('admin.barcode_generator')->with(['types'=>$types]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 981);
            $data = BarcodeGenerator::leftJoin('barcode_types as bt', 'bt.id','barcode_generators.barcode_type_id')->select(['barcode_generators.*','bt.barcode_name'])->orderby('barcode_generators.id', 'desc');

            if($barcode_type = $request->get('search_barcode_type')) {
                $data =  $data->where('barcode_generators.barcode_type_id', $barcode_type);
            }

            $datatable = Datatables::of($data)
            ->addColumn('barcode', function ($data) {

                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $html = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($data->barcode_key, $generator::TYPE_CODE_128, 2, 70)) . '" class="img-fluid mx-auto d-block h-auto">';
                return $html;
            });
            return $datatable->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 982);
        $from = $request->from;
        $to= $request->to;
        for($i=$request->from; $i<=$to; $i++) {

            $record = new BarcodeGenerator();

            $record->barcode_key = $request->prefix . $from;
            $record->barcode_type_id = $request->type;
            $record->save();
            $from++;

        }
        return redirect()->route('admin.barcode_generator.index')->with('success','Barcodes generated Successfully!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print_barcodes(Request $request)
    {
        $ids = $request->ids;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '<!doctype html>
            <html lang="en">
              <head>
                <meta charset="utsf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                <title>Air Waybill Sticker Barcode</title>
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
                  .barcode span {font-size: 12px;}
                  @media print {
                   html, body {min-width:auto!important; min-height:auto!important;}
                   @page {margin:0 !important; size: landscape;}
                   .pwrapper {margin: auto; page-break-inside: avoid;}
                   .logo span {font-size: 8px;}
                   .barcode span {font-size: 12px;}
                  }
                </style>
              </head>
              <body>
        ';

        $barcodes = '';

        foreach ($ids as $id) {
            $record = BarcodeGenerator::find($id);

            $barcodes .= '
                <div class="text-center pwrapper p-1">
                    <div class="logo">
                        <img src="' . asset('img/trax_logo_new.png') . '" width="75" class="d-block mx-auto">
                    </div>
                    <div class="barcode">
                        <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($record->barcode_key, $generator::TYPE_CODE_128, 2, 70)) . '" class="img-fluid mx-auto d-block h-auto">
                        <span class="d-block"><strong>* ' . $record->barcode_key . ' *</strong></span>
                    </div>
                </div>
            ';
        }

        $html .= $barcodes;

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
