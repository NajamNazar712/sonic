<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\Zone;
use App\Http\Models\ZoneClassCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $zones = Zone::where('status', 1)->get();
        if($zones){
            $city_list_array = array();
            $city_list_array['header'] = [
                'S. No.',
                'ID', 
                'Name',
                'Origin',
                'Destination', 
                'Class', 
                'Zone'
            ];
            $serial = 1;

            $city_data = DB::table('shipments')
                ->join('user_shipping_infos', 'shipments.pickup_address_id', '=', 'user_shipping_infos.id')
                ->join('cities AS origin_city', 'user_shipping_infos.city_id', '=', 'origin_city.id')
                ->join('cities AS destination_city', 'shipments.consignee_city_id', '=', 'destination_city.id')
                ->where('shipments.user_id', Auth::user()->id)
                ->select([
                    'origin_city.name as origin_city',
                    'destination_city.name as destination_city'
                ])
            ->get();

            $origin_city = $city_data->pluck(['origin_city'])->toArray();
            $destination_city = $city_data->pluck(['destination_city'])->toArray();

            foreach ($zones as $index => $zone){
                // foreach ($zone->zone_cities as $city) {
                foreach ($zone->zone_cities as $city_index => $city) {
                    $city_check = City::where('id', $city->id)->first();
                    if ($city_check->status == 1) {
                        $class = ZoneClassCity::where('zone_id', $zone->id)->where('city_id', $city->id)->first();
                        if ($class) {
                            $class = $class->class;
                            if ($class == 0) {
                                $class_name = "A";
                            } elseif ($class == 1) {
                                $class_name = "B";
                            } elseif ($class == 2) {
                                $class_name = "C";
                            } else {
                                $class_name = "D";
                            }

                            $city_origin = $origin_city[$city_index] ?? '-';
                            $city_destination = isset($destination_city[$city_index]) ? $destination_city[$city_index] : '-';

                            $city_list_array[] = [
                                'S. No.' => $serial,
                                'ID' => $city->id,
                                'Name' => $city_check->name,
                                'Origin' => $city_origin,
                                'Destination' => $city_destination,
                                'Class' => $class_name,
                                'Zone' => $zone->name
                            ];
                            $serial++;
                        }
                    }
                }
            }
            $cell_st =[
                'font' =>['bold' => true],
                'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(20);
            $sheet->fromArray($city_list_array,NULL,'A2',true);
            $sheet->getStyle("A2:G2")->applyFromArray($cell_st);
            $sheet->setTitle('Network List');
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(0);
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Network List.xlsx"');
            header('Cache-Control: max-age=0');
            $file_name_without_path = "file/documents/Network List.xlsx";
            $file_name = public_path() .'/'.$file_name_without_path ;
            $writer->save('php://output');
        }
    }

    static public function get_city_list(){
        $city_list_array = array();
        $city_list = City::where('status', 1)->select('id','name')->get();
        if($city_list){
            $city_list_array['header'] = ['S. No.','ID', 'Name'];
            $serial = 1;
            foreach ($city_list as $list){
                $city_list_array[] = ['serial'=> $serial ,'id' => $list->id, 'name' => $list->name];
                $serial++;
            }
            $cell_st =[
                'font' =>['bold' => true],
                'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(20);

            $sheet->fromArray($city_list_array,NULL,'A2',true);
            $sheet->getStyle("A2:C2")->applyFromArray($cell_st);
            $sheet->setTitle('Network List');
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Network List.xlsx"');
            header('Cache-Control: max-age=0');
            $file_name_without_path = "file/documents/Network List.xlsx";
            $file_name = public_path() .'/'.$file_name_without_path ;
            return $writer->save('php://output');
            // return url('/').'/'.$file_name_without_path;
        }

    }
}
