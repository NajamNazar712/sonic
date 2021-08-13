@extends('admin.layout.master')

@section('title', 'Reporting Manager')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Reporting Manager
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="rm_form"  method="post"  novalidate="novalidate">

                                @csrf
                                @method('post')
                                <input type="hidden" name="fnf_id" value="{{$fnf->id}}">
                                <input type="hidden" name="approval" id="approval">
                                <fieldset>
                                    @include('admin.human_resource.fnf.employee_data')
                                    <h3 class="text-center mt-2 mb-2"><strong>Payments</strong></h3>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">
                                                    Overtime:
                                                </label>
                                                <input type="text" id="overtime" name="overtime" class="form-control" placeholder="Amount/Hrs" value="{{$rm->overtime ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                    Sunday / Holiday :
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount/Hrs" name="holiday" id="holiday" value="{{$rm->holiday ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Pickup Incentive Fuel:
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount/Hrs" name="pickup_incentive" id="pickup_incentive" value="{{$rm->pickup_incentive ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Fixed Incentive (If any):
                                                  
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount/Hrs" name="fixed_incentive" id="fixed_incentive" value="{{$rm->fixed_incentive ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Delivery Incentive:
                                                  
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount/Hrs" name="delivery_incentive" id="delivery_incentive" value="{{$rm->delivery_incentive ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Hard Route/Extra Duty:
                                                  
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount/Hrs" name="extra_duty" id="extra_duty" value="{{$rm->extra_duty ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-center mb-2"><strong>Deductions</strong></h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">Temporary Advance (IOU):
                                                  
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control" name="iou"  id="iou" placeholder="Amount" value="{{$rm->iou ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="average_shipment_duration">Penalty:
                                                  
                                                </label>
                                                <div>
                                                    <input type="text" class="form-control" name="penalty" id="penalty" placeholder="Amount" value="{{$rm->penalty ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                   <textarea cols="50"  class="form-control" rows="5" id="comments" name="comments" placeholder="Comments">{{$rm->comments ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        @if($rm == Null )
                                            <button type="button" id="submit_rm_info" class="btn btn-primary">Submit</button>
                                        @endif
                                        @if($rm != Null && $rm->status_id == 1 ||  $rm->status_id == 3 )
                                            <button type="button" id="approve" class="btn btn-success" value="Approve">Approve</button>
                                        @endif
                                        @if($rm != Null && $rm->status_id == 1)
                                            <button type="button" id="reject" class="btn btn-danger" value="Reject">Reject</button>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
             $('#submit_rm_info').on('click',function(){
                if($('#comments').val() == '' || $('#comments').val() == null){

                    var error = "Comments cannot be empty";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    return false;
                }
                else{
                    var route = '{{route("admin.human_resource.fnf.rm.submit")}}';
                    $('#rm_form').attr('action', route);
                    $('#rm_form').submit()
                }
             });

             $('#approve').on('click',function(){
                var route = '{{route("admin.human_resource.fnf.rm_status_edit")}}';
                 $('#rm_form').attr('action', route);
                 $('#approval').val('approved');
                 $('#rm_form').submit()
             });
             $('#reject').on('click',function(){
                var route = '{{route("admin.human_resource.fnf.rm_status_edit")}}';
                 $('#rm_form').attr('action', route);
                 $('#approval').val('rejected');
                 $('#rm_form').submit()
             });
        });
    </script>
@endsection