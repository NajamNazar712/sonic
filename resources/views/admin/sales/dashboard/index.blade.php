@extends('admin.layout.master')

@section('title', 'Sales Dashboard')

@section('content')
    <h1>Sales Dashboard</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('admin.inc.messages')
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @if (session('role_id') == 1 || in_array(276, session('permissions')))
                                <div id="search_form" class="row mb-2 justify-content-center">
                                    <div class="col-4">
                                        <fieldset class="form-group">
                                            <select name="search_admins[]" id="search_admins" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
                                                @foreach($sale_name as $admin)
                                                    <option value="{{$admin->id}}">{{$admin->name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                    </div>
                                </div>
                            @endif
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No</th>
                                    <th class="border-primary border-darken-1">Account ID</th>
                                    <th class="border-primary border-darken-1">Account Type</th>
                                    <th class="border-primary border-darken-1">Company Name</th>
                                    <th class="border-primary border-darken-1">City Name</th>
                                    <th class="border-primary border-darken-1">Contact Person</th>
                                    <th class="border-primary border-darken-1">Phone Number</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">Email Address</th>
                                    <th class="border-primary border-darken-1">Product Type</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Sales Person Tagged</th>
                                    <th class="border-primary border-darken-1">Request Date</th>
                                    <th class="border-primary border-darken-1">Rate Added By</th>
                                    <th class="border-primary border-darken-1">Rate Updated By</th>
                                    <th class="border-primary border-darken-1">Rate Status</th>
                                    <th class="border-primary border-darken-1">Rate Status Remarks</th>
                                    <th class="border-primary border-darken-1">Rate Approved By</th>
                                    <th class="border-primary border-darken-1">Account Activated By</th>
                                    <th class="border-primary border-darken-1">Account Activation Date</th>
                                    <th class="border-primary border-darken-1">Account Disable Remarks</th>
                                    <th class="border-primary border-darken-1">Documents Status</th>
                                    <th class="border-primary border-darken-1">Documents Rejection Reason</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade text-left" id="SalesTagModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SalesTagModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Tag Sales Person</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="shipper_id">
                    <select name="Sale_person" id="saletag" class="form-control select2">
                        @foreach($sale_name as $sn)
                            <option value="{{ $sn->id }}" > {{ $sn->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="salesTagSubmit">Submit</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">



@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {


        });

    </script>

@endsection

