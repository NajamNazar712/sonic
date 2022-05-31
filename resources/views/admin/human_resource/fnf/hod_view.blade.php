@extends('admin.layout.master')

@section('title', 'HOD Approval')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    HOD Approval
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="hod_form" action="{{route('admin.human_resource.fnf.hod_approval_submit')}}" method="post"  novalidate="novalidate">
                                @csrf
                                @method('post')
                                <input type="hidden" name="fnf_id" value="{{$fnf->id}}">
                                <input type="hidden" name="approval" id="approval">
                                <fieldset>
                                    @include('admin.human_resource.fnf.employee_data')

                                    @if($fnf->manager)
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
                                    @endif

                                    @if($fnf->customer_experience)
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
                                    @endif

                                    @if($fnf->administration)
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
                                    @endif

                                    @if($fnf->it_support)
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
                                    @endif

                                    @if($fnf->finance)
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
                                    @endif

                                    <hr>
                                    <h3 class="text-center mt-2 mb-2"><strong>HOD </strong></h3>
                                    <div class="row justify-content-center">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <textarea cols="50"  class="form-control"  rows="5" id="hod_comments" name="hod_comments" placeholder="Enter Comments">{{$approval->comments ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </fieldset>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        @if($fnf->manager && $fnf->customer_experience &&  $fnf->administration &&  $fnf->it_support  && $fnf->finance )
                                            @if($fnf->manager->status_id == 2 && $fnf->customer_experience->status_id == 2 &&  $fnf->administration->status_id == 2 &&  $fnf->it_support->status_id == 2  && $fnf->finance->status_id == 2 && ($approval == Null || $approval->status_id == 3 || $approval->status_id == 1))
                                             <button type="button" id="approve" class="btn btn-success">Approve</button>
                                            @endif
                                            @if(($approval == Null) || ($approval != Null && $approval->status_id !=2 && $approval->status_id !=3) )
                                                <button type="button" id="reject" class="btn btn-danger">Reject</button>
                                            @endif
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
         
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    
    <script>
        $(document).ready(function() {
            $('#approve').on('click',function(){
                if( $('#hod_comments').val() == '' || $('#hod_comments').val() == null){
                    var error = "Comments cannot be empty";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    return false;
                }
                else{
                    $('#approval').val('approved');
                    $('#hod_form').submit()
                }
            });
            $('#reject').on('click',function(){
                if( $('#hod_comments').val() == '' || $('#hod_comments').val() == null){
                    var error = "Comments cannot be empty";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    return false;
                }
                else{
                    $('#approval').val('rejected');
                    $('#hod_form').submit()
                }
            });
        });
    </script>
@endsection