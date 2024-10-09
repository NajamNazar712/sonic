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
    // public function get_network_list(Request $request){
    //     $zones = Zone::where('status', 1)->get();
    //     if($zones){
    //         $city_list_array = array();
    //         $city_list_array['header'] = ['S. No.','ID', 'Name', 'Class', 'Zone'];
    //         $serial = 1;
    //         foreach ($zones as $index => $zone){
    //             foreach ($zone->zone_cities as $city) {
    //                 $city_check = City::where('id', $city->id)->first();
    //                 if ($city_check->status == 1) {
    //                     $class = ZoneClassCity::where('zone_id', $zone->id)->where('city_id', $city->id)->first();
    //                     if ($class) {
    //                         $class = $class->class;
    //                         if ($class == 0) {
    //                             $class_name = "A";
    //                         } elseif ($class == 1) {
    //                             $class_name = "B";
    //                         } elseif ($class == 2) {
    //                             $class_name = "C";
    //                         } else {
    //                             $class_name = "D";
    //                         }
    //                         $city_list_array[] = ['serial' => $serial, 'id' => $city->id, 'name' => $city->name, 'class' => $class_name, 'Zone' => $zone->name];
    //                         $serial++;
    //                     }
    //                 }
    //             }
    //         }
    //         $cell_st =[
    //             'font' =>['bold' => true],
    //             'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    //             'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
    //         ];
    //         $spreadsheet = new Spreadsheet();
    //         $sheet = $spreadsheet->getActiveSheet();
    //         $sheet->getDefaultColumnDimension()->setWidth(20);
    //         $sheet->fromArray($city_list_array,NULL,'A2',true);
    //         $sheet->getStyle("A2:E2")->applyFromArray($cell_st);
    //         $sheet->setTitle('Network List');
    //         $spreadsheet->createSheet();
    //         $spreadsheet->setActiveSheetIndex(0);
    //         $writer = new Xlsx($spreadsheet);
    //         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //         header('Content-Disposition: attachment;filename="Network List.xlsx"');
    //         header('Cache-Control: max-age=0');
    //         $file_name_without_path = "file/documents/Network List.xlsx";
    //         $file_name = public_path() .'/'.$file_name_without_path ;
    //         $writer->save('php://output');
    //     }
    // }

    public function get_network_list(Request $request){
        $zones = Zone::where('status', 1)->get();
        if($zones){
            $city_list_array = array();
            // $city_list_array['header'] = ['S. No.','ID', 'Name', 'Class', 'Zone'];
            // $serial = 1;

            $city_list_array['header'] = [
                'S. No.',
                // 'Name',
                'Origin',
                'Destination',
                'ID',
                'Class',
                'Zone'
            ];
            $serial = 1;

            $user_origin_city = DB::table('users')
            ->leftJoin('cities as origin_city', 'users.city_id', 'origin_city.id')
            ->where('origin_city.status', 1)
            ->where('users.id', Auth::user()->id)
            ->select([
                'origin_city.id as origin_id',
                'origin_city.name as origin_name',
            ])
            ->first();

            $user_destination_city = City::where('hub', '!=', 1)
            ->where('hub_id', $user_origin_city->origin_id)
            ->select([
                'id',
                'name'
            ])
            ->get();

            foreach ($zones as $zone) {
                foreach ($zone->zone_cities as $city) {
                    $city_check = City::where('id', $city->id)->first();
                    if ($city_check && $city_check->status == 1) {
                        $class = ZoneClassCity::where('zone_id', $zone->id)->where('city_id', $city->id)->first();
                        if ($class) {
                            if ($user_origin_city->origin_id != $city->id) {
                                $class_name = "Local";
                            } else {
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
                            }

                            // Only loop through the destination cities once for each zone city
                            foreach ($user_destination_city as $destination) {
                                // Avoid duplicating the same origin-destination pair
                                $existing_entry = collect($city_list_array ?? [])->firstWhere('destination', $destination->name);

                                if (!$existing_entry) {
                                    $city_list_array[] = [
                                        'serial' => $serial,
                                        'origin' => $user_origin_city->origin_name,
                                        'destination' => $destination->name,
                                        'id' => $destination->id,
                                        'class' => $class_name,
                                        'Zone' => $zone->name
                                    ];
                                    $serial++;
                                }
                            }
                        }
                    }
                }
            }

            // foreach ($zones as $index => $zone){
            //     foreach ($zone->zone_cities as $city) {
            //         $city_check = City::where('id', $city->id)->first();
            //         if ($city_check->status == 1) {
            //             $class = ZoneClassCity::where('zone_id', $zone->id)->where('city_id', $city->id)->first();
            //             if ($class) {
            //                 $class = $class->class;
            //                 if ($class == 0) {
            //                     $class_name = "A";
            //                 } elseif ($class == 1) {
            //                     $class_name = "B";
            //                 } elseif ($class == 2) {
            //                     $class_name = "C";
            //                 } else {
            //                     $class_name = "D";
            //                 }
            //                 // $city_list_array[] = ['serial' => $serial, 'id' => $city->id, 'name' => $city->name, 'class' => $class_name, 'Zone' => $zone->name];
            //                 // $serial++;
            //                 foreach($user_destination_city as $destination){
            //                     $city_list_array[] = [
            //                         'serial' => $serial,
            //                         // 'name' => $city->name,
            //                         'origin' => $user_origin_city->origin_name,
            //                         'destination' => $destination->name,
            //                         'id' => $destination->id,
            //                         'class' => $class_name,
            //                         'Zone' => $zone->name
            //                     ];
            //                     $serial++;
            //                 }
            //             }
            //         }
            //     }
            // }

            $cell_st =[
                'font' =>['bold' => true],
                'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
            ];
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultColumnDimension()->setWidth(20);
            $sheet->fromArray($city_list_array,NULL,'A2',true);
            $sheet->getStyle("A2:F2")->applyFromArray($cell_st);
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
