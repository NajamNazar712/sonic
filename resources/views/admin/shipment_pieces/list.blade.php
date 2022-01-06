
@extends('admin.layout.master')
@section('title','Shipment Pieces List')

@section('content')
    <h1 class="mb-1">
        Shipment Pieces List
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Search Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                    <span class="la la-calendar-o"></span>
                                </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Search Date (To)">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">COD</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Date & Time Entered</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Request Status</th>
                        <th class="border-primary border-darken-1">Last Status By Date</th>
                        <th class="border-primary border-darken-1">Last Status By</th>
                        <th class="border-primary border-darken-1">Image</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <!--Rider popup -->
    <div class="modal fade text-left" id="RiderModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="RiderModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Rider (Return Note)</h4>

                </div>
                <form id="return_rider_form" class="justify-content-center" novalidate="novalidate" method="post" action="{{ route('admin.multiple_pieces.hold.return_note_create') }}">
                    @csrf
                    @method('POST')
                    <div class="modal-body text-center">
                        <input type="hidden" name="shipment_ids" id="shipment_ids">
                        <div class="form-group">
                            <select name="rider_select" id="rider_select" class="form-control select2" data-rule-required="true" data-msg-required="Rider is required">
                                @foreach($riders as $rider)
                                    <option value="{{$rider->id}}">{{$rider->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="route" id="route" class="form-control select2" data-rule-required="true" data-msg-required="Route is required">
                                @foreach($routes as $route)
                                    <option value="{{$route->id}}">{{$route->code}} ({{$route->start}} to {{$route->end}})</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer text-center">
                        <button type="submit" class="btn btn-primary">Create & Print</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--Rider popup -->
    <!--Image Upload popup -->
    <div class="modal fade text-left" id="uploadImage" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="uploadImage"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Image Upload</h4>

                </div>
                <div class="modal-body  text-center">
                    <form id="attachment_upload_form" class="form-horizontal" method="post" action="{{route('admin.multiple_pieces.upload_attachment')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="col text-center mt-2">
                            <h4><b>Upload Attachment</b></h4>
                        </div>
                        <div class="col form-group">
                            <input type="hidden" name="shipment_image_id" id="shipment_image_id"/>
                            <input class="form-control form-control-sm" type="file" name="upload_attachment" id="upload_attachment" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-rule-required="true" data-msg-required="Image is required" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-3">
                                <button id="" type="button" class="btn btn-danger btn-block" data-dismiss="modal">Close</button>
                            </div>
                            <div class="col-3">
                                <button type="submit" name="update" id="attachment_upload_form_submit" class="btn btn-primary">Submit</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

    <style type="text/css">
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.multiple_pieces.hold.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Shipper');
                            head.push('COD');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Date & Time Entered');
                            head.push('Status');
                            head.push('Request Status');
                            head.push('Last Status By Date');
                            head.push('Last Status By');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.amount);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.created_at);
                                row.push(values.status);
                                row.push(values.request_status);
                                row.push(values.last_updated_by);
                                row.push(values.last_updated_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    @if (session('role_id') == 1 || in_array(649, session('permissions')))
                    {
                        text: 'Wait For Remaining Piece(s)',
                        className: 'btn btn-primary remaining_piece',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to change shipments to Wait for Remaining piece!',
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
                                    if(confirm){
                                        blockPagePermanently();
                                        $.ajax({
                                            url:"{{route('admin.multiple_pieces.hold.wait_remaining_pieces_bulk')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids': selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                            }
                                        }).done(function (data) {
                                            if(data.status == 0){
                                                table.rows().nodes().each(function(index) {
                                                    var row = table.row(index);

                                                    if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                                        row.deselect();

                                                        id = parseInt(row.id());

                                                        var index = $.inArray(id, selected_rows);

                                                        if (index !== -1) {
                                                            selected_rows.splice(index, 1);
                                                        }

                                                        if (selected_rows.length == 0) {
                                                            table.button('.print').disable();
                                                            table.button('.remaining_piece').disable();
                                                            table.button('.return_back_shipper').disable();
                                                            table.button('.single_piece').disable();
                                                        }
                                                    }
                                                });
                                                table.draw('false');
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }else{
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                            }
                                            UnblockPagePermanently();

                                        });
                                    }
                                });
                            }

                        }
                    },
                    @endif
                    @if (session('role_id') == 1 || in_array(650, session('permissions')))
                    {
                        text: 'Return Back To Shipper',
                        className: 'btn btn-primary return_back_shipper',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to change shipments to Return Back to Shipper!',
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
                                    if(confirm){
                                        blockPagePermanently();
                                        $.ajax({
                                            url:"{{route('admin.multiple_pieces.hold.return_back_to_shipper_bulk')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids': selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                            }
                                        }).done(function (data) {
                                            if(data.status == 0){
                                                table.rows().nodes().each(function(index) {
                                                    var row = table.row(index);

                                                    if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                                        row.deselect();

                                                        id = parseInt(row.id());

                                                        var index = $.inArray(id, selected_rows);

                                                        if (index !== -1) {
                                                            selected_rows.splice(index, 1);
                                                        }

                                                        if (selected_rows.length == 0) {
                                                            table.button('.print').disable();
                                                            table.button('.remaining_piece').disable();
                                                            table.button('.return_back_shipper').disable();
                                                            table.button('.single_piece').disable();
                                                        }
                                                    }
                                                });
                                                table.draw('false');
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }else{
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                            }
                                            UnblockPagePermanently();

                                        });
                                    }
                                });
                            }

                        }
                    },
                    @endif
                    @if (session('role_id') == 1 || in_array(651, session('permissions')))
                    {
                        text: 'Switch To Single Piece',
                        className: 'btn btn-primary single_piece',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                swal({
                                    title: 'Are You Sure?',
                                    text: 'Select Yes to change shipments to Single Piece!',
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
                                    if(confirm){
                                        blockPagePermanently();
                                        $.ajax({
                                            url:"{{route('admin.multiple_pieces.hold.single_piece_bulk')}}",
                                            method:'POST',
                                            data:{
                                                'shipment_ids': selected_rows,
                                                '_token':'{{ csrf_token() }}',
                                            }
                                        }).done(function (data) {
                                            if(data.status == 0){
                                                table.rows().nodes().each(function(index) {
                                                    var row = table.row(index);

                                                    if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                                        row.deselect();

                                                        id = parseInt(row.id());

                                                        var index = $.inArray(id, selected_rows);

                                                        if (index !== -1) {
                                                            selected_rows.splice(index, 1);
                                                        }

                                                        if (selected_rows.length == 0) {
                                                            table.button('.print').disable();
                                                            table.button('.remaining_piece').disable();
                                                            table.button('.return_back_shipper').disable();
                                                            table.button('.single_piece').disable();
                                                        }
                                                    }
                                                });
                                                table.draw('false');
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }else{
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                            }
                                            UnblockPagePermanently();

                                        });
                                    }
                                });
                            }
                        }
                    },
                    @endif
                    @if (session('role_id') == 1 || in_array(372, session('permissions')))
                    {
                        text: '<i class="la la-print"></i> Print & Create Return Note',
                        className: 'btn btn-primary print',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows.length > 0){
                                $('#RiderModal').modal('show');
                            }

                        }
                    },
                    @endif
                    {
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.print').enable();
                                    table.button('.remaining_piece').enable();
                                    table.button('.return_back_shipper').enable();
                                    table.button('.single_piece').enable();
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

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.print').disable();
                                        table.button('.remaining_piece').disable();
                                        table.button('.return_back_shipper').disable();
                                        table.button('.single_piece').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Shipment Pieces List',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.multiple_pieces.hold.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'shId',
                order: [[8, 'desc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'created_at', name: 'shipment_pieces_requests.created_at', class: 'align-middle created_at'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'request_status', name: 'request_status', class: 'align-middle request_status'},
                    {data: 'last_updated_at', name: 'shipment_pieces_requests.last_updated_at', class: 'align-middle last_updated_at'},
                    {data: 'last_updated_by', name: 'last_updated_by', class: 'align-middle last_updated_by'},
                    {data: 'image_view', name: 'image_view', class: 'align-middle image_viewa',orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if (data.request_status_id == 3 || data.status == 'Pending') {
                        $('td:eq(0)', row).addClass('select-checkbox');

                        if ($.inArray(data.shId, selected_rows) !== -1) {
                            table.row(row).select();
                        }
                    }
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var request_status_select = '<select name="request_status_select" id="request_status_select" class="select2 form-control"></select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="1">Pending</option>' +
                        '<option value="2">Resolved</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action') || $(header).is('.image_viewa')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.request_status')){
                            $(request_status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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
                    var data = $.map({!! $request_status !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#request_status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Request Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $("#rider_select").prepend('<option value="" selected></option>').select2({
                placeholder: "Select Rider*",
                width:'100%',
                dropdownParent:$('#return_rider_form')
            });
            $('#route').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Route*',
                width:'100%',
                dropdownParent:$('#return_rider_form')
            });
            $('#rider_name').on('change',function () {
                var route = $(this).find(":selected").data("id");
                $('#route').val(route).trigger('change');
            });

            $('.datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.print').enable();
                    table.button('.remaining_piece').enable();
                    table.button('.return_back_shipper').enable();
                    table.button('.single_piece').enable();
                }
                else {
                    table.button('.print').disable();
                    table.button('.remaining_piece').disable();
                    table.button('.return_back_shipper').disable();
                    table.button('.single_piece').disable();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            $('#datatable tbody').on('click','tr td.action a',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    if($(this).hasClass('single_piece')){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to change shipment to Single Piece!',
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
                            if(confirm){
                                blockPagePermanently();
                                $.ajax({
                                    url:"{{route('admin.multiple_pieces.hold.single_piece')}}",
                                    method:'POST',
                                    data:{
                                        'shipment_id':id,
                                        '_token':'{{ csrf_token() }}',
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                    }
                                    UnblockPagePermanently();

                                });
                            }
                        });

                    }else if($(this).hasClass('remaining_piece')){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to wait for remaining pieces!',
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
                            if(confirm){
                                blockPagePermanently();
                                $.ajax({
                                    url:"{{route('admin.multiple_pieces.hold.wait_remaining_pieces')}}",
                                    method:'POST',
                                    data:{
                                        'shipment_id':id,
                                        '_token':'{{ csrf_token() }}',
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                    }
                                    UnblockPagePermanently();

                                });
                            }
                        });
                    }else if($(this).hasClass('return_to_shipper')){
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to wait for Return Back To Shipper!',
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
                            if(confirm){
                                blockPagePermanently();
                                $.ajax({
                                    url:"{{route('admin.multiple_pieces.hold.return_back_to_shipper')}}",
                                    method:'POST',
                                    data:{
                                        'shipment_id':id,
                                        '_token':'{{ csrf_token() }}',
                                    }
                                }).done(function (data) {
                                    if(data.status == 0){
                                        table.draw('false');
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }else{
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                    }
                                    UnblockPagePermanently();

                                });
                            }
                        });
                    }
                    else if($(this).hasClass('image_upload')){
                        $('#uploadImage #shipment_image_id').val(id);
                        $('#uploadImage').modal('show');
                    }
                }


            });

            $('#return_rider_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $('#shipment_ids').val(selected_rows);
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');
                    swal({
                        title: 'Please Wait!',
                        text: 'Return Note is being created!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();

                }
            });

            @if(session('print'))
            var pid = '{{ session('print') }}';
            print(pid);
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.multiple_pieces.hold.return_note_print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            @endif
            $('#attachment_upload_form').validate({
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
                        text: 'Uploading Attachment!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                    form.submit();
                }
            });
            $('#uploadImage').on('hidden.bs.modal', function () {
                $("#attachment_upload_form").validate().resetForm();
                $('#upload_attachment').val('');
            });
        });
    </script>
@endsection