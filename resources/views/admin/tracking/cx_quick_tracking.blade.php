
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
                                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                                @foreach($shippers as $shipper)
                                                    <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-3">
                                        <fieldset>
                                            <input type="text" class="form-control" placeholder="Order ID" id="search_order_id">
                                        </fieldset>
                                    </div>
                                    <div class="col-2">
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
                    <h4 class="modal-title white">Add Request</h4>
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
                                        <input type="text" name="update_consignee_name" class="form-control" placeholder="Consignee Name" id="update_consignee_name" id="update_consignee_address" data-rule-required="true" data-msg-required="Consignee Name is required">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <input type="text" name="update_consignee_address" class="form-control" placeholder="Consignee Address*" id="update_consignee_address" data-rule-required="true" data-msg-required="Consignee Address is required">
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



    <script type="text/javascript">
        $(document).ready(function () {
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
            $('#search_order_id').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Shipper',
                width: '100%',
                allowClear: true
            });

            $("#search_form").keyup(function(event) {
                if (event.keyCode === 13) {
                    $("#search_filter_btn").click();
                }
            });

            var table;
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
                    }else if(id === 2){
                        $('#request_complaints').addClass('d-none');
                        $('#request_service').removeClass('d-none');
                        $('#request_feedback').addClass('d-none');
                        $('#AddNewRequest').removeClass('d-none');
                    }
                    else if(id === 3){
                        $('#request_complaints').addClass('d-none');
                        $('#request_service').addClass('d-none');
                        $('#request_feedback').removeClass('d-none');
                        $('#AddNewRequest').removeClass('d-none');
                    }else{
                        $('#request_complaints').addClass('d-none');
                        $('#request_service').addClass('d-none');
                        $('#AddNewRequest').addClass('d-none');
                    }
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
                $('.phone_number').inputmask({
                    'mask': '9999-9999999',
                    'clearIncomplete': true
                });

                jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                    if (this.context.length) {
                        body = [];

                        var jsonResult = $.ajax({
                            url: '{{ route('admin.cx_quick_tracking.cx_list') }}',
                            data: {
                                'page': 'all',
                                'search_tracking': $('#search_tracking_number').val(),
                                'search_shipper': $('#search_shipper').val(),
                                'search_phone_no': $('#search_consignee_phone_number').val(),
                                'search_order_id': $('#search_order_id').val(),
                            },
                            success: function (result) {
                                head = [];

                                head.push('S. No');
                                head.push('Tracking Number');
                                head.push('Order ID');
                                head.push('Origin');
                                head.push('Destination');
                                head.push('Address');
                                head.push('COD Amount.');
                                head.push('Status');
                                head.push('Shipper Name');
                                head.push('Consignee Name');
                                head.push('Consignee Phone Number');
                                $.each(result.data, function (index, values) {
                                    row = [];

                                    row.push(index + 1);
                                    row.push(values.tracking_number);
                                    row.push(values.order_id);
                                    row.push(values.origin);
                                    row.push(values.destination);
                                    row.push(values.address);
                                    row.push(values.cod_amount);
                                    row.push(values.status);
                                    row.push(values.shipper_name);
                                    row.push(values.consignee_name);
                                    row.push(values.consignee_phone_no);

                                    body.push(row);
                                });
                            },
                            async: false
                        });

                        return {body: body, header: head};
                    }
                });
                table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    buttons: [{
                        extend: 'excel',
                        title: 'CX Quick Tracking',
                        className: 'btn btn-primary mb-1',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                    lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                    pageLength: 50,
                    pagingType: 'full_numbers',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.cx_quick_tracking.cx_list') }}',
                        data: function (d) {
                            d.search_tracking = $('#search_tracking_number').val();
                            d.search_shipper = $('#search_shipper').val();
                            d.search_phone_no = $('#search_consignee_phone_number').val();
                            d.search_order_id = $('#search_order_id').val();
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
                    var tracking_number = table.row().data().tracking_number;
                    html_row = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ tracking_number +'</b></span></div>';
                    $('#requested_shipments').html(html_row);
                    $('#AddRequestModal').modal('show');
                });
                $('body').on('click','.update_consignee_info',function () {
                    var consignee_name = table.row().data().consignee_name;
                    var consignee_address = table.row().data().address;
                    var consignee_phone = table.row().data().consignee_phone_no;
                    var special_instructions = table.row().data().special_instructions;
                    update_shipment_id = $(this).parents('tr').attr('id');
                    var tracking_number = table.row().data().tracking_number;
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
                                    'description' : complaint_description
                                }
                            })
                                .done(function(data) {
                                    if (data.status) {
                                        if(data.flag){
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Already Lodged!',
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
                            $.ajax({
                                url: '{!! route('admin.crm.request.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'shipment_id': shipment_id,
                                    'case_nature_id' : case_nature_id,
                                    'complaint_id' : case_nature_complaint_id,
                                    'channel_id': case_nature_channel_id,
                                    'description' : service_description
                                }
                            })
                                .done(function(data) {
                                    if (data.status) {
                                        if(data.flag){
                                            var html = '';

                                            $.each(data.already_existed_shipments, function(index, tracking_number) {
                                                html += tracking_number + '<br/>';
                                            });

                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Already Lodged!',
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

                                            html += '<br/>Request/Complaint already lodged for the above Shipment(s) !';

                                            content = document.createElement('div');
                                            content.innerHTML = html;

                                            swal({
                                                title: 'Request / Complaint Already Lodged!',
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
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
                });
                $( "#update_consignee_info_form" ).validate({
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
                            text: 'Your shipment is being booked!',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        form.submit();
                    }
                });

                $('#UpdateConsigneeInfoModal').on('hide.bs.modal', function (e) {
                    $('#update_consignee_info_form')[0].reset();
                    $('#update_consignee_info_shipment_id').val('');
                    $('#update_consignee_name').val('');
                    $('#update_consignee_address').val('');
                    $('#update_consignee_phone').val('');
                    $('#update_special_instructions').val('');
                });
            }
            $('#search_filter_btn').on('click', function () {
                if($('#tracking_info').hasClass('d-none')) {
                    $('#tracking_info').removeClass('d-none');
                    init();
                }
                else {
                    table.draw();
                }
            });
        });
    </script>
@endsection