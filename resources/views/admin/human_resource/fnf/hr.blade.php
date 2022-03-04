@extends('admin.layout.master')

@section('title', 'Human Resources')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Human Resources
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="hr_form"  method="post"  novalidate="novalidate">

                                @csrf
                                @method('post')
                                <input type="hidden" name="fnf_id" value="{{$fnf->id}}">
                                <fieldset>
                                    <div class="row justify-content-center">
                                        <h3><strong>FNF Request Details</strong></h3>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">
                                                    Employee ID:
                                                    <span class="danger">*</span>
                                                </label>
                                                <select name="trax_id" id="trax_id" class="select2 form-control required" style="width: 100%" >
                                                    @foreach($trax_ids as $data)
                                                        <option value="{{$data->trax_id}}">{{$data->trax_id}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                    Employee Name:
                                                    <span class="danger">*</span>
                                                </label>
                                                <input type="text" class="form-control required" placeholder="Employee Name (Alphabet Only)" name="employee_name" id="employee_name" data-rule-required="true" data-msg-required="Name is required" value="{{$fnf->employee->name}}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Designation:
                                                    <span class="danger">*</span>
                                                </label>
                                                <select name="designation" id="designation" class="select2 form-control required" style="width: 100%" disabled>
                                                    @foreach($designations as $designation)
                                                            <option value="{{$designation->id}}">{{$designation->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Department:
                                                    <span class="danger">*</span>
                                                </label>
                                                <select name="department_id" id="department_id" class="select2 form-control required" style="width: 100%" disabled>
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference">Date of Joining:</label>
                                                <div class="form-group input-group">
                                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                                    </div>
                                                    <input type="text" name="joining_date" class="form-control bg-primary border-primary white rounded-right pickadate" id="joining_date" placeholder="Joining Date" data-rule-required="true" data-msg-required="Joining Date is required" value="{{$fnf->joining_date}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference">Date of Resign:</label>
                                                <div class="form-group input-group">
                                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                                    </div>
                                                    <input type="text" name="resign_date" class="form-control bg-primary border-primary white rounded-right pickadate" id="resign_date" placeholder="Resign Date" data-rule-required="true" data-msg-required="Resign Date is required" value="{{$fnf->resign_date}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">Line Manager Email:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="email" class="form-control required"  name="line_manager" placeholder="Line Manager Email" data-rule-required="true" data-msg-required="Email is required" value="{{$fnf->reporting_manager->email}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">HOD Email:
                                                    <span class="danger">*</span>
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control required"  name="hod" placeholder="HOD Email" data-rule-required="true" data-msg-required="Email is required" value="{{$fnf->department_head->email}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <hr>
                                        <h3 class="text-center"><strong>Reporting Manager</strong></h3>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">
                                                        Overtime:
                                                    </label>
                                                    <input type="text" id="overtime" name="overtime" class="form-control" placeholder="Amount/Hrs" value="{{$fnf->manager->overtime ?? ''}}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipper_poc">
                                                        Sunday / Holiday :
                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount/Hrs" name="holiday" id="holiday" value="{{$fnf->manager->holiday ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_address">Pickup Incentive Fuel:
                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount/Hrs" name="pickup_incentive" id="pickup_incentive" value="{{$fnf->manager->pickup_incentive ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Fixed Incentive (If any):

                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount/Hrs" name="fixed_incentive" id="fixed_incentive" value="{{$fnf->manager->fixed_incentive ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Delivery Incentive:

                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount/Hrs" name="delivery_incentive" id="delivery_incentive" value="{{$fnf->manager->delivery_incentive ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Hard Route/Extra Duty:

                                                    </label>
                                                    <input type="text" class="form-control" disabled  placeholder="Amount/Hrs" name="extra_duty" id="extra_duty" value="{{$fnf->manager->extra_duty ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-center mb-2"><strong>Deductions</strong></h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Temporary Advance (IOU):

                                                    </label>
                                                    <div>
                                                        <input type="text" class="form-control" name="iou"  disabled id="iou" placeholder="Amount" value="{{$fnf->manager->iou ?? ''}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Penalty:

                                                    </label>
                                                    <div>
                                                        <input type="text" class="form-control" name="penalty" disabled id="penalty" placeholder="Amount" value="{{$fnf->manager->penalty ?? ''}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Comments:

                                                    </label>
                                                    <div>
                                                        <textarea cols="50"  class="form-control" rows="5"  disabled id="comments" name="comments" placeholder="Comments">{{$fnf->manager->comments ?? ''}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Status:

                                                    </label>
                                                    <div>
                                                        <input  class="form-control status"  disabled id="rm_status" name="rm_status" placeholder="Comments" value="{{$fnf->manager->status->name ?? ''}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <h3 class="text-center mt-2 mb-2"><strong>Customer Experience</strong></h3>
                                        <h6 class="text-center mt-2 mb-2"><strong>Deductions</strong></h6>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">
                                                        Phone Call Deduction:
                                                    </label>
                                                    <input type="text" id="phone_call_deduction" disabled name="phone_call_deduction" class="form-control" placeholder="Amount" value="{{$fnf->customer_experience->call_deduction ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipper_poc">
                                                        Open Parcel:
                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount" name="open_parcel" id="open_parcel" value="{{$fnf->customer_experience->parcel ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_address">Fake Status:
                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount" name="fake_status" id="fake_status" value="{{$fnf->customer_experience->fake_status ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Month Closing:
                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount" name="month_closing" id="month_closing" value="{{$fnf->customer_experience->month_closing ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div>
                                                        <textarea cols="50"  disabled class="form-control" rows="5" id="comments" name="comments" placeholder="Comments">{{$fnf->customer_experience->comments ?? ''}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Status:
                                                    </label>
                                                    <div>
                                                        <input  class="form-control status"  disabled id="customer_status" name="customer_status" placeholder="Comments" value="{{$fnf->manager->status->name ?? ''}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <h3 class="text-center mt-2 mb-2"><strong>Administration</strong></h3>
                                        <h6 class="text-center mt-2 mb-2"><strong>Deductions</strong></h6>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">
                                                        Auction Sell:
                                                    </label>
                                                    <input type="text" id="auction_sell" disabled name="auction_sell" class="form-control" placeholder="Amount" value="{{$fnf->administration->auction ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipper_poc">
                                                        T- Shirts:
                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount" name="tshirts" id="tshirts" value="{{$fnf->administration->shirt ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_address">Co'Car Maintenance/Repair (if any):
                                                    </label>
                                                    <input type="text" class="form-control" disabled placeholder="Amount" name="maintenance" id="maintenance" value="{{$fnf->administration->maintenance ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Status:

                                                    </label>
                                                    <div>
                                                        <input  class="form-control status"  disabled id="admin_status" name="admin_status" placeholder="Status" value="{{$fnf->administration->status->name ?? ''}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Comments:
                                                    </label>
                                                    <div>
                                                        <textarea cols="50"  class="form-control" disabled rows="5" id="comments" name="comments" placeholder="Comments">{{$fnf->administration->comments ?? ''}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <h3 class="text-center mt-2 mb-2"><strong>IT Support</strong></h3>
                                        <div class="row justify-content-center">
                                            <div class="col-md-6 text-center">
                                                <div class="form-group">
                                                    <div>
                                                        <textarea cols="50" disabled class="form-control" rows="5" id="comments" name="comments" placeholder="Comments">{{$fnf->it_support->comments ?? ''}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Status:

                                                    </label>
                                                    <div>
                                                        <input  class="form-control status"  disabled id="it_status" name="it_status" placeholder="Status" value="{{$fnf->it_support->status->name ?? ''}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <h3 class="text-center mt-2 mb-2"><strong>Finance</strong></h3>
                                        <h6 class="text-center mt-2 mb-2"><strong>Deductions</strong></h6>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">
                                                        Advance Salary :
                                                    </label>
                                                    <input type="text" id="salary" name="salary" disabled class="form-control" placeholder="Amount" value="{{$fnf->finance->advance_salary ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="shipper_poc">
                                                        Loan Outstanding :
                                                    </label>
                                                    <input type="text" class="form-control" placeholder="Amount" disabled name="loan" id="loan" value="{{$fnf->finance->loan_outstanding ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company_address">Short Cash:
                                                    </label>
                                                    <input type="text" class="form-control" placeholder="Amount" disabled name="cash" id="cash" value="{{$fnf->finance->short_cash ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">COD Recovery:

                                                    </label>
                                                    <input type="text" class="form-control" placeholder="Amount" name="cod" disabled id="cod" value="{{$fnf->finance->cod_recovery ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Temporary Advance (IOU):

                                                    </label>
                                                    <input type="text" class="form-control" placeholder="Amount" disabled name="iou" id="iou" value="{{$fnf->finance->iou ?? ''}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">I. Tax (if any):

                                                    </label>
                                                    <input type="text" class="form-control" placeholder="Amount" disabled name="tax" id="tax" value="{{$fnf->finance->tax ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div>
                                                        <textarea cols="50"  class="form-control" disabled rows="5" id="comments" name="comments" placeholder="Comments">{{$fnf->finance->comments ?? ''}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="average_shipment_duration">Status:

                                                    </label>
                                                    <div>
                                                        <input  class="form-control status"  disabled id="finance_status" name="finance_status" placeholder="Status" value="{{$fnf->finance->status->name ?? ''}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    
                                    <hr>
                                    <h3 class="text-center mt-2 mb-2"><strong>HOD </strong></h3>
                                    <div class="row justify-content-center">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Comments

                                                </label>
                                                <div>
                                                    <textarea cols="50"  class="form-control" disabled rows="5" id="hod_comments" name="hod_comments" placeholder="Enter Comments">{{$fnf->hod_approval->comments ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">Status:

                                                </label>
                                                <div>
                                                    <input  class="form-control status"  disabled id="hod_status" name="hod_status" placeholder="Status" value="{{$fnf->hod_approval->status->name ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <hr>
                                    <h3 class="text-center mt-2 mb-2"><strong>HR Details</strong></h3>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">
                                                    Medical Re Imbursement (if any) :
                                                </label>
                                                <input type="text" id="medical" name="medical" class="form-control" placeholder="Amount" value="{{$hr->medical ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                    Notice Period :
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="notice_period" id="notice_period" value="{{$hr->notice_period ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Penalty (if any) :
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="penalty" id="penalty"  value="{{$hr->penalty ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Van Deduction:
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="deduction" id="deduction"  value="{{$hr->van_deduction ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <textarea cols="50"  class="form-control" rows="5" id="hr_comments" name="comments" placeholder="Comments">{{$hr->comments ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        @if($hr == null )
                                         <button type="submit" id="submit_hr_info" class="btn btn-primary">Submit</button>
                                        @endif
                                        @if($hod_approval != null && $hod_approval == 2)
                                             <button type="submit" id="hr_confirm" class="btn btn-success">Complete Request</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style>
        @if($fnf->manager)
            @if($fnf->manager->status_id == 1)
                   #rm_status{
            box-shadow: 0 0 5px steelblue;
        }
        @elseif($fnf->manager->status_id == 2)
               #rm_status{
            box-shadow: 0 0 5px limegreen;
        }
        @elseif($fnf->manager->status_id == 3)
               #rm_status{
            box-shadow: 0 0 5px orangered;
        }
        @endif
        @endif

         @if($fnf->customer_experience)
            @if($fnf->customer_experience->status_id == 1)
                   #customer_status{
            box-shadow: 0 0 5px steelblue;
        }
        @elseif($fnf->customer_experience->status_id == 2)
               #customer_status{
            box-shadow: 0 0 5px limegreen;
        }
        @elseif($fnf->customer_experience->status_id == 3)
               #customer_status{
            box-shadow: 0 0 5px orangered;
        }
        @endif
         @endif


         @if($fnf->administration)
            @if($fnf->administration->status_id == 1)
                   #admin_status{
            box-shadow: 0 0 5px steelblue;
        }
        @elseif($fnf->administration->status_id == 2)
               #admin_status{
            box-shadow: 0 0 5px limegreen;
        }
        @elseif($fnf->administration->status_id == 3)
               #admin_status{
            box-shadow: 0 0 5px orangered;
        }
        @endif
        @endif


         @if($fnf->it_support)
             @if($fnf->it_support->status_id == 1)
                   #it_status{
            box-shadow: 0 0 5px steelblue;
        }
        @elseif($fnf->it_support->status_id == 2)
               #it_status{
            box-shadow: 0 0 5px limegreen;
        }
        @elseif($fnf->it_support->status_id == 3)
               #it_status{
            box-shadow: 0 0 5px orangered;
        }
        @endif
         @endif

         @if($fnf->finance)
            @if($fnf->finance->status_id == 1)
                   #finance_status{
            box-shadow: 0 0 5px steelblue;
        }
        @elseif($fnf->finance->status_id == 2)
               #finance_status{
            box-shadow: 0 0 5px limegreen;
        }
        @elseif($fnf->finance->status_id == 3)
               #finance_status{
            box-shadow: 0 0 5px orangered;
        }
        @endif
    @endif

       @if($fnf->hod_approval)
         @if($fnf->hod_approval->status_id == 1)
        #hod_status{
            box-shadow: 0 0 5px steelblue;
        }
        @elseif($fnf->hod_approval->status_id == 2)
        #hod_status{
            box-shadow: 0 0 5px limegreen;
        }
        @elseif($fnf->hod_approval->status_id == 3)
        #hod_status{
            box-shadow: 0 0 5px orangered;
        }
        @endif
        @endif

    </style>

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

          $('#department_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Department*'
            });

            $('#designation').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Designation*'
            });

            $('#trax_id').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Trax Id*'
            }).bind('change',function(){
                var id = $(this).val();
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.human_resource.fnf.employee_data') !!}',
                        method: 'POST',
                        data: {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status == 1){
                            $('#employee_name').val(data.data.name);
                            $('#designation').val(data.data.designation).trigger('change');
                            $('#department_id').val(data.data.department).trigger('change');
                        }
                        else{
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });


            var department_id = @json($fnf->employee->department_id);
            if(department_id){
                $('#department_id').val(department_id).trigger('change');
            }

            var designation_id = @json($fnf->employee->designation_id);
            if(designation_id){
                $('#designation').val(designation_id).trigger('change');
            }

            var trax_id = @json($fnf->employee->trax_id);
            if(trax_id){
                $('#trax_id').val(trax_id).trigger('change');
            }

            var joining_date = $('#joining_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    //$('#to_date_root').css('top', '40px');
                    $('.picker').css('position','relative');
                },
                onSet: function(context) {

                }
            });

            var resign_date = $('#resign_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    //$('#to_date_root').css('top', '40px');
                    $('.picker').css('position','relative');
                },
                onSet: function(context) {

                }
            });

            $('#submit_hr_info').on('click',function(){
                if($('#hr_comments').val() == '' || $('#hr_comments').val() == null){

                    var error = "Comments Cannot be Empty";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    return false;
                }
                else{
                    var route = '{{route('admin.human_resource.fnf.hr.submit')}}';
                    $('#hr_form').attr('action', route);
                    $('#hr_form').submit()
                }
            });

            $('#hr_confirm').on('click',function(){
                
                var route = '{{route("admin.human_resource.fnf.hr_status_edit")}}';
                $('#hr_form').attr('action', route);
                $('#hr_form').submit()
            });
        });
    </script>
@endsection