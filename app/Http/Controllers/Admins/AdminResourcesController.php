<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Zone;
use App\Http\Models\ZoneClassCity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\City;
use Illuminate\Support\Facades\DB;
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
        // $zones = Zone::where('status', 1)->pluck('id','id')->toArray();
        $ZoneClassCity = ZoneClassCity::with('city','zone_classification')->get()->pluck(null, 'id')->toArray();
        if(true){
            $city_list_array = array();
            $city_list_array['header'] = ['S. No.','Origin','ID', 'Destination', 'Class', 'Zone','Zone Classification'];
            $serial = 1;
            $hubs = City::where('id',auth()->user()->default_hub_id)->get();
            foreach ($hubs as $key=>$hub_city) {
                $zone = $hub_city->zone;
                $zoneId = $zone->id;
                $zone_cities = array_filter($ZoneClassCity, function ($item) use ($zoneId) {
                    return $item['zone_id'] == $zoneId;
                });
                foreach ($zone_cities as $city2) {
                    $city = (object) $city2['city'];
                    $classification = (object) $city2['zone_classification'];
                    if (isset($city->id)) {
                        if ($city->status == 1) {
                            // $class = ZoneClassCity::where('zone_id', $zone->id)->where('city_id', $city->id)->first();
                            $cityId = $city->id;
                            $class = (object)collect(array_filter($ZoneClassCity, function ($item) use ($zoneId, $cityId) {
                                return $item['zone_id'] == $zoneId && $item['city_id'] == $cityId;
                            }))->first();
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
                                $city_list_array[] = ['serial' => $serial, 'origin' => $hub_city->name, 'id' => $city->id, 'name' => $city->name, 'class' => $class_name, 'Zone' => $zone->name,'zone_classification'=>$classification->name];
                                $serial++;
                            }
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
//            return url('/').'/'.$file_name_without_path;
        }

    }
}
