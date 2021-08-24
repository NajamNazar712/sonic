@extends('admin.layout.master')

@section('title', 'Finance')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                  Finance
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="finance_form" method="post"  novalidate="novalidate">

                                @csrf
                                @method('post')
                                <input type="hidden" name="fnf_id" value="{{$fnf->id}}">
                                <input type="hidden" name="approval" id="approval">
                                <fieldset>
                                    @include('admin.human_resource.fnf.employee_data')
                                    <h3 class="text-center mt-2 mb-2"><strong>Deductions</strong></h3>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">
                                                    Advance Salary :
                                                </label>
                                                <input type="text" id="salary" name="salary" class="form-control" placeholder="Amount" value="{{$finance->advance_salary ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                    Loan Outstanding :
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="loan" id="loan" value="{{$finance->loan_outstanding ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Short Cash:
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="cash" id="cash" value="{{$finance->short_cash ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">COD Recovery:

                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="cod" id="cod" value="{{$finance->cod_recovery ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Temporary Advance (IOU):

                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="iou" id="iou" value="{{$finance->iou ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">I. Tax (if any):

                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="tax" id="tax" value="{{$finance->tax ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <textarea cols="50"  class="form-control" rows="5" id="comments" name="comments" placeholder="Comments">{{$finance->comments ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        @if($finance == Null )
                                            <button type="button" id="submit_finance_info" class="btn btn-primary">Submit</button>
                                        @endif
                                        @if($finance != Null )
                                            @if($finance->status_id == 1 ||  $finance->status_id == 3 )
                                                <button type="button" id="approve" class="btn btn-success" value="Approve">Approve</button>
                                            @endif
                                            @if($finance != Null && $finance->status_id == 1)
                                                <button type="button" id="reject" class="btn btn-danger" value="Reject">Reject</button>
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
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {
            $('#submit_finance_info').on('click',function(){
                if($('#comments').val() == '' || $('#comments').val() == null){

                    var error = "Comments Cannot be Empty";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    return false;
                }
                else{
                    var route = '{{route('admin.human_resource.fnf.finance.submit')}}';
                    $('#finance_form').attr('action', route);
                    $('#finance_form').submit()
                }
            });

            $('#approve').on('click',function(){
                var route = '{{route("admin.human_resource.fnf.finance_status_edit")}}';
                $('#finance_form').attr('action', route);
                $('#approval').val('approved');
                $('#finance_form').submit()
            });

            $('#reject').on('click',function(){
                var route = '{{route("admin.human_resource.fnf.finance_status_edit")}}';
                $('#finance_form').attr('action', route);
                $('#approval').val('rejected');
                $('#finance_form').submit()
            });
        });
    </script>
@endsection