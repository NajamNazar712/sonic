@extends('client.layout.master')
@section('title','Pickups History')

@section('content')
    <h1 class="mb-1">
        Pickups History
        <span class="pull-right">Pickup Helpline No. 0348-1115858</span>
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Request ID.</th>
                        <th class="border-primary border-darken-1">Requested Date</th>
                        <th class="border-primary border-darken-1">No. of Shipments Booked</th>
                        <th class="border-primary border-darken-1">No. of Shipments Received</th>
                        <th class="border-primary border-darken-1">Contact Person</th>
                        <th class="border-primary border-darken-1">Vendor</th>
                        <th class="border-primary border-darken-1">Contact No(s)</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">City</th>
                        <th class="border-primary border-darken-1">Status</th>
{{--                        <th class="border-primary border-darken-1">Trax Reason</th>--}}
{{--                        <th class="border-primary border-darken-1">Trax Remarks</th>--}}
{{--                        <th class="border-primary border-darken-1">Shipper Remarks</th>--}}
{{--                        <th class="border-primary border-darken-1">Attempt Date/Time</th>--}}
                        <th class="border-primary border-darken-1">Attempt Count</th>
                        <th class="border-primary border-darken-1">View Details</th>
                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


    <div class="modal fade" id="shipment_modal" data-backdrop="static" role="dialog" aria-labelledby="shipment_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipment_modal_tittle"></h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view_detail_modal" data-backdrop="static" role="dialog" aria-labelledby="view_detail_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="view_detail_modal_tittle"></h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="modal-body">
                        <table class="table table-striped" id="view_details">
                            <thead>
                            <tr>
                                <th>Reason</th>
                                <th>Trax Remarks</th>
                                <th>Shipper Remarks</th>
                                <th>Attempt Date</th>
                                <th>Attempts</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                        @method('POST')
                        @csrf
                        <div class="container">
                            <div class="row">
                                <h2 class="heading">Pickup Requests(s)</h2>
                            </div>

                            <input type="hidden" id="requested_pickup_ids">
                            <div class="row old_scroll" id="requested_pickups">

                            </div>
                            <hr>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <fieldset class="form-group">
                                        <select name="case_nature_select" id="case_nature_select" class="form-control select2" data-rule-required="true" data-msg-required="Case Nature is required">
                                            @foreach($case_nature as $nature)
                                                <option value="{{$nature->id}}">{{$nature->name}}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="complaints d-none" id="request_complaints">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="case_nature_complaint" id="case_nature_complaints" class="form-control select2" data-rule-required="true" data-msg-required="Complaint Type is required">
                                                @foreach($case_nature_complaints as $complaints)
                                                    <option value="{{$complaints->id}}">{{$complaints->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="service d-none" id="request_service">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <select name="case_nature_request" id="case_nature_requests" class="form-control select2" data-rule-required="true" data-msg-required="Complaint Type is required">
                                                @foreach($case_nature_service_requests as $service)
                                                    <option value="{{$service->id}}">{{$service->type}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="service_description" id="service_description" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="feedback d-none" id="request_feedback">
                                <div class="row justify-content-center">
                                    <div class="col-8">
                                        <fieldset class="form-group">
                                            <textarea class="form-control" name="feedback_description_request" id="feedback_description_request" rows="5" placeholder="Enter Description Here..." data-rule-required="true" data-msg-required="Description is required"></textarea>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="claims d-none" id="request_claims">
                                <input type="hidden" name="pickup_request_ids" id="pickup_request_ids">
                                <input type="hidden" name="case_nature_id" id="case_nature_id">
                                <input type="hidden" name="complaint_id" id="complaint_id">
                                <input type="hidden" name="pickup_request" id="pickup_request">
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
                                    <div class="col-8" id="claim_product_cost_div">
                                        <fieldset class="form-group">
                                            <input class="form-control" name="claim_product_cost" id="claim_product_cost" value="" placeholder="Enter Product Cost">
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
    </div>

    <div class="modal fade" id="add_remarks_modal" role="dialog" aria-labelledby="add_remarks_title" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_remarks_title">Remarks</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="add_remarks_form" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">

                        <div class="form-group">
                            <input type="hidden" name="remarks_pickup_request_id" id="remarks_pickup_request_id" class="remarks_pickup_request_id" value="">
                            <input type="text" name="remarks" id="remarks" class="form-control remarks" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks is required">

                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add Remarks</button>
                            <button type="button" class="btn btn-secondary ml-2" data-dismiss="modal">Close</button>

                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
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
                    $('#AddNewRequest').removeClass('d-none');
                    $('#request_claims').removeClass('d-none');
                }else{
                    $('#request_complaints').addClass('d-none');
                    $('#request_service').addClass('d-none');
                    $('#request_feedback').addClass('d-none');
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
            $('#case_nature_complaints').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Complaint Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });
            $('#case_nature_requests').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Request Type",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.pickup.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Request ID.');
                            head.push('Request Date');
                            head.push('No. of Shipments Booked');
                            head.push('No. of Shipments Received');
                            head.push('Contact Person');
                            head.push('Vendor');
                            head.push('Contact No(s)');
                            head.push('Address');
                            head.push('City');
                            head.push('Status');
                            head.push('Trax Reason');
                            head.push('Trax Remarks');
                            head.push('Shipper Remarks');
                            head.push('Attempt Date/Time');
                            head.push('Attempt Count');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.pickup_request_id);
                                row.push(values.requested_at);
                                row.push(values.booked);
                                row.push(values.received);
                                row.push(values.contact_person);
                                row.push(values.vendor);
                                row.push(values.contact_number);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.status);
                                row.push(values.reason.replace('<br/>', '\r\n'));
                                row.push(values.remarks.replace('<br/>', '\r\n'));
                                row.push(values.shipper_remarks.replace('<br/>', '\r\n'));
                                row.push(values.attempt_date_time.replace('<br/>', '\r\n'));
                                row.push(values.attempts);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var shipment_remarks = {};
            var selected_rows = [];
            var selected_rows_nsa = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        text: 'Complain',
                        className: 'btn btn-danger complain',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if (selected_rows.length > 0) {
                                $('#AddRequestModal').modal('show');
                                $('#requested_pickup_ids').val(selected_rows);
                                var html_rows = '';
                                var count = 1;
                                table.rows().nodes().each(function (index) {
                                    var row = table.row(index);
                                    if ($(row.node()).hasClass('selected')) {
                                        var id = $(row.node()).find('td.pickup_request_id').text();
                                        html_rows += '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> ' + id + '</b></span></div>';
                                        count++;
                                    }
                                });
                                $('#requested_pickups').html(html_rows);
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Pickup History',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }, {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());
                                    row.select();

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.complain').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);
                                if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.complain').disable();
                                    }
                                }
                            });
                        }
                    }
                ],

                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: '{{ route('cod.pickup.list') }}',
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'pickup_request_id', name: 'v2_pickup_requests.id', class: 'align-middle pickup_request_id'},
                    {data: 'requested_at', name: 'v2_pickup_requests.created_at', class: 'align-middle requested_at'},
                    {data: 'booked_button', name: 'v2_pickup_requests.booked', class: 'text-center align-middle booked'},
                    {data: 'received_button', name: 'v2_pickup_requests.received', class: 'text-center align-middle received'},
                    {data: 'contact_person', name: 'usi.poc', class: 'align-middle contact_person'},
                    {data: 'vendor', name: 'usi.vendor', class: 'align-middle vendor'},
                    {data: 'contact_number', name: 'usi.phone', class: 'align-middle contact_number'},
                    {data: 'address', name: 'usi.pickup_address', class: 'align-middle address'},
                    {data: 'city', name: 'ci.name', class: 'align-middle city'},
                    {data: 'status', name: 'vprs.id', class: 'align-middle status'},
                    // {data: 'reason', name: 'reason', class: 'align-middle reason', orderable: false, searchable: false, width: 200},
                    // {data: 'remarks', name: 'remarks', class: 'align-middle remarks', orderable: false, searchable: false, width: 400},
                    // {data: 'shipper_remarks', name: 'shipper_remarks', class: 'align-middle shipper_remarks', orderable: false, searchable: false, width: 400},
                    // {data: 'attempt_date_time', name: 'attempt_date_time', class: 'align-middle attempt_date_time', orderable: false, searchable: false, width: 400},
                    {data: 'attempts', name: 'v2_pickup_requests.attempts', class: 'align-middle attempt'},
                    {data: 'view_details', name: '', class: 'align-middle view_details'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    $('td:eq(0)', row).addClass('select-checkbox');
                    if ($.inArray(data.shId, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.reason') || $(header).is('.view_details') || $(header).is('.remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempt_date_time') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    this.api().table().columns.adjust();
                }
            });


            var route = '{!! route('cod.tracking.index') !!}';
            $('body').on('click','#datatable tbody tr td.booked button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipment_modal #shipment_modal_tittle').html('');
                $('#shipment_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('cod.pickup.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_request_id': id,
                        'status': 0
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#shipment_modal #shipment_modal_tittle').html('Booked Shipments');
                            $('#shipment_modal .modal-body').html(shipments);
                            $('#shipment_modal').modal('show');


                        }
                    });

            });
            //todo : yahan p mujhe popup show krna h
            $('body').on('click','#datatable tbody tr td.view_details button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipment_modal #shipment_modal_tittle').html('');
                $('#shipment_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('cod.pickup.view_details') !!}',
                    method: 'GET',
                    data: {
                        'pickup_request_id': id,
                        'status': 1
                    }
                })
                    .done(function(data) {
                        var result = JSON.parse(data);
                        var count = JSON.parse(data).length;
                        $('#view_detail_modal').modal('show');
                        if (count != 0) {

                            $('#view_detail_modal #view_detail_modal_tittle').html('Details');
                            $('#view_details tbody ').html(`
                                    <tr>
                                    <td>${result.reason}</td>
                                    <td>${result.trax_remarks}</td>
                                    <td>${result.shipper_remarks}</td>
                                    <td>${result.attempt_date}</td>
                                    <td>${result.attempts}</td>
                                    </tr>`)
                        }
                        else{
                            $('#view_details tbody ').html('') ;
                        }
                    });

            });
            //todo : yahan p mujhe popup show krna h end

            $('body').on('click','#datatable tbody tr td.received button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipment_modal #shipment_modal_tittle').html('');
                $('#shipment_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('cod.pickup.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'pickup_request_id': id,
                        'status': 1
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#shipment_modal #shipment_modal_tittle').html('Assigned Shipments');
                            $('#shipment_modal .modal-body').html(shipments);
                            $('#shipment_modal').modal('show');


                        }
                    });

            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.complain').enable();
                }
                else {
                    table.button('.complain').disable();
                }
            });

            $('body').on('click','.cancel',function () {
                var id = $(this).parents('tr').attr('id');

                if(id != ''){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to cancel Pickup Request!',
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
                    }).then(function (confirm) {
                        if (confirm) {
                            $.ajax({
                                url:"{{route('cod.pickup.cancel')}}",
                                method:'POST',
                                data:{
                                    'pickup_request_id':id,
                                    '_token':'{{ csrf_token() }}',
                                }
                            }).done(function (data) {
                                if(data.status == 1){
                                    table.draw('false');
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                }

                            });
                        }
                    });


                }
            });

            $('body').on('click','.renew',function () {
                var id = $(this).parents('tr').attr('id');

                if(id != ''){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to renew Pickup Request!',
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
                    }).then(function (confirm) {
                        if (confirm) {
                            $.ajax({
                                url:"{{route('cod.pickup.renew')}}",
                                method:'POST',
                                data:{
                                    'pickup_request_id':id,
                                    '_token':'{{ csrf_token() }}',
                                }
                            }).done(function (data) {
                                if(data.status == 1){
                                    table.draw('false');
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                }

                            });
                        }
                    });


                }
            });
            $('body').on('click','.add_remarks',function () {
                var id = $(this).parents('tr').attr('id');
                if(id != ''){
                    $('#add_remarks_modal #remarks_pickup_request_id').val(id);
                    $('#add_remarks_modal').modal('show');
                }
            });

            $('#add_remarks_modal').on('hide.bs.modal', function () {
                $('#add_remarks_form input.remarks').val('');
            });

            $('#add_remarks_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                normalizer: function(value) {
                    return $.trim(value);
                },
                submitHandler: function(form) {
                    var remarks = $('#remarks').val();
                    var id = $('#remarks_pickup_request_id').val();
                    console.log(id);
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to add remarks against last attempt!',
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
                    }).then(function (confirm) {
                        if (confirm) {
                            $.ajax({
                                url:"{{route('cod.pickup.add_remarks')}}",
                                method:'POST',
                                data:{
                                    'pickup_request_id':id,
                                    '_token':'{{ csrf_token() }}',
                                    'remark':remarks
                                }
                            }).done(function (data) {
                                if(data.status == 1){
                                    table.draw('false');
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                }

                            });
                        }
                        $('#add_remarks_modal').modal('hide');
                    });
                }
            });

            var max_char_request = 245;
            $('#feedback_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });
            $('#service_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });
            $('#complaint_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char_request) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char_request) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char_request);
                }
            });

            $('body').on('change', '#add_request_form textarea', function () {
                $(this).val($(this).val().trim());
            });
            $('#add_request_form').on('submit',function (e) {
                e.preventDefault();
            });
            $( "#add_request_form" ).validate({
                errorClass:"danger",
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var case_nature_id = parseInt($('#case_nature_select').val());
                    if(case_nature_id === 1){
                        var complaint_id = $('#case_nature_complaints').val();
                        var description = $('#complaint_description').val();
                    }
                    else if(case_nature_id === 3){
                        var feedback_flag = true;
                        var feedback_description = $('#feedback_description_request').val();
                    }else{
                        var complaint_id = $('#case_nature_requests').val();
                        var description = $('#service_description').val();
                    }

                    if(case_nature_id === 3)
                    {
                        if(!feedback_description){
                            feedback_flag = false;
                            var error = "Please Enter Description!";
                            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                        if(feedback_flag){
                            $('#AddNewRequest').attr('disabled',true);
                            $.ajax({
                                url: '{!! route('cod.crm.feedback.add') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'pickup_request_ids': selected_rows,
                                    'description' : feedback_description
                                }
                            })
                                .done(function(data) {
                                    if(data.status){
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

                                    table.button('.complain').disable();

                                    selected_rows = [];

                                    table.rows().deselect();

                                    table.draw('false');

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled',false);
                                });
                        }
                    }
                    else if(case_nature_id === 4)
                    {
                        var nature_flag = true;
                        var case_nature_claim_id = $('#case_nature_claim').val();
                        var product_cost = $('#claim_product_cost').val();
                        var check_product_picture = $('#product_picture').val();
                        var check_invoice_picture = $('#invoice_picture').val();
                        $('#pickup_request_ids').val(selected_rows);
                        $('#case_nature_id').val(case_nature_id);
                        $('#complaint_id').val(case_nature_claim_id);
                        $('#pickup_request').val(1);
                        var formData = new FormData($('#add_request_form')[0]);
                        if(!case_nature_claim_id){
                            nature_flag = false;
                            var error = "Please select Claim type!";
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
                                var error = "Please enter Product Cost!";
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
                                url: '{!! route('cod.crm.request.add') !!}',
                                method: 'POST',
                                enctype: 'multipart/form-data',
                                data: formData,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                            })
                                .done(function(data) {
                                    if(data.status){
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

                                    table.button('.complain').disable();

                                    selected_rows = [];

                                    table.rows().deselect();

                                    table.draw('false');

                                    $('#AddRequestModal').modal('hide');
                                    $('#AddNewRequest').attr('disabled',false);
                                });
                        }
                    }
                    else {
                        $('#AddNewRequest').attr('disabled',true);
                        $.ajax({
                            url: '{!! route('cod.crm.request.add') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'pickup_request_ids': selected_rows,
                                'case_nature_id': case_nature_id,
                                'complaint_id': complaint_id,
                                'description': description,
                                'pickup_request' : 1
                            }
                        })
                            .done(function (data) {
                                if (data.status) {
                                    if (data.flag) {
                                        var html = '';

                                        $.each(data.already_existed_shipments, function (index, tracking_number) {
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
                                    } else {
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    }
                                    // toastr.success(data.success, 'Success!', {
                                    //     positionClass: 'toast-bottom-center',
                                    //     containerId: 'toast-bottom-center'
                                    // });
                                } else {
                                    toastr.error(data.error, 'Error!', {
                                        positionClass: 'toast-top-center',
                                        containerId: 'toast-top-center'
                                    });
                                }

                                table.button('.complain').disable();

                                selected_rows = [];

                                table.rows().deselect();

                                table.draw('false');

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

                $('#complaint_description').val('');
                $('#service_description').val('');
                $('#feedback_description_request').val('');
                $('#request_complaints').addClass('d-none');
                $('#request_service').addClass('d-none');
                $('#request_feedback').addClass('d-none');
                $('#request_claims').addClass('d-none');
                $('#case_nature_claim').val('').trigger('change');
                $('#claim_channel').val('').trigger('change');
                $('#claim_product_cost').val('');
            });
        });
    </script>
@endsection