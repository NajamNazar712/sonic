
@extends('admin.layout.master')
@section('title','CX Quick Tracking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    CX Quick Tracking
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                                <div class="row justify-content-center mb-2" id="search_form">
                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Tracking Number" id="search_tracking_number">
                                        </fieldset>
                                    </div>
                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Consignee Phone Number" id="search_consignee_phone_number">
                                        </fieldset>
                                    </div>
                                    <div class="col-3">
                                        <fieldset class="form-group">
                                            <select name="search_shipper[]" id="search_shipper" class="form-control select2" multiple>
                                            </select>
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-3" style="height: 60px">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Order ID" id="search_order_id">
                                        </fieldset>
                                    </div>

                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Request ID" id="crm_request_id">
                                        </fieldset>
                                    </div>
                                    <div class="col-3">
                                        <fieldset class="form-group">
                                            <select name="search_shipment_status" id="search_shipment_status" class="form-control select2">
                                                @foreach($shipment_statuses as $shipment_status)
                                                    <option value="{{$shipment_status->id}}">{{$shipment_status->name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div>
                                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                    </div>
                                </div>
                            </div>
                            <div id="tracking_info" class="d-none mb-3 ml-1 mr-1">
                                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                    <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Tracking Number</th>
                                        <th class="border-primary border-darken-1">Order ID</th>
                                        <th class="border-primary border-darken-1">Origin</th>
                                        <th class="border-primary border-darken-1">Destination</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">COD Amount</th>
                                        <th class="border-primary border-darken-1">Status</th>
                                        <th class="border-primary border-darken-1">Shipper Name</th>
                                        <th class="border-primary border-darken-1">Consignee Name</th>
                                        <th class="border-primary border-darken-1">Consignee Phone Number</th>
                                        <th class="border-primary border-darken-1"></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Get Support</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_request_form" method="post">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number(s)</h2>
                            </div>

                            <input type="hidden" id="requested_shipment_ids">
                            <div class="row old_scroll" id="requested_shipments">

                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select" class="form-control select2">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="complaints d-none" id="request_complaints">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2">
                                                @foreach($case_nature_complaints as $complaints)
                                                    <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complainant" id="case_nature_complainant"
                                                    class="form-control select2">
                                                <option value="1">Consignee</option>
                                                <option value="2">Shipper</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <input type="text" class="form-control" placeholder="Enter Phone Number" name="complainant_phone" id="complainant_phone">
                                        </fieldset>
                                    </div>


                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="complaint_channel" id="complaint_channels" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="service d-none" id="request_service">
                                <div class="row justify-content-center">
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="case_nature_request" id="case_nature_requests" class="form-control select2">
                                                @foreach($case_nature_service_requests as $service)
                                                    <option value="{{$service->id}}">{{$service->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <select name="request_channel" id="request_channels" class="form-control select2">
                                                @foreach($case_nature_channels as $channel2)
                                                    <option value="{{$channel2->id}}">{{$channel2->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="service_description" id="service_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="feedback d-none" id="request_feedback">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="feedback_channel_request" id="feedback_channel_request" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="feedback_description_request" id="feedback_description_request" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="claims d-none" id="request_claims">
                                <input type="hidden" name="shipment_ids" id="shipment_ids">
                                <input type="hidden" name="case_nature_id" id="case_nature_id">
                                <input type="hidden" name="complaint_id" id="complaint_id">
                                <input type="hidden" name="channel_id" id="channel_id">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="case_nature_tclaim" id="case_nature_claim" class="form-control select2">
                                                @foreach($case_nature_type_claims as $claim)
                                                    <option value="{{$claim->id}}">{{$claim->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="claim_channel" id="claim_channel" class="form-control select2">
                                                @foreach($case_nature_channels as $channel1)
                                                    <option value="{{$channel1->id}}">{{$channel1->channel}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8" id="claim_product_cost_div">
                                        <fieldset class="form-group">
                                            <input class="form-control" name="claim_product_cost" id="claim_product_cost" value="" placeholder="Enter Claim Amount">
                                        </fieldset>
                                    </div>
                                    <div class="col-8 text-left" id="claim_product_picture_div">
                                        <fieldset class="form-group">
                                            <label for="product_picture"><b>Product Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="product_picture" id="product_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>
                                    <div class="col-8 text-left" id="claim_invoice_picture_div">
                                        <fieldset class="form-group">
                                            <label for="invoice_picture"><b>Invoice Picture:</b></label>
                                            <input class="form-control form-control-sm" type="file" name="invoice_picture" id="invoice_picture" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Image is required">
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="description" id="claim_description" rows="5" placeholder="Enter Description Here..."></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="AddNewRequest" type="submit" class="btn btn-primary btn-block d-none">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><div class="modal fade text-left" id="UpdateConsigneeInfoModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="UpdateConsigneeInfoModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Update Consignee Info</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="update_consignee_info_form" method="post" action="{{route('admin.cx_quick_tracking.update')}}">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Tracking Number</h2>
                            </div>

                            <input type="hidden" name="update_consignee_info_shipment_id" id="update_consignee_info_shipment_id">
                            <div class="row old_scroll" id="update_consignee_info_shipment">

                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <input type="text" name="update_consignee_name" class="form-control" placeholder="Consignee Name*" id="update_consignee_name" id="update_consignee_address" data-rule-required="true" data-msg-required="Consignee Name is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <input type="text" name="update_consignee_address" class="form-control" placeholder="Consignee Address*" id="update_consignee_address" data-rule-required="true" data-msg-required="Consignee Address is required" data-rule-maxlength="255" data-msg-maxlength="Address can be maximum 255 characters">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <input type="text" name="update_consignee_phone" class="form-control phone_number" id="update_consignee_phone" placeholder="Consignee Phone Number*" data-rule-required="true" data-msg-required="Consignee Phone Number is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <textarea class="form-control" name="update_special_instructions" id="update_special_instructions" rows="5" placeholder="Enter Special Instructions Here..."></textarea>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button id="Update_consignee_info_button" type="submit" class="btn btn-primary btn-block">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }

        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
          
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function(options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = -1;
                    var jsonResult =
                        $.ajax({
                            url: '{{ route('admin.cx_quick_tracking.cx_list') }}',
                            method: 'GET',
                            data: params,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) 
                            {
                                head = [];
                                head.push('S. No.');
                                head.push('Tracking Number');
                                head.push('Order ID');
                                head.push('Origin');
                                head.push('Destination');
                                head.push('Address');
                                head.push('COD Amount');
                                head.push('Status');
                                head.push('Shipper Name');
                                head.push('Consignee Name');
                                head.push('Consignee Phone Number');

                                $.each(res.data || [], function(i, r) {
                                    var row = [];
                                    row.push(i + 1);
                                    row.push(r.tracking_number || r.tracking_number_link || '');
                                    row.push(r.order_id || '');
                                    row.push(r.origin || '');
                                    row.push(r.destination || '');
                                    row.push(r.address || '');
                                    row.push(r.cod_amount || '');
                                    row.push(r.status || '');
                                    row.push(r.shipper_name || '');
                                    row.push(r.consignee_name || '');
                                    row.push(r.consignee_phone_no || '');
                                    body.push(row);
                                });
                            },
                            async: false
                        });
                    return {
                        header: head,
                        body: body
                    };
                }
            });  
            $('#search_consignee_phone_number').inputmask({
                'mask': '9999-9999999',
                'clearIncomplete': true
            });

            $('#claim_product_cost').inputmask({
                'alias': 'decimal',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'digits': 2,
                'min': 0.00,
                'max': 1000000.00
            });
            $('#complainant_phone').inputmask({
                mask: '9999-9999999',
                'clearIncomplete': true
            });
            $('#search_tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#consignee_phone_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });

            $('#search_shipper').select2({
                width:'100%',
                placeholder:"Select Shipper",
                allowClear:true,
                multiple: true,
                minimumInputLength: 2,
                ajax: {
                    dataType: 'json',
                    url:  '{!! route('admin.accounts.shipper_names.dropdown',['type'=>'active']) !!}',
                        data: function (params) {
                            return {
                                search: params.term,
                            }
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                    delay: 700,
                }
            });


            $('#search_shipment_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Shipment Status',
                width: '100%',
                allowClear: true
            });

            var table;
            let trackingSelect, orderSelect, requestSelect;
            function init(){

                $('#case_nature_select').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Case Nature",
                    allowClear:true,
                    dropdownParent:$('#add_request_form')
                }).bind('change', function () {
                    var id = parseInt($(this).val());
                    if(id === 1){
                        $('#request_service').addClass('d-none');
                        $('#request_complaints').removeClass('d-none');
                        $('#request_feedback').addClass('d-none');
                        $('#AddNewRequest').removeClass('d-none');
                        $('#request_claims').addClass('d-none');
                    }else if(id === 2){
                        $('#request_complaints').addClass('d-none');
                        $('#request_service').removeClass('d-none');
                        $('#request_feedback').addClass('d-none');
                        $('#AddNewRequest').removeClass('d-none');
                        $('#request_claims').addClass('d-none');
                    }
                    else if(id === 3){
                        $('#request_complaints').addClass('d-none');
                        $('#request_service').addClass('d-none');
                        $('#request_feedback').removeClass('d-none');
                        $('#AddNewRequest').removeClass('d-none');
                        $('#request_claims').addClass('d-none');
                    }
                    else if(id === 4){
                        $('#request_complaints').addClass('d-none');
                        $('#request_service').addClass('d-none');
                        $('#request_feedback').addClass('d-none');
                        $('#request_claims').removeClass('d-none');
                        $('#AddNewRequest').removeClass('d-none');
                    }else{
                        $('#request_complaints').addClass('d-none');
                        $('#request_service').addClass('d-none');
                        $('#AddNewRequest').addClass('d-none');
                        $('#request_claims').addClass('d-none');
                    }
                });
                $('#case_nature_claim').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Claim Type",
                    allowClear:true,
                    dropdownParent:$('#add_request_form')
                }).bind('change', function () {
                    var id = parseInt($(this).val());
                    if(id === 26){
                        $('#claim_product_cost_div').addClass('d-none');
                        $('#claim_product_picture_div').addClass('d-none');
                        $('#claim_invoice_picture_div').addClass('d-none');
                    }
                    else{
                        $('#claim_product_cost_div').removeClass('d-none');
                        $('#claim_product_picture_div').removeClass('d-none');
                        $('#claim_invoice_picture_div').removeClass('d-none');
                    }
                });
                $('#claim_channel').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Channel",
                    allowClear:true,
                    dropdownParent:$('#add_request_form')
                });
                $('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Complaint Type",
                    allowClear:true,
                    dropdownParent:$('#add_request_form')
                });
                $('#complaint_channels').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Channel",
                    allowClear:true,
                    dropdownParent:$('#add_request_form')
                });
                $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Request Type",
                    allowClear:true,
                    dropdownParent:$('#add_request_form')
                });
                $('#request_channels').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Channel",
                    allowClear:true,
                    dropdownParent:$('#add_request_form')
                });
                $('#feedback_channel').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Channel",
                    allowClear:true,
                    dropdownParent:$('#add_feedback_form')
                });
                $('#feedback_channel_request').prepend('<option value="" selected="selected"></option>').select2({
                    width:'100%',
                    placeholder:"Select Channel",
                    allowClear:true,
                    dropdownParent:$('#add_request_form')
                });
                $('#case_nature_complainant').prepend('<option value="" selected="selected"></option>').select2({
                    width: '100%',
                    placeholder: "Select Complainant",
                    allowClear: true,
                    dropdownParent: $('#add_request_form')
                });
                $('.phone_number').inputmask({
                    'mask': '9999-9999999',
                    'clearIncomplete': true
                });

                table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    lengthMenu: [[10, 50, 100], [10, 50, 100]],
                    scrollX: true,
                    pageLength: 10,
                    buttons:[
                        {
                            extend: 'excel',
                            title: 'CX Quicking Tracking',
                            className: 'btn btn-primary',
                            text: '<i class="la la-file-excel-o"></i> Excel',
                        },
                    ],
                    pagingType: 'full_numbers',
                    processing: true,
                    language: {
                        processing: data_table_loader
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.cx_quick_tracking.cx_list') }}',
                      data: function (d) {
                            const itemsOf = (sel) => (sel && Array.isArray(sel.items)) ? sel.items : [];

                            d.search_tracking        = itemsOf(trackingSelect);
                            d.search_shipper         = $('#search_shipper').val();
                            d.search_phone_no        = $('#search_consignee_phone_number').val();
                            d.search_order_id        = itemsOf(orderSelect);
                            d.crm_request_id         = itemsOf(requestSelect);
                            d.search_shipment_status = $('#search_shipment_status').val();
                        }
                    },
                    rowId: 'shipment_id',
                    order: [1, 'desc'],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_numbers'},
                        {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                        {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                        {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                        {data: 'address', name: 'shipments.consignee_address', class: 'align-middle address'},
                        {data: 'cod_amount', name: 'shipments.amount', class: 'align-middle cod_amount'},
                        {data: 'status', name: 'ss.name', class: 'align-middle status'},
                        {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name'},
                        {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                        {data: 'consignee_phone_no', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone_no'},
                        {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                    ],
                    rowCallback: function (row, data, index) {
                        var info = table.page.info();
                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    },
                    initComplete: function () {

                        this.api().table().columns.adjust();
                    }
                });

                var shipment_id = null;
                var update_shipment_id = null;
                
                $('body').on('click','.request_add',function () {
                    shipment_id = $(this).parents('tr').attr('id');
                    var tracking_number = table.row( $(this).parents('tr') ).data().tracking_number;
                    html_row = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking_number +'</b></span></div>';
                    $('#requested_shipments').html(html_row);
                    $('#AddRequestModal').modal('show');
                });
                $('body').on('click','tr .action button.update_consignee_info',function () {
                    var consignee_name = table.row( $(this).parents('tr') ).data().consignee_name;
                    var consignee_address = table.row( $(this).parents('tr') ).data().address;
                    var consignee_phone = table.row( $(this).parents('tr') ).data().consignee_phone_no;
                    var special_instructions = table.row( $(this).parents('tr') ).data().special_instructions;
                    update_shipment_id = parseInt($(this).parents('tr').attr('id'));
                    var tracking_number = table.row( $(this).parents('tr') ).data().tracking_number;
                    html_row = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking_number +'</b></span></div>';
                    $('#update_consignee_info_shipment_id').val(update_shipment_id);
                    $('#update_consignee_name').val(consignee_name);
                    $('#update_consignee_address').val(consignee_address);
                    $('#update_consignee_phone').val(consignee_phone);
                    $('#update_special_instructions').val(special_instructions);
                    $('#update_consignee_info_shipment').html(html_row);
                    $('#UpdateConsigneeInfoModal').modal('show');
                });

                $( "#add_request_form" ).bind('submit', function (e) {
                    e.preventDefault();
                    var case_nature_id = parseInt($('#case_nature_select').val());
                    if(case_nature_id === 1){
                        var nature_flag = true;
                        var case_nature_complaint_id = $('#case_nature_complaints').val();
                        var case_nature_channel_id = $('#complaint_channels').val();
                        var complaint_description = $('#complaint_description').val();
                        var complainant_phone = $('#complainant_phone').val();
                        var case_nature_complainant = $('#case_nature_complainant').val();

                        if(!case_nature_complaint_id){
                            nature_flag = false;
                            var error = "Please select Complaint type!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!case_nature_channel_id){
                            nature_flag = false;
                            var error = "Please select Channel!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!complaint_description){
                            nature_flag = false;
                            var error = "Please select Description!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if (!complainant_phone) {
                            nature_flag = false;
                            var error = "Please enter complainant phone!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!case_nature_complainant) {
                            nature_flag = false;
                            var error = "Please select complainant!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if(nature_flag){
                            $('#AddNewRequest').attr('disabled',true);
                            $.ajax({
                                url: '{!! route('admin.crm.request.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'shipment_id': shipment_id,
                                    'case_nature_id' : case_nature_id,
                                    'complaint_id' : case_nature_complaint_id,
                                    'channel_id': case_nature_channel_id,
                                    'description' : complaint_description,
                                    'complainant_phone' : $('#complainant_phone').val(),
                                    'case_nature_complainant' : $('#case_nature_complainant').val(),
                                }
                            })
                                .done(function(data) {
                                    if (data.status) {
                                        if(data.flag){
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                        }
                                        else {
                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                        }

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Cannot Be Lodged!',
                                                content: content,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'Close',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            });
                                        }else{
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                        // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                    else {
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled',false);
                                });
                        }

                    }else if(case_nature_id == 2){
                        var nature_flag = true;
                        var case_nature_complaint_id = $('#case_nature_requests').val();
                        var case_nature_channel_id = $('#request_channels').val();
                        var service_description = $('#service_description').val();
                        if(!case_nature_complaint_id){
                            nature_flag = false;
                            var error = "Please select Complaint type!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!case_nature_channel_id){
                            nature_flag = false;
                            var error = "Please select Channel!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!service_description){
                            nature_flag = false;
                            var error = "Please select Description!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(nature_flag){
                            $('#AddNewRequest').attr('disabled',true);
                            if(case_nature_complaint_id == 39) {
                                swal({
                                    title: 'Are you sure to change service type?',
                                    text: 'Select Yes to change service type!',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'No',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Yes',
                                            value: true,
                                            visible: true,
                                            closeModal: true
                                        }
                                    },
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true
                                }).then(function (confirm){
                                    if(confirm){
                                        $.ajax({
                                            url: '{!! route('admin.crm.request.add') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'shipment_id': shipment_id,
                                                'case_nature_id' : case_nature_id,
                                                'complaint_id' : case_nature_complaint_id,
                                                'channel_id': case_nature_channel_id,
                                                'description' : service_description,
                                                'complainant_phone' : $('#complainant_phone').val(),
                                                'case_nature_complainant' : $('#case_nature_complainant').val(),
                                            }
                                        })
                                            .done(function(data) {
                                                if (data.status) {
                                                    if(data.flag){
                                                        var html = '';

                                                        $.each(data.already_existed_shipments, function(index, tracking_number) {
                                                            html += tracking_number + '<br/>';
                                                        });

                                                        if (!data.cannot_change) {
                                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                                        }
                                                        else {
                                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                                        }

                                                        content = document.createElement('div');
                                                        content.innerHTML = html;

                                                        swal({
                                                            title: 'Request / Complaint Cannot Be Lodged!',
                                                            content: content,
                                                            icon: 'warning',
                                                            buttons: {
                                                                cancel: {
                                                                    text: 'Close',
                                                                    value: null,
                                                                    visible: true,
                                                                    closeModal: true,
                                                                },
                                                            },
                                                            closeOnClickOutside: false,
                                                            closeOnEsc: false,
                                                            dangerMode: true
                                                        });
                                                    }else{
                                                        toastr.success(data.success, 'Success!', {
                                                            positionClass: 'toast-bottom-center',
                                                            containerId: 'toast-bottom-center'
                                                        });
                                                    }
                                                    // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                                }
                                                else {
                                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                                }

                                                $('#AddRequestModal').modal('hide');
                                                $('#AddNewRequest').attr('disabled',false);
                                            });
                                    }else{
                                        $('#AddNewRequest').attr('disabled',false);
                                    }
                                });
                            }
                            else {
                                $.ajax({
                                    url: '{!! route('admin.crm.request.add') !!}',
                                    method: 'POST',
                                    data: {
                                        '_token': '{{ csrf_token() }}',
                                        'shipment_id': shipment_id,
                                        'case_nature_id' : case_nature_id,
                                        'complaint_id' : case_nature_complaint_id,
                                        'channel_id': case_nature_channel_id,
                                        'description' : service_description,
                                        'complainant_phone' : $('#complainant_phone').val(),
                                        'case_nature_complainant' : $('#case_nature_complainant').val(),
                                    }
                                })
                                    .done(function(data) {
                                        if (data.status) {
                                            if(data.flag){
                                                var html = '';

                                                $.each(data.already_existed_shipments, function(index, tracking_number) {
                                                    html += tracking_number + '<br/>';
                                                });

                                                if (!data.cannot_change) {
                                                    html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                                }
                                                else {
                                                    html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                                }

                                                content = document.createElement('div');
                                                content.innerHTML = html;

                                                swal({
                                                    title: 'Request / Complaint Cannot Be Lodged!',
                                                    content: content,
                                                    icon: 'warning',
                                                    buttons: {
                                                        cancel: {
                                                            text: 'Close',
                                                            value: null,
                                                            visible: true,
                                                            closeModal: true,
                                                        },
                                                    },
                                                    closeOnClickOutside: false,
                                                    closeOnEsc: false,
                                                    dangerMode: true
                                                });
                                            }else{
                                                toastr.success(data.success, 'Success!', {
                                                    positionClass: 'toast-bottom-center',
                                                    containerId: 'toast-bottom-center'
                                                });
                                            }
                                            // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                        }
                                        else {
                                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                        }

                                        $('#AddRequestModal').modal('hide');
                                        $('#AddNewRequest').attr('disabled',false);
                                    });
                            }
                        }
                    }else if(case_nature_id == 3) {
                        var feedback_flag = true;
                        var feedback_channel = $('#feedback_channel_request').val();
                        var feedback_description = $('#feedback_description_request').val();
                        if (!feedback_description) {
                            feedback_flag = false;
                            var error = "Please select Description!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (!feedback_channel) {
                            feedback_flag = false;
                            var error = "Please select Channel!";
                            toastr.error(error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                        if (feedback_flag) {
                            $('#AddNewRequest').attr('disabled',true);
                            $.ajax({
                                url: '{!! route('admin.crm.feedback.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'channel_id': $('#feedback_channel_request').val(),
                                    'shipment_id': shipment_id,
                                    'description': feedback_description
                                }
                            })
                                .done(function (data) {
                                    if (data.status) {
                                        if(data.flag){
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                        }
                                        else {
                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                        }

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Cannot Be Lodged!',
                                                content: content,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'Close',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            });
                                        }else{
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                    } else {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled',false);
                                });
                        }
                    }
                    else if(case_nature_id === 4){
                        var nature_flag = true;
                        var case_nature_claim_id = $('#case_nature_claim').val();
                        var case_nature_channel_id = $('#claim_channel').val();
                        var product_cost = parseFloat($('#claim_product_cost').inputmask('unmaskedvalue'));
                        var check_product_picture = $('#product_picture').val();
                        var check_invoice_picture = $('#invoice_picture').val();
                        $('#shipment_ids').val(shipment_id);
                        $('#case_nature_id').val(case_nature_id);
                        $('#channel_id').val(case_nature_channel_id);
                        $('#complaint_id').val(case_nature_claim_id);
                        var formData = new FormData($('#add_request_form')[0]);
                        if(!case_nature_claim_id){
                            nature_flag = false;
                            var error = "Please select Claim type!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!case_nature_channel_id){
                            nature_flag = false;
                            var error = "Please select Channel!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(!$('#claim_description').val()){
                            nature_flag = false;
                            var error = "Please enter the claim description!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(case_nature_claim_id !== "26"){
                            if(!check_product_picture){
                                nature_flag = false;
                                var error = "Please attach Product Picture!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            if(!product_cost){
                                nature_flag = false;
                                var error = isNaN(product_cost) ? "Please enter Claim Amount!" : "Claim Amount cannot be zero !!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                            if(!check_invoice_picture){
                                nature_flag = false;
                                var error = "Please attach Invoice Picture!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }

                        if(nature_flag){
                            $('#AddNewRequest').attr('disabled',true);
                            $.ajax({
                                url: '{!! route('admin.crm.request.add') !!}',
                                method: 'POST',
                                enctype: 'multipart/form-data',
                                data: formData,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                            })
                                .done(function(data) {
                                    if (data.status) {
                                        if(data.flag){
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            if (!data.cannot_change) {
                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s)!';
                                        }
                                        else {
                                            html += '<br/>Request for Change cannot be opened for the above Shipment(s) at the Current Status!';
                                        }

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Cannot Be Lodged!',
                                                content: content,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: {
                                                        text: 'Close',
                                                        value: null,
                                                        visible: true,
                                                        closeModal: true,
                                                    },
                                                },
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                dangerMode: true
                                            });
                                        }else{
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                        // toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                    else {
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }
                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled',false);
                                });
                        }

                    }
                });
                $('#AddRequestModal').on('hide.bs.modal', function (e) {
                    $('#add_request_form')[0].reset();
                    $('#case_nature_complaints').val('').trigger('change');
                    $('#case_nature_select').val('').trigger('change');
                    $('#case_nature_requests').val('').trigger('change');
                    $('#complaint_channels').val('').trigger('change');
                    $('#request_channels').val('').trigger('change');
                    $('#feedback_channel_request').val('').trigger('change');
                    $('#complaint_description').val('');
                    $('#service_description').val('');
                    $('#feedback_description_request').val('');
                    $('#request_claims').addClass('d-none');
                    $('#case_nature_claim').val('').trigger('change');
                    $('#claim_channel').val('').trigger('change');
                    $('#claim_product_cost').val('');
                    $('#case_nature_complainant').val('').trigger('change');
                    $('#complainant_phone').val('');
                });
                var validator_consignee_info_form = $( "#update_consignee_info_form" ).validate({
                    errorClass: 'danger',
                    successClass: 'success',
                    normalizer: function(value) {
                        return $.trim(value);
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('w-100').appendTo(element.parent('.form-group'));
                    },
                    submitHandler: function(form) {
                        $(form).find('button[type=submit]').attr('disabled', 'disabled');

                        swal({
                            title: 'Please Wait!',
                            text: 'Your shipment is being updated!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                });

                $('#UpdateConsigneeInfoModal').on('hide.bs.modal', function (e) {
                    // console.log($('#update_consignee_info_form')[0]);
                    validator_consignee_info_form.resetForm();
                    $('#update_consignee_info_form')[0].reset();
                    $('#update_consignee_info_shipment_id').val('');
                    $('#update_consignee_name').val('');
                    $('#update_consignee_address').val('');
                    $('#update_consignee_phone').val('');
                    $('#update_special_instructions').val('');
                });
            }

            function check_inputs(){
                var isInputFilled = $('#search_form input[type="text"]').filter(function() {
                    return $(this).val().trim() !== '';
                }).length > 0;

                // Check if at least one select is selected
                var isSelectSelected = $('#search_form select').filter(function() {
                    return $(this).val() !== '';
                }).length > 0;

                // Check if at least one dropdown is selected
                var isDropdownSelected = $('#search_form .select2').filter(function() {
                    return $(this).val() !== '';
                }).length > 0;

                // If none of the conditions are met, display an alert
                if (!isInputFilled && !isSelectSelected && !isDropdownSelected) {
                    alert('At least one input field, select, or dropdown must be filled or selected.');
                    return false; // prevent form submission
                }else{
                    return true;
                }
            }

            $('#search_filter_btn').on('click', function () {
                if(check_inputs()) {
                    if ($('#tracking_info').hasClass('d-none')) {
                        $('#tracking_info').removeClass('d-none');
                        init();
                    } else {
                        table.draw();
                    }
                }
            });

            function initNumericOnlySelectize(selector, minLength = 1, placeholder = '') {
                var $el = $(selector);
                if ($el.length === 0) return null;

                var select = $el.selectize({
                    placeholder: placeholder,
                    delimiter: ',',
                    createOnBlur: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function () { return false; },
                    create: function (input) {
                    input = input.trim();
                    if ($.isNumeric(input) && input.length >= minLength) {
                        return { value: input, text: input };
                    }
                    return false;
                    }
                });

                var inst = select[0].selectize;
                inst.$control_input.on('input', function () {
                    var v = this.value.replace(/[^0-9,]/g, '');
                    if (this.value !== v) this.value = v;
                });
                inst.$control_input.on('keydown', function (e) {
                    if (e.key === 'Enter') {
                    e.preventDefault();
                    inst.createItem();
                    }
                });

                return inst;
            }

            function initAnySelectize(selector, minLength = 1, placeholder = '') {
                var $el = $(selector);
                if ($el.length === 0) return null;

                var select = $el.selectize({
                    placeholder: placeholder,
                    delimiter: ',',
                    createOnBlur: true,
                    persist: false,
                    plugins: ['remove_button'],
                    onDropdownOpen: function () { return false; },
                    create: function (input) {
                    input = input.trim();
                    if (input.length >= minLength) {
                        return { value: input, text: input };
                    }
                    return false;
                    }
                });

                var inst = select[0].selectize;
                inst.$control_input.on('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        inst.createItem();
                    }
                });

                return inst;
            }

            trackingSelect = initNumericOnlySelectize('#search_tracking_number', 6, 'Tracking Number(s)*');
            requestSelect  = initNumericOnlySelectize('#crm_request_id', 1, 'Request ID(s)*');

            orderSelect    = initAnySelectize('#search_order_id', 1, 'Order ID(s)*');
            });
    </script>
@endsection