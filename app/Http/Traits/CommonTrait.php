<?php

namespace App\Http\Traits;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\HBLKonnect\HblKonnectTransactionDeliveryNote;
use App\Http\Models\City;
use App\Http\Models\CityArea;
use App\Http\Models\CRM\CrmRequest;
use Carbon\Carbon;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeLeave;
use App\Http\Models\ReportingLocation;
use App\Http\Models\Rider;
use App\Http\Models\Rider\RiderReturnDelivery;
use App\Http\Models\RiderDelivery;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentInformationLog;
use App\Http\Models\ShipmentsJourney;
use App\Models\InternationalZonalMarginColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\ShipmentOtpVerification;

trait CommonTrait
{
    public function getAvailedLeaves($employee_id)
    {
        $employee = Employee::find($employee_id);
        if($employee){
            $weekend_days = [];
            $working_days = 1;
            if (isset($employee->department) && $employee->department->working_days == 1) {
                $working_days = 1;
                // Sunday is off
                $weekend_days = [Carbon::SUNDAY];
            } else if (isset($employee->department) && $employee->department->working_days == 2){
                $working_days = 2;
                // Saturday and Sunday are off
                $weekend_days = [Carbon::SATURDAY, Carbon::SUNDAY];
            }
            $leaves_availed = EmployeeLeave::where('employee_id', $employee->id)
            ->whereIn('status', [2,4,6])
            ->whereIn('leave_type', [1,2,3,4])
            ->get()
            ->filter(function ($leave) {
                return $leave->from <= $leave->to;
            })
            ->sum(function ($leave) use ($weekend_days, $working_days) {
                $leave_days = Carbon::parse($leave->from)->diffInDaysFiltered(function (Carbon $date) use ($weekend_days) {
                    return !in_array($date->dayOfWeek, $weekend_days);
                }, $leave->to);
                if ($working_days == 2) {
                    $leave_days -= Carbon::parse($leave->from)->isWeekend() ? 1 : 0;
                }
                return $leave_days + 1;
            });
            return $leaves_availed;
        }
        return 0;
    }

    public function calculateToDateLeaves($employee, $toDate)
    {
        try {
            $start = Carbon::now()->startOfMonth();
            $to_date = Carbon::parse($toDate)->startOfMonth();
            $to_date_month = Carbon::parse($toDate)->month;
            $difference = $to_date->diffInMonths($start);
            // If Leaves apply for 2 or more than 2 days
            if($difference >= 2) {
                // If month is june, add 6 as per last months of fiscal year
                if($to_date_month == 6){
    
                    $nd = $difference - 2;
                    $result = $nd * 2;
                    $result = $result+6;
                } 
                // If month is may, add 3 as per second last month of fiscal year
                else if($to_date_month == 5){
    
                    $nd = $difference - 1;
                    $result = $nd * 2;
                    $result = $result+3;
                } else {
                    $result = $difference * 2;
                }
            }
            else if ($difference == 1){
                // If month is may or june, add 3 as per last month of fiscal year
                if($to_date_month == 5 || $to_date_month == 6){
                    $result = 3;
                }  else {
                    $result = 2;
                }
            } else {
                $result = $employee->leave_count;
            }
            return ['status' => 1, 'data' => $result];
        } catch (\Throwable $th) {
            return ['status' => 0, 'msg' => $th->getMessage()];
        }

    }


    private function distance($origin, $destination)
    {
        return $this->vincenty_distance($origin, $destination);
    }


    private function vincenty_distance($origin, $destination)
    {
        $earth_radius = 6371;

        list($origin_latitude, $origin_longitude) = explode(',', $origin);
        list($destination_latitude, $destination_longitude) = explode(',', $destination);

        $origin_latitude = deg2rad($origin_latitude);
        $origin_longitude = deg2rad($origin_longitude);
        $destination_latitude = deg2rad($destination_latitude);
        $destination_longitude = deg2rad($destination_longitude);

        $longitude_delta = $destination_longitude - $origin_longitude;

        $distance = round($earth_radius * (atan2(sqrt(pow(cos($destination_latitude) * sin($longitude_delta), 2) + pow(cos($origin_latitude) * sin($destination_latitude) - sin($origin_latitude) * cos($destination_latitude) * cos($longitude_delta), 2)), (sin($origin_latitude) * sin($destination_latitude) + cos($origin_latitude) * cos($destination_latitude) * cos($longitude_delta)))), 2);

        return $distance;
    }

    private function calculate_location_status($latitude, $longitude)
    {
        $location_status = 1;
        $reporting_locations = ReportingLocation::where('status', 1);
        if ($reporting_locations->exists()) {
            $reporting_locations = $reporting_locations->get();
            foreach ($reporting_locations as $reporting_location) {
                $reporting_location->radius;
                $destination = $reporting_location->lat . ',' . $reporting_location->long;
                $origin = $latitude . ',' . $longitude;
                $distance = $this->distance($origin, $destination);
                if ($distance <= $reporting_location->radius / 1000) {
                    $location_status = 2;
                    return $location_status;
                }
            }
        } else {
            $location_status = 0;
        }
        return $location_status;
    }

    function setJourneyDetails($scanning_data)
    {
        if (isset($scanning_data)) {
            return [
                'latitude' => $scanning_data['latitude'] ?? '-',
                'longitude' => $scanning_data['longitude'] ?? '-',
                'location_status' => ($scanning_data['location_status'] == 1) ? 'On-Site' : 'Off-site',
                'area' => CityArea::find($scanning_data['area_id'])->name ?? '-',
                'city' => City::where(['id' => $scanning_data['hub_id'], 'hub' => "1"])->first()->name ?? '-',

            ];
        } else {
            return [
                'latitude' => '-',
                'longitude' => '-',
                'area' => '-',
                'city' => '-',
                'location_status' => '-',
            ];
        }
    }

    function receiveDeliveryPrint($delivery_note_id){
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">

                    <title>Delivery Note</title>

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

                      /*table.table-bordered {
                        page-break-inside: avoid;
                      }*/

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

                      td.try_and_buy span {
                        width: 22px;
                      }

                      td.try_and_buy span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
                      }
                      
                      td.complaint {
                            background: #09262e !important;
                            color: #ffffff;
                       }
                      td.details_changed {
                            background: #000000 !important;
                            color: #ffffff;
                       }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $delivery_note_id);
        if ($delivery_note->exists()) {
            $total_weight = 0;
            $total_shipments = 0;
            $total_cod_amount = 0;
            $shipments = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->select('shipment_id')->orderBy('ordering', 'asc', 'shipment_id', 'asc')->get();

            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Item Qty</strong></td>
                            <td class="color primary"><strong>Weight</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                            <td class="color primary"><strong>Special Instructions</strong></td>
                            <td class="color primary"><strong>Open Shipment</strong></td>
                            <td class="color primary"><strong>Remarks</strong></td>
                            <td class="color primary" style="width:200px;"><strong>Receiver\'s Name</strong></td>
                            <td class="color primary" style="width:200px;"><strong>Sign</strong></td>
                          </tr>
        ';


            foreach ($shipments as $parcel) {
                $total_shipments++;
                $shipment = Shipment::find($parcel->shipment_id);
                $total_weight += (float) $shipment->actual_weight;
                $class = null;
                $details_change_class = null;
                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                    $class = 'complaint';
                } elseif (ShipmentInformationLog::where('shipment_id', $shipment->id)->exists()) {
                    $details_change_class = 'details_changed';
                }
                $check_walk_in = GlobalSettings::where('type', 'Walk-In')->first();
                if ($check_walk_in['setting_value'] == $shipment->user->id) {
                    $user_details = 'Walk-In (' . $shipment->pickup_address->poc . ') | ' . $shipment->pickup_address->phone;
                } else {
                    $user_details = $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '');
                }
                $ccd_icon = '';
                if ($shipment->payment_mode_id == 2) {
                    $tracking_number = '<b>' . $shipment->tracking_number . ' </b><br/><span><i class="la la-credit-card"></i>(Credit Card on Delivery-CCD)</span>';
                } else {
                    $tracking_number = $shipment->tracking_number;
                }
                $consignee_address = '';
                if ($shipment->consignee_address != null) {
                    $consignee_address = $shipment->consignee_address;
                }

                $shipment_details_row_start = '
                          <tr>
                            <td class="' . $class . '">' . $total_shipments . '</td>
                            <td class="' . $class . '">' . $tracking_number . '</td>
                            <td class="' . $class . '">' . $user_details . '</td>
                            <td class="' . $class . ' ' . $details_change_class . '">' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                             <td class="' . $class . '">' . $consignee_address . '</td>
                           
                ';

                if ($shipment->booking_type_id == 1) {
                    $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment->booking_type->booking_type . '</td>
                ';
                } else if ($shipment->booking_type_id == 2) {
                    $shipment_details_row_start .= '
                    <td class="replacement ' . $class . '"><span class="align-middle">' . $shipment->booking_type->booking_type . '</span><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                ';
                } else if ($shipment->booking_type_id == 3) {
                    $shipment_details_row_start .= '
                    <td class="try_and_buy ' . $class . '"><span class="align-middle">' . $shipment->booking_type->booking_type . '</span><span class="d-inline-block align-middle float-right"><img src="' . asset('img/try_and_buy.png') . '"></span></td>
                ';
                } else {
                    $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment->booking_type->booking_type . '</td>
                ';
                }

                $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment->items->sum('quantity') . '</td>';
                $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment->actual_weight . '</td>';

                if ($shipment->booking_type_id != 4 || ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 2)) {
                    $shipment_details_row_start .= '
                            <td class="' . $class . '">Rs ' . number_format($shipment->amount) . '</td>
                    ';

                    $total_cod_amount += $shipment->amount;
                } else {
                    $shipment_details_row_start .= '
                            <td class="' . $class . '">Rs 0</td>
                    ';
                }
                if ($shipment->special_instructions != null) {
                    $shipment_details_row_start .= '<td class="' . $class . ' ' . $details_change_class . '">' . $shipment->special_instructions . '</td>';
                } else {
                    $shipment_details_row_start .= '<td class="' . $class . ' ' . $details_change_class . '">-</td>';
                }
                if ($shipment->shipment_detail()->exists()) {
                    if ($shipment->shipment_detail->is_open == 1) {
                        $shipment_details_row_start .= '
                    <td class="' . $class . '"><strong> Yes <span><img src="' . asset('img/open_box_icon.png') . '" ></span></strong></td>';
                    } else {
                        $shipment_details_row_start .= '
                    <td class="' . $class . '"><strong> No <span></span></strong></td>';
                    }
                } else {
                    $shipment_details_row_start .= '
                    <td class="' . $class . '"><strong> No <span></span></strong></td>';
                }
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', '!=', 5)->where('remarks', '!=', null)->select('remarks');

                if ($shipment_journey->exists()) {
                    $shipment_journey = $shipment_journey->latest()->first();

                    $shipment_details_row_start .= '
                            <td class="' . $class . '">' . $shipment_journey->remarks . '</td>
                    ';
                } else {
                    $shipment_details_row_start .= '
                            <td class="' . $class . '"></td>
                    ';
                }


                $shipment_details_row_start .= '
                            <td class="' . $class . '"></td>
                            <td class="' . $class . '"></td>
                          </tr>
                ';

                $shipment_details .= $shipment_details_row_start;

                if ($shipment->booking_type_id == 3) {
                    $total_Shipment_items = 0;
                    foreach ($shipment->items as $shipment_item) {
                        $total_Shipment_items++;
                        $shipment_details_row_start = '
                          <tr>
                            <td class="' . $class . '">' . $total_shipments . '.' . $total_Shipment_items . '</td>
                            <td class="' . $class . '">' . $shipment_item->id . ' (' . $shipment->tracking_number . ')</td>
                            <td class="' . $class . '"><b>Product Type:</b></td>
                            <td class="' . $class . '">' . $shipment_item->product->product_name . '</td>
                            <td class="' . $class . '">' . $shipment_item->description . '</td>
                ';
                        $shipment_details_row_start .= '
                    <td class="try_and_buy ' . $class . '"><span class="align-middle">' . $shipment->booking_type->booking_type . '</span><span class="d-inline-block align-middle float-right"><img src="' . asset('img/try_and_buy.png') . '"></span></td>
                ';

                        $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment_item->quantity . '</td>';

                        $shipment_details_row_start .= '
                            <td class="' . $class . '">Rs ' . number_format($shipment_item->price) . '</td>
                    ';
                        $shipment_details_row_start .= '<td class="' . $class . '">-</td>';

                        $shipment_details_row_start .= '
                        <td class="' . $class . '"></td>
                    ';


                        $shipment_details_row_start .= '
                            <td class="' . $class . '"></td>
                            <td class="' . $class . '"></td>
                          </tr>
                ';

                        $shipment_details .= $shipment_details_row_start;
                    }
                }
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $delivery_note_id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $delivery_note = $delivery_note->first();
            $rider_id = NULL;
            if ($delivery_note->special_rider) {
                $rider_name = $rider->name . ' ( ' . $delivery_note->special_rider_name . ' )';
            } else {
                $rider_name = $rider->name;
                $rider_id = $rider->trax_id;
            }
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route ? $delivery_note_details->route->code . '( ' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ' )' : 'Hold In Route';

            //HBL Konnect Integration
            $hbl_transactions_amount = 0;
            $hbl_transactions_delivery_note = HblKonnectTransactionDeliveryNote::where('delivery_note_id', $delivery_note_id);
            if ($hbl_transactions_delivery_note->exists()) {
                $hbl_transactions_delivery_note = $hbl_transactions_delivery_note->first();
                $hbl_transactions_amount = $hbl_transactions_delivery_note->transactions_amount;
                $cash_amount = $hbl_transactions_delivery_note->cash_amount;
            } else {
                $cash_amount = $delivery_note->recived_cod_amount;
            }
            //HBL Konnect Integration
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Delivery Note</strong></td>
                            <td class="text-center align-middle color secondary">Created at ' . $delivery_note_details->created_at . '</br> by ' . ucfirst($delivery_note_details->admin->name) . '</td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td colspan="2" rowspan="9" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($delivery_note_id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($delivery_note_id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Trax ID</strong></td>
                            <td>' . $rider_id . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Collection Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>HBL Transactions Amount</strong></td>
                            <td>Rs ' . number_format($hbl_transactions_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Cash Amount</strong></td>
                            <td>Rs ' . number_format($cash_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Weight (Kg)</strong></td>
                            <td>' . $total_weight . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;
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
    public function riderInformation($riderId)
    {
      $rider = Rider::find($riderId);
      $information = array();
      $information['id'] = $rider->id;
      $information['name'] = $rider->name;
      $information['phone_number'] = $rider->phone;
      $information['city'] = $rider->city->name;
      $information['category'] = $rider->rider_category->name;
      if ($rider->route) {
        $information['route'] = $rider->route->code . ' (' . $rider->route->start . ' to ' . $rider->route->end . ')';
      } else {
        $information['route'] = '';
      }
      return $information;
    }

    function zoneMarginColumnName(){
      $zoneColumn = InternationalZonalMarginColumn::where('type', 1)->first();
      $zoneColumnsArray = explode(",", $zoneColumn->zone_column);
      $marginColumnsArray = explode(",", $zoneColumn->margin_column);
      return ['zoneColumnArray'=> $zoneColumnsArray,'marginColumn'=> $marginColumnsArray];
    }
  /**
   * Get image, audio, and location buttons for a given shipment journey.
   *
   * This method checks the shipment status and retrieves the corresponding
   * RiderDelivery or RiderReturnDelivery records. If image/audio files exist,
   * it generates temporary S3 URLs or public URLs for display buttons.
   * It also includes a Google Maps location button if coordinates are available.
   *
   * @param  object  $journey  The shipment journey instance containing status info.
   * @return string  HTML string with action buttons or '-' if no data found.
   */
  function getImageAudio($journey)
  {
    // Define which statuses belong to normal delivery or return delivery
    $deliveryStatuses = [7, 8, 9, 12, 15, 18, 14, 30, 37, 56];
    $returnStatuses = [47, 24, 48, 60, 25, 31, 38];

    $output = '-'; // Default output if no data found

    // === CASE 1: RiderDelivery ===
    if (in_array($journey->shipment_status_shipper->id, $deliveryStatuses)) {

      $riderDelivery = RiderDelivery::where('shipment_id', $journey->shipment_id)
        ->where('delivery_note_id', $journey->reference_1_id)
        ->where('rider_status_id', $journey->shipper_status_id)
        ->where('rider_status_reason_id', $journey->status_reason_id)
        ->first();

      if ($riderDelivery) {
        $output = '';

        if ($riderDelivery->picture_path) {
          if (Storage::disk('public')->exists($riderDelivery->picture_path)) {
            $imageUrl = asset(Storage::url($riderDelivery->picture_path));
          } else {
            try {
              $imageUrl = Storage::disk('s3')->temporaryUrl(
                $riderDelivery->picture_path,
                now()->addMinutes(5)
              );
            } catch (\Exception $e) {
              \Log::error('S3 Image URL error: ' . $e->getMessage());
              $imageUrl = null;
            }
          }

          if ($imageUrl) {
            $output .= '<button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . $imageUrl . '" target="_blank"><i class="la la-lg la-image"></i></button>';
          }
        }

        // ✅ Handle Audio
        if ($riderDelivery->audio_path) {
          if (Storage::disk('public')->exists($riderDelivery->audio_path)) {
            $audioUrl = asset(Storage::url($riderDelivery->audio_path));
          } else {
            try {
              $audioUrl = Storage::disk('s3')->temporaryUrl(
                $riderDelivery->audio_path,
                now()->addMinutes(5)
              );
            } catch (\Exception $e) {
              \Log::error('S3 Audio URL error: ' . $e->getMessage());
              $audioUrl = null;
            }
          }

          if ($audioUrl) {
            $output .= '| <button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . $audioUrl . '" target="_blank"><i class="la la-file-sound-o"></i></button>';
          }
        }

        // ✅ Handle Location
        if ($riderDelivery->actual_location_latitude && $riderDelivery->actual_location_longitude) {
          $locationUrl = 'https://www.google.com/maps/search/?api=1&query=' .
            $riderDelivery->actual_location_latitude . ',' .
            $riderDelivery->actual_location_longitude;
          $output .= '| <a type="button" class="btn btn-sm btn-outline-info align-middle location p-0" href="' . $locationUrl . '" target="_blank"><i class="la la-map-marker"></i></a>';
        }
      }
    }

    // === CASE 2: RiderReturnDelivery ===
    else if (in_array($journey->shipment_status_shipper->id, $returnStatuses)) {

      $riderReturn = RiderReturnDelivery::where('shipment_id', $journey->shipment_id)
        ->where('return_note_id', $journey->reference_1_id)
        ->where('rider_status_id', $journey->shipper_status_id)
        ->where('rider_status_reason_id', $journey->status_reason_id)
        ->first();

      if ($riderReturn) {
        $output = '';

        // ✅ Handle Image
        if ($riderReturn->picture_path) {
          if (Storage::disk('public')->exists($riderReturn->picture_path)) {
            $imageUrl = asset(Storage::url($riderReturn->picture_path));
          } else {
            try {
              $imageUrl = Storage::disk('s3')->temporaryUrl(
                $riderReturn->picture_path,
                now()->addMinutes(5)
              );
            } catch (\Exception $e) {
              \Log::error('S3 Return Image URL error: ' . $e->getMessage());
              $imageUrl = null;
            }
          }

          if ($imageUrl) {
            $output .= '<button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . $imageUrl . '" target="_blank"><i class="la la-lg la-image"></i></button>';
          }
        }

        // ✅ Handle Audio
        if ($riderReturn->audio_path) {
          if (Storage::disk('public')->exists($riderReturn->audio_path)) {
            $audioUrl = asset(Storage::url($riderReturn->audio_path));
          } else {
            try {
              $audioUrl = Storage::disk('s3')->temporaryUrl(
                $riderReturn->audio_path,
                now()->addMinutes(5)
              );
            } catch (\Exception $e) {
              \Log::error('S3 Return Audio URL error: ' . $e->getMessage());
              $audioUrl = null;
            }
          }

          if ($audioUrl) {
            $output .= '| <button type="button" class="btn btn-sm btn-outline-info align-middle picture p-0" data-link="' . $audioUrl . '" target="_blank"><i class="la la-file-sound-o"></i></button>';
          }
        }

        // ✅ Handle Location
        if ($riderReturn->actual_location_latitude && $riderReturn->actual_location_longitude) {
          $locationUrl = 'https://www.google.com/maps/search/?api=1&query=' .
            $riderReturn->actual_location_latitude . ',' .
            $riderReturn->actual_location_longitude;
          $output .= '| <a type="button" class="btn btn-sm btn-outline-info align-middle location p-0" href="' . $locationUrl . '" target="_blank"><i class="la la-map-marker"></i></a>';
        }
      }
    }

    // Return all generated buttons or '-' if nothing found
    return $output ?: '-';
  }

  function withOtpOrNot($journey)
  {
    // Extract the date-hour-minute from journey time
    $start = Carbon::parse($journey->created_at)->subMinute();
    $end   = Carbon::parse($journey->created_at)->addMinute();

    $otpRecord = ShipmentOtpVerification::where('shipment_id', $journey->shipment_id)
        ->where('via_rvrsub_reason', 1)
        ->whereBetween('created_at', [$start, $end])
        ->first();

    return $otpRecord ? 'With OTP' : 'Without OTP';
  }

}
