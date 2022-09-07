<?php

namespace App\Jobs;

use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\EmployeePayslip;
use App\Http\Models\PayslipPdf;
use App\Http\Models\Rider;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class ProcessPaySlipPdfEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $payslip_details;
    protected $payslip_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $payslip_details, int $payslip_id)
    {
        $this->queue = 'payslip_pdf_email';
        $this->payslip_details = $payslip_details;
        $this->payslip_id = $payslip_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payslip = EmployeePayslip::find($this->payslip_id);
        $payroll_month = Carbon::parse($payslip->payroll_month)->format('F Y');
        $payroll_cut_off_date = Carbon::parse($payslip->payroll_cut_off_date)->toDateString();
        $trax_id = $payslip->trax_id;
        $personal_contact = '';
        if($payslip->employee_type == 2){
            $riders = Rider::where('trax_id', $trax_id)->first();
            if($riders){
                $personal_contact = $riders->phone;
            }
        }
        else{
            $admins = Admin::where('trax_id', $trax_id)->first();
            if($admins){
                $personal_contact = $admins->phone_number;
            }
        }
        $payslip_salary_details = $this->payslip_details;

        $basic_salary = ($payslip_salary_details['basic_salary'] != NULL) ? number_format($payslip_salary_details['basic_salary']) : '-';
        $house_rent = ($payslip_salary_details['house_rent'] != NULL) ? number_format($payslip_salary_details['house_rent']) : '-';
        $medical = ($payslip_salary_details['medical'] != NULL) ? number_format($payslip_salary_details['medical']) : '-';
        $gross_salary = ($payslip_salary_details['gross_salary'] != NULL) ? number_format($payslip_salary_details['gross_salary']) : '-';
        $payroll_days = ($payslip->payroll_days != NULL) ? $payslip->payroll_days : '-';
        $present_days = ($payslip->present_days != NULL) ? $payslip->present_days : '-';
        $absent_days = ($payslip->absent_days != NULL) ? $payslip->absent_days : '-';
        $pay_cut_days = ($payslip->pay_cut_days != NULL) ? $payslip->pay_cut_days : '-';
        $extra_paid_days = ($payslip->extra_paid_days != NULL) ? $payslip->extra_paid_days : '-';
        $fuel_days = ($payslip->fuel_days != NULL) ? $payslip->fuel_days : '-';


        $mobile_allowance = ($payslip_salary_details['mobile_allowance'] != NULL) ? number_format($payslip_salary_details['mobile_allowance']) : '-';
        $vehicle_allowance = ($payslip_salary_details['vehicle_allowance'] != NULL) ? number_format($payslip_salary_details['vehicle_allowance']) : '-';
        $fuel_allowance = ($payslip_salary_details['fuel_allowance'] != NULL) ? number_format($payslip_salary_details['fuel_allowance']) : '-';
        $conveyance_allowance = ($payslip_salary_details['conveyance_allowance'] != NULL) ? number_format($payslip_salary_details['conveyance_allowance']) : '-';
        $vehicle_maintenance = ($payslip_salary_details['vehicle_maintenance'] != NULL) ? number_format($payslip_salary_details['vehicle_maintenance']) : '-';
        $fixed_incentive = ($payslip_salary_details['fixed_incentive'] != NULL) ? number_format($payslip_salary_details['fixed_incentive']) : '-';
        $holiday_allowance = ($payslip_salary_details['holiday_allowance'] != NULL) ? number_format($payslip_salary_details['holiday_allowance']) : '-';
        $overtime = ($payslip_salary_details['overtime'] != NULL) ? number_format($payslip_salary_details['overtime']) : '-';
        $bonus = ($payslip_salary_details['bonus'] != NULL) ? number_format($payslip_salary_details['bonus']) : '-';
        $arrears = ($payslip_salary_details['arrears'] != NULL) ? number_format($payslip_salary_details['arrears']) : '-';
        $pickup_incentive = ($payslip_salary_details['pickup_incentive'] != NULL) ? number_format($payslip_salary_details['pickup_incentive']) : '-';
        $delivery_incentive = ($payslip_salary_details['delivery_incentive'] != NULL) ? number_format($payslip_salary_details['delivery_incentive'] ) : '-';
        $operations_incentive = ($payslip_salary_details['operation_incentive'] != NULL) ? number_format($payslip_salary_details['operation_incentive']) : '-';
        $extra_duty_allowance = ($payslip_salary_details['extra_duty_allowance'] != NULL) ? number_format($payslip_salary_details['extra_duty_allowance']) : '-';
        $others_addition = ($payslip_salary_details['others_addition'] != NULL) ? number_format($payslip_salary_details['others_addition']) : '-';

        $total_addition = ($payslip_salary_details['total_salary'] != NULL) ? number_format($payslip_salary_details['total_salary']) : '-';

        $paycut = ($payslip_salary_details['paycut'] != NULL) ? number_format($payslip_salary_details['paycut']) : '-';
        $absent = ($payslip_salary_details['absent'] != NULL) ? number_format($payslip_salary_details['absent'] ) : '-';
        $late_deduction = ($payslip_salary_details['late_deduction'] != NULL) ? number_format($payslip_salary_details['late_deduction']) : '-';
        $income_tax = ($payslip_salary_details['income_tax'] != NULL) ? number_format($payslip_salary_details['income_tax']) : '-';
        $eobi = ($payslip_salary_details['eobi'] != NULL) ? number_format($payslip_salary_details['eobi']) : '-';
        $advance_salary = ($payslip_salary_details['advance_salary'] != NULL) ? number_format($payslip_salary_details['advance_salary']) : '-';
        $month_closing = ($payslip_salary_details['month_closing'] != NULL) ? number_format($payslip_salary_details['month_closing']) : '-';
        $loan = ($payslip_salary_details['loan'] != NULL) ? number_format($payslip_salary_details['loan']) : '-';
        $fuel_card = ($payslip_salary_details['fuel_card'] != NULL) ? number_format($payslip_salary_details['fuel_card']) : '-';
        $open_parcel = ($payslip_salary_details['open_parcel'] != NULL) ? number_format($payslip_salary_details['open_parcel']) : '-';
        $phone_call = ($payslip_salary_details['phone_call'] != NULL) ? number_format($payslip_salary_details['phone_call']) : '-';
        $recovery = ($payslip_salary_details['recovery'] != NULL) ? number_format($payslip_salary_details['recovery']) : '-';
        $auction_sale = ($payslip_salary_details['auction_sale'] != NULL) ? number_format($payslip_salary_details['auction_sale']) : '-';
        $penalty = ($payslip_salary_details['penalty'] != NULL) ? number_format($payslip_salary_details['penalty']) : '-';
        $medical_insurance = ($payslip_salary_details['medical_insurance'] != NULL) ? number_format($payslip_salary_details['medical_insurance'] != NULL) : '-';
        $van_deduction = ($payslip_salary_details['van_deduction'] != NULL) ? number_format($payslip_salary_details['van_deduction']) : '-';
        $others_deduction = ($payslip_salary_details['others_deduction'] != NULL) ? number_format($payslip_salary_details['others_deduction']) : '-';

        $total_deduction = ($payslip_salary_details['total_deduction'] != NULL) ? number_format($payslip_salary_details['total_deduction']) : '-';
        $net_salary = ($payslip_salary_details['net_salary'] != NULL) ? number_format($payslip_salary_details['net_salary']) : '-';

        $html = '<!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Payslip</title>

                     <style>
                     @page {
                        size: A4 portrait;
                      }
                      body {
                        font-size: 0.95rem !important;
                        
                      }
                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }
                      .border.twice {
                        border-width: 1px !important;
                      }

                      .border.twice-top {
                        border-top-width: 1px !important;
                      }

                      .border.twice-bottom {
                        border-bottom-width: 1px !important;
                      }

                      .border.twice-left {
                        border-left-width: 1px !important;
                      }

                      .border.twice-right {
                        border-right-width: 1px !important;
                      }

                      .font-small {
                        font-size: 0.65rem !important;
                      }
                      
                      .table-borderless td, .table th {
                        border: none;
                     }
                     td{
                        color: #000;
                     }
                    </style>';

        $html .= '</head>
                  <body>
                   
                      <div class="table-responsive">
                          <table class="table table-borderless mb-0">
                          
                          <tbody>
                            <tr>
                              <td class="text-left align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class=""></td>
                       
                                 <td class="text-right align-middle"><h1 class="d-block">SALARY SLIP</h1></td>
                             </tr>
                             <tr>
                                <td class="text-left align-middle">Head Office (Karachi): </td>
                                <td class="text-right align-middle"><b>Payroll Month: </b><u>' . $payroll_month . '</u></td>
                             </tr>
                             <tr>
                                <td class="text-left align-middle">Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi.</td>
                                <td class="text-right align-middle"><b>Payroll Cut Off Date: </b> <u>' . $payroll_cut_off_date . '</u></td>
                                
                             </tr>
                             </tbody>
                         </table>';

        $html .= '<table class="table border table-sm">
                    
                    <tbody>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="8"><b>Employee Information</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Employee ID</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->trax_id . '</td>
                            <td colspan="2"  class="border twice-right">Date of Joining</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->joining_date . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Employee Name</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->name . '</td>
                            <td colspan="2"  class="border twice-right">Date of Confirmation</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->confirmation_date . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Designation</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->designation . '</td>
                            <td colspan="2"  class="border twice-right">Employee Type</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->employee_type . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Department</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->department . '</td>
                            <td colspan="2"  class="border twice-right">Employee Status</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->employee_status . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Location</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->hub . '</td>
                            <td colspan="2"  class="border twice-right">Personal Contact #</td>
                            <td colspan="2"  class="border twice-right">' . $personal_contact . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">CNIC No.</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->cnic . '</td>
                            <td colspan="2"  class="border twice-right">Bank Account No.</td>
                            <td colspan="2"  class="border twice-right">' . $payslip->iban . '</td>
                        </tr>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="8"><b>Salary Breakup</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Basic Salary</td>
                            <td colspan="2"  class="border twice-right">' . $basic_salary . '</td>
                            <td colspan="1"  class="border twice-right">Payroll Days</td>
                            <td colspan="1"  class="border twice-right">' . $payroll_days . '</td>
                            <td colspan="1"  class="border twice-right">Absent Days</td>
                            <td colspan="1"  class="border twice-right">' . $absent_days . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">House Rent</td>
                            <td colspan="2"  class="border twice-right">' . $house_rent . '</td>
                            <td colspan="1"  class="border twice-right">Present Days</td>
                            <td colspan="1"  class="border twice-right">' . $present_days . '</td>
                            <td colspan="1"  class="border twice-right">Extra Paid Days</td>
                            <td colspan="1"  class="border twice-right">' . $extra_paid_days . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Medical</td>
                            <td colspan="2"  class="border twice-right">' . $medical . '</td>
                            <td colspan="1"  class="border twice-right">Pay Cut Days</td>
                            <td colspan="1"  class="border twice-right">' . $pay_cut_days . '</td>
                            <td colspan="1"  class="border twice-right">Fuel Days</td>
                            <td colspan="1"  class="border twice-right">' . $fuel_days . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right"><b>Gross Salary</b></td>
                            <td colspan="2"  class="border twice-right">' . $gross_salary . '</td>
                            <td colspan="4"  class="border twice-right"></td>
                        </tr>
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="4"><b>Addition</b></td>
                            <td class="color primary border twice" colspan="4"><b>Deduction</b></td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Mobile Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $mobile_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Pay Cut</td>
                            <td colspan="2"  class="border twice-right">' . $paycut . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Vehicle Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $vehicle_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Absent</td>
                            <td colspan="2"  class="border twice-right">' . $absent . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Fuel Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $fuel_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Late Deduction</td>
                            <td colspan="2"  class="border twice-right">' . $late_deduction . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Conveyance Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $conveyance_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Income Tax</td>
                            <td colspan="2"  class="border twice-right">' . $income_tax . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Vehicle Maintenance</td>
                            <td colspan="2"  class="border twice-right">' . $vehicle_maintenance . '</td>
                            <td colspan="2"  class="border twice-right">EOBI</td>
                            <td colspan="2"  class="border twice-right">' . $eobi . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Fixed Incentive</td>
                            <td colspan="2"  class="border twice-right">' . $fixed_incentive . '</td>
                            <td colspan="2"  class="border twice-right">Advance Salary</td>
                            <td colspan="2"  class="border twice-right">' . $advance_salary . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Sunday / Holiday Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $holiday_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Month Closing</td>
                            <td colspan="2"  class="border twice-right">' . $month_closing . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Overtime</td>
                            <td colspan="2"  class="border twice-right">' . $overtime . '</td>
                            <td colspan="2"  class="border twice-right">Loan</td>
                            <td colspan="2"  class="border twice-right">' . $loan . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Bonus</td>
                            <td colspan="2"  class="border twice-right">' . $bonus . '</td>
                            <td colspan="2"  class="border twice-right">Fuel Card</td>
                            <td colspan="2"  class="border twice-right">' . $fuel_card . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Arrears</td>
                            <td colspan="2"  class="border twice-right">' . $arrears . '</td>
                            <td colspan="2"  class="border twice-right">Open Parcel</td>
                            <td colspan="2"  class="border twice-right">' . $open_parcel . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Pickup Incentive</td>
                            <td colspan="2"  class="border twice-right">' . $pickup_incentive . '</td>
                            <td colspan="2"  class="border twice-right">Phone Call</td>
                            <td colspan="2"  class="border twice-right">' . $phone_call . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Delivery Incentive</td>
                            <td colspan="2"  class="border twice-right">' . $delivery_incentive . '</td>
                            <td colspan="2"  class="border twice-right">Recovery</td>
                            <td colspan="2"  class="border twice-right">' . $recovery . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Operations Incentive</td>
                            <td colspan="2"  class="border twice-right">' . $operations_incentive . '</td>
                            <td colspan="2"  class="border twice-right">Auction Sale</td>
                            <td colspan="2"  class="border twice-right">' . $auction_sale . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Extra Duty Allowance</td>
                            <td colspan="2"  class="border twice-right">' . $extra_duty_allowance . '</td>
                            <td colspan="2"  class="border twice-right">Penalty</td>
                            <td colspan="2"  class="border twice-right">' . $penalty . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right">Others Addition</td>
                            <td colspan="2"  class="border twice-right">' . $others_addition . '</td>
                            <td colspan="2"  class="border twice-right">Medical Insurance</td>
                            <td colspan="2"  class="border twice-right">' . $medical_insurance . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right">Van Deduction</td>
                            <td colspan="2"  class="border twice-right">' . $van_deduction . '</td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="2" class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right"></td>
                            <td colspan="2"  class="border twice-right">Others Deduction</td>
                            <td colspan="2"  class="border twice-right">' . $others_deduction . '</td>
                        </tr>
                        
                        <tr class="text-center">
                            <td class="color primary border twice" colspan="2"><b>Total Addition</b></td>
                            <td class="color primary border twice" colspan="2">' . $total_addition . '</td>
                            <td class="color primary border twice" colspan="2"><b>Total Deduction</b></td>
                            <td class="color primary border twice" colspan="2">' . $total_deduction . '</td>
                        </tr>
                        <tr class="text-left">
                            <td class="color primary border twice" colspan="6"><b>Net Salary</b></td>
                            <td class="color primary border twice text-center" colspan="2">' . $net_salary . '</td>
                        </tr>
                        <tr class="text-left">
                            <td class="border twice" colspan="8" rowspan="5"><i>Note: This is a system generated document and does not require any signature.</i></td>
                        </tr>
                   </tbody>
                         </table>';


        $html .= ' 
                      </div>
                      </body>
                      </html>';

        $pdf = SnappyPDF::loadHTML($html);

        $filename = 'payslip_' . $payslip->id . Carbon::now()->format('Uu') . '-' . $payroll_month . '.pdf';
        $path = 'payslip_pdf/' . $filename;
        $result = $pdf->download($filename);
        Storage::disk('public')->put($path, $result);
        $payslip_pdf = new PayslipPdf();
        $payslip_pdf->payslip_id = $payslip->id;
        $payslip_pdf->file_path = $path;
        $payslip_pdf->save();
    }
}
