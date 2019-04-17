<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Http\Models\City;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ShipperResourcesController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

    }

    public function index(){
        return view('client.documents.index');
    }
    public function get_network_list(Request $request){
        $response = self::get_city_list();
        return $response;
    }

    static public function get_city_list(){
        $city_list = City::where('status', 1)->select('id','name')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($city_list,NULL,'A1',true);
        $sheet->setTitle('Network List');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name_without_path = "file/documents/Network List.xlsx";
        $file_name = public_path() .'/'.$file_name_without_path ;
        $writer->save($file_name);
        return url('/').'/'.$file_name_without_path;

    }
}
