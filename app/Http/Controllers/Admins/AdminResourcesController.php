<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Zone;
use App\Http\Models\ZoneClassCity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\City;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminResourcesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }



    public function index(){
        return view('admin.documents.index');
    }
    public function get_network_list(Request $request){
        $zones = Zone::where('status', 1)->get();
        if($zones){
            $cell_st =[
                'font' =>['bold' => true],
                'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $spreadsheet = new Spreadsheet();
            foreach ($zones as $index => $zone){
                $city_list_array = array();
                $city_list_array['header'] = ['S. No.','ID', 'Name', 'Class'];
                $serial = 1;
                foreach ($zone->zone_cities as $city){
                    $class = ZoneClassCity::where('zone_id', $zone->id)->where('city_id', $city->id)->first();
                    $class = $class->class;
                    if($class == 0){
                        $class_name = "A";
                    }
                    elseif($class == 1){
                        $class_name = "B";
                    }
                    elseif($class == 2){
                        $class_name = "C";
                    }
                    else{
                        $class_name = "D";
                    }
                    $city_list_array[] = ['serial'=> $serial ,'id' => $city->id, 'name' => $city->name, 'class' => $class_name];
                    $serial++;
                }
                $spreadsheet->setActiveSheetIndex($index);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultColumnDimension()->setWidth(20);
                $sheet->fromArray($city_list_array,NULL,'A2',true);
                $sheet->getStyle("A2:D2")->applyFromArray($cell_st);
                $sheet->setTitle($zone->name);
                $spreadsheet->createSheet();
            }
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
//            return url('/').'/'.$file_name_without_path;
        }

    }
}
