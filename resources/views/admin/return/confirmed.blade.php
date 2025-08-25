@extends('admin.layout.master')
@section('title','Return Confirmed Shipments')

@section('content')
    <h1 class="mb-1">
        Return Confirmed Shipments
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <form id="track_form" class="mb-1" novalidate="novalidate">
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <div class="form-group">
                                <input type="text" name="tracking_numbers" id="tracking_number" class="dt_search tracking_numbers"
                                       placeholder="Tracking Number(s)" data-tags-input-name="tracking_number">
                            </div>
                        </div>
                        <div class="col-3">
                            <select name="search_shipping_mode" id="search_shipping_mode" class="form-control select2">
                                @foreach($shipping_mode as $mode)
                                    <option value="{{$mode->id}}">{{$mode->mode}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <div class="form-group input-group ml">
                                <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                </div>
                                <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" data-value="{{ Carbon\Carbon::now()->subMonth(12) }}" placeholder="Search Date (From)">
                            </div>
                        </div>
                        <div class="col-3 ">
                            <div class="form-group input-group ml">
                                <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                </div>
                                <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" data-value="{{ Carbon\Carbon::today() }}" placeholder="Search Date (To)">
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>

                <div class="col justify-content-end mb-3">
                    <div class="card-header">
                        <div class="heading-elements">
                            <ul class="list-inline">
                                <li class="primary border-primary round" value="0" id="star_shippers_filter"><a>
                                        Star Shippers</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Attempt Count</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Return City</th>
                        <th class="border-primary border-darken-1">Area</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Sub Station</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Rider Trax Id</th>
                        <th class="border-primary border-darken-1">Rider Name</th>
                        <th class="border-primary border-darken-1">Return Pending for</th>
                        <th class="border-primary border-darken-1">Return Confirmed By</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Status Date</th>
                        <th class="border-primary border-darken-1">Received By</th>

                        <th class="border-primary border-darken-1"></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <div class="modal fade" id="excel_upload_modal" data-backdrop="static" role="dialog" aria-labelledby="excel_upload_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="excel_upload_modal_title">Upload Excel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="return_status_form" class="form-horizontal" method="POST" action="{{ route('admin.return.confirmed.excel.store') }}" novalidate="novalidate" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row align-items-center justify-content-center">
                            <div class="col">
                                <div class="form-group">
                                    <input type="file" name="shipments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group text-left">
                                    <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                </div>
                            </div>

                            <div class="col ml-auto">
                                <div class="form-group text-right">
                                    <a href="{{ asset('file/Trax Revert Status Template.xlsx') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Enter Remarks Modal --}}
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
                            <input type="text" name="add_remarks" id="add_remarks" class="form-control add_remarks" placeholder="Remarks" data-rule-required="true" data-msg-required="Remarks is required">

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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
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
        .selectize-control {
            width: 300px !important;
        }
        .goldClass{
            background-color: gold;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        var selected_rows = [];
        var restricted_rows = [];
        $(document).ready(function () {

            $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
                    }
                }
            });


            $('#search_shipping_mode').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Shipping Mode',
                allowClear:true
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.confirmed.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Shipper Name');
                            head.push('Phone');
                            head.push('Address');
                            head.push('Attempt Count');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Return City');
                            head.push('Area');
                            head.push('Consignee Name');
                            head.push('Consignee Sub Station');
                            head.push('Collection Amount');
                            head.push('Shipping Mode');
                            head.push('Service Type');
                            head.push('Status');
                            head.push('Rider Trax Id');
                            head.push('Rider Name');
                            head.push('Return Pending For');
                            head.push('Return Confirmed By');
                            head.push('Reason');
                            head.push('Remarks');
                            head.push('Arrival Date');
                            head.push('Status Date');
                            head.push('Received By');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking);
                                row.push(values.order_id);
                                row.push(values.shipper);
                                row.push(values.shipper_phone); // to be changed
                                row.push(values.shipper_return_address); // to be changed
                                row.push(values.total_attempt);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.return_city);
                                row.push(values.area);
                                row.push(values.consignee_name);
                                row.push(values.sub_station);
                                row.push(values.amount);
                                row.push(values.mode);
                                row.push(values.service_type);
                                row.push(values.status);
                                row.push(values.rider_id);
                                row.push(values.rider_name);
                                row.push(values.return_pending_for);
                                row.push(values.return_confirmed_by);
                                row.push(values.reason);
                                row.push(values.remarks);
                                row.push(values.arrival);
                                row.push(values.last_status_date);
                                row.push(values.receiver_name);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var shipment_remarks = {};
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                        @if ( session('role_id') == 1 || in_array(109, session('permissions')) )
                    {
                        text: 'Revert',
                        className: 'btn btn-primary revert',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            if(selected_rows !== '' && restricted_rows.length == 0){

                                $('#add_remarks_modal').modal('show');
                                $('#add_remarks_modal').on('hide.bs.modal', function () {
                                    $('#add_remarks_form input.add_remarks').val('');
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
                                        var remarks = $('#add_remarks').val();

                                        swal({
                                            title: 'Are You Sure?',
                                            text: 'Are you sure, you want to revert this Shipment?',
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
                                                blockPagePermanently();
                                                table.rows().nodes().each(function(index) {
                                                    var row = table.row(index);

                                                    if ($(row.node()).hasClass('selected')) {
                                                        var id = parseInt(row.id());
                                                        shipment_remarks[id] = remarks;
                                                    }
                                                });
                                                //alert(selected_rows);
                                                $.ajax({
                                                    url:"{{route('admin.return.confirmed.revert.status')}}",
                                                    method:'POST',
                                                    data:{
                                                        'shipment_ids':selected_rows,
                                                        '_token':'{{ csrf_token() }}',
                                                        'action': 'revert',
                                                        'remark': shipment_remarks
                                                    }
                                                })
                                                    .done(function (data) {
                                                        UnblockPagePermanently();
                                                        $('#add_remarks_modal').modal('hide');
                                                        table.draw(false);

                                                        if (data.status == 0) {
                                                            toastr.success(data.success, 'Success!', {
                                                                positionClass: 'toast-bottom-center',
                                                                containerId: 'toast-bottom-center'
                                                            });
                                                        }
                                                        else {
                                                            toastr.error(data.error, 'Error!', {
                                                                positionClass: 'toast-top-center',
                                                                containerId: 'toast-top-center'
                                                            });
                                                        }
                                                        table.button('.revert').disable();
                                                    });
                                            }
                                        });

                                    }
                                });

                            }else{
                                var error = "Not selected any shipments!";
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }
                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'Return Confirmed',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },{
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                    id = parseInt(row.id());

                                    var assigned_agent_id = row.data().assigned_agent_id;
                                    var tat = row.data().confirmation_on;

                                    hub_id = $(row.node()).data('hub');

                                    var allow = false;

                                    if(hub_ids.length == 0) {
                                        hub_ids.push(hub_id);

                                        allow = true;
                                    }
                                    else if(hub_ids[0] == hub_id) {
                                        allow = true;
                                    }

                                    if (allow) {
                                        row.select();

                                        var index = $.inArray(id, selected_rows);

                                        if (index === -1) {
                                            selected_rows.push(id);
                                        }

                                        if(restricted_rows.length == 0)
                                        {
                                            table.button('.revert').enable();
                                        }
                                    }
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

                                    var restricted_index = $.inArray(id,restricted_rows);

                                    if(restricted_index !== -1)
                                    {
                                        restricted_rows.splice(restricted_index,1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.revert').disable();

                                        hub_ids.splice(index, 1);
                                    }
                                }
                            });
                        }
                    },
                    {
                        title: 'Upload',
                        className: 'btn btn-primary excel-upload',
                        text: '<i class="la la-file-excel-o"></i> Upload',
                        action : function(e) {
                            $('#excel_upload_modal').modal('show');
                        }
                    },
                    'reset'
                ],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[5,50, 100, 500, 1000, -1], [5,50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                deferLoading: 0,
                ajax:{
                    url:'{{ route('admin.return.confirmed.list') }}',
                    data: function (d) {
                        d.select_type = $('#select_type').val();
                        d.search_shipping_mode = $('#search_shipping_mode').val();
                        d.tracking_numbers = $('#tracking_number').val();
                        d.star_shipper_filter = $('#star_shippers_filter').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'shId',
                order: [[26, 'desc']],
                columns: [
                    {data: 'shId', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id',defaultContent:'', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'shipper_phone', name: 'usi.phone', class: 'align-middle shipper_phone'},
                    {data: 'shipper_return_address', name: 'usi.pickup_address', class: 'align-middle shipper_return_address'},
                    {data: 'total_attempt', name: 'total_attempt', class: 'align-middle total_attempt', orderable: false},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'hub', name: 'h.name', class: 'align-middle hub'},
                    {data: 'return_city', name: 'return_city', class: 'align-middle return_city', orderable: false, searchable: false},
                    {data: 'area', name: 'ca.name', class: 'align-middle area'},
                    {data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'sub_station', name: 'dlm.area_name', class: 'align-middle sub_station',orderable: false,searchable:false},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'mode', name: 'sm.id', class: 'align-middle mode'},
                    {data: 'service_type', name: 'bt.id', class: 'align-middle service_type'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'rider_id', name: 'rider.trax_id', class: 'align-middle rider_id'},
                    {data: 'rider_name', name: 'rider.name', class: 'align-middle rider_name'},
                    {data: 'return_pending_for', name: 'return_pending_for', class: 'align-middle return_pending_for', orderable: false},
                    {data: 'return_confirmed_by', name: 'cb.name', class: 'align-middle return_confirmed_by', orderable: false},
                    {data: 'reason', name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'shipment_remarks', name: 'shipments_journey.remarks', class: 'align-middle remarks', orderable: false, searchable: false},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'status_date', name: 'shipments_journey.created_at', class: 'align-middle status_date'},
                    {data: 'receiver_name', name: 'a.name', class: 'align-middle receiver_name'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
                ],
                rowCallback: function(row, data, index) {
                    if (data.check_return_bag == 'yes') {
                        $('td:eq(0)', row).removeClass('select-checkbox');
                    }
                    var info = table.page.info();
                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.shId, selected_rows) !== -1) {
                        table.row(row).select();
                    }

                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number')|| $(header).is('.select-checkbox') || $(header).is('.action') || $(header).is('.remarks') || $(header).is('.retuen_city') || $(header).is('.sub_station')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.mode')){
                            $(mode_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.service_type')){
                            $(service_drop_select).appendTo($(search))
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

                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $shipping_mode !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.mode;
                        return obj;
                    });

                    $("#mode_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data2 = $.map({!! $service_type !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.booking_type;
                        return obj;
                    });

                    $("#service_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Service",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            var hub_ids = [];
            $('#return_status_form').validate({
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
                        text: 'Transaction(s) are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));
                var con_id = parseInt($(this).parent('tr').attr('tracking_number'));
                var hub_id = $(this).parents('tr').data('hub');

                if(hub_ids.length == 0){
                    hub_ids.push(hub_id);
                    var index = $.inArray(id, selected_rows);

                    if (index === -1) {
                        selected_rows.push(id);
                    }
                    else {
                        selected_rows.splice(index, 1);
                    }

                    if (selected_rows.length > 0) {
                        if(restricted_rows.length == 0)
                        {
                            table.button('.revert').enable();
                        }
                        else{
                            table.button('.revert').disable();
                        }
                    }
                    else {
                        table.button('.revert').disable();
                    }
                }else{
                    if(hub_ids[0] == hub_id){
                        var index = $.inArray(id, selected_rows);

                        if (index === -1) {
                            selected_rows.push(id);
                        }
                        else {
                            selected_rows.splice(index, 1);
                        }

                        if (selected_rows.length > 0) {
                            if(restricted_rows.length == 0)
                            {
                                table.button('.revert').enable();
                            }
                            else{
                                table.button('.revert').disable();
                            }
                        }
                        else {
                            table.button('.revert').disable();
                        }
                    }else{
                        var error = "Selected hubs should be the same!";
                        toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        return false;
                    }

                }


            });
            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('revert')) {
                    swal({
                        text: 'Are you sure, you want to revert this Shipment?',
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
                    }).then(function(confirm) {
                        if (confirm) {
                            if(id) {
                                var remark = $.trim($('tr#' + id).find('td.remarks input').val());
                                $.ajax({
                                    url: '{!! route('admin.return.confirmed.revert') !!}',
                                    method: 'POST',
                                    data: {
                                        '_token': '{{ csrf_token() }}',
                                        'id': id,
                                        'remarks':remark
                                    }
                                })
                                    .done(function (data) {
                                        table.draw(false);

                                        if (data.status == 0) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                        else {
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                    });
                            }
                        }
                    });
                }
            });



            //Selectize
            var select = $('#tracking_number').selectize({
                placeholder: 'Tracking Number(s)',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                    else {
                        return false;
                    }
                },
            });

            $('#track_form').bind('submit',function (e) {

                var tracking_numbers = $('#track_form .tracking_numbers').val();
                var search_shipping_mode = $('#track_form #search_shipping_mode').val();

                if (tracking_numbers != '' || search_shipping_mode != '') {
                    table.draw();
                }else{
                    alert('Fill Tracking First');
                }

                e.preventDefault();

            });

            $('#star_shippers_filter').on('click',function () {
                $('#star_shippers_filter').val(1);
                table.draw(true);
                $('#star_shippers_filter').val(0);
            });

        });
    </script>
@endsection