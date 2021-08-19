@extends('admin.layout.master')

@section('title', 'Administration')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                  Administration
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="admin_form"  method="post" novalidate="novalidate">

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
                                                    Auction Sell:
                                                </label>
                                                <input type="text" id="auction_sell" name="auction_sell" class="form-control" placeholder="Amount" value="{{$admin->auction ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shipper_poc">
                                                    T- Shirts:
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="tshirts" id="tshirts" value="{{$admin->shirt ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Co'Car Maintenance/Repair (if any):
                                                </label>
                                                <input type="text" class="form-control" placeholder="Amount" name="maintenance" id="maintenance" value="{{$admin->maintenance ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <textarea cols="50"  class="form-control" rows="5" id="comments" name="comments" placeholder="Comments">{{$admin->comments ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        @if($admin == Null )
                                            <button type="button" id="submit_admin_info" class="btn btn-primary">Submit</button>
                                        @endif
                                        @if($admin != Null )
                                            @if($admin->status_id == 1 ||  $admin->status_id == 3 )
                                                <button type="button" id="approve" class="btn btn-success" value="Approve">Approve</button>
                                            @endif
                                             @if($admin != Null && $admin->status_id == 1)
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
            $('#submit_admin_info').on('click',function(){
                if( $('#comments').val() == '' || $('#comments').val() == null){

                    var error = "Comments cannot be empty";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                    return false;
                }
                else{
                    var route = '{{route('admin.human_resource.fnf.administration.submit')}}';
                    $('#admin_form').attr('action', route);
                    $('#admin_form').submit()
                }
            });

            $('#approve').on('click',function(){
                var route = '{{route("admin.human_resource.fnf.administration_status_edit")}}';
                $('#admin_form').attr('action', route);
                $('#approval').val('approved');
                $('#admin_form').submit()
            });

            $('#reject').on('click',function(){
                var route = '{{route("admin.human_resource.fnf.administration_status_edit")}}';
                $('#admin_form').attr('action', route);
                $('#approval').val('rejected');
                $('#admin_form').submit()
            });
        });
    </script>
@endsection