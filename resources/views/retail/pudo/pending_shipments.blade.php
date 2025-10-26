@extends('retail.layout.master')
@section('title','Pending Shipments')

@section('content')
    <h1 class="mb-1">
        Pending Shipments
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('retail.inc.messages')

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
                    <div class="col-4 ">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Search By Tracking Number"
                                   name="search_tracking" id="search_tracking">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Order ID</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Shipper Phone</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Service Type</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="DeliveredModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DeliveredModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Delivered Confirmation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="delivered_form" method="POST">
                        @csrf
                        <input type="hidden" name="shipment_id" id="shipment_id">

                        <div class="row">
                            <div class="col-6 form-group">
                                <label>Consignee Name</label>
                                <input type="text" name="consignee_name" id="consignee_name" class="form-control" required>
                            </div>

                            <div class="col-6 form-group">
                                <label>CNIC Number</label>
                                <input type="text" name="cnic" id="cnic" maxlength="15" class="form-control" placeholder="XXXXX-XXXXXXX-X" required>
                            </div>

                            <div class="col-6 form-group">
                                <label>Relation</label>
                                <select name="relation" id="relation_id" class="form-control select2" required style="width:100%">
                                    <option value="">Select Relation</option>
                                    @foreach($delivery_relation as $relation)
                                        <option value="{{ $relation->name }}">{{ $relation->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 form-group">
                                <label>COD Value</label>
                                <input type="text" name="cod_value" id="cod_value" class="form-control" readonly>
                            </div>

                            <div class="col-6 form-group">
                                <label>OTP</label>
                                <input type="text" name="otp" id="otp" class="form-control" required>
                            </div>

                            <div class="col-6 form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-2">
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block">Confirm Delivered</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="returnToShipperModal" tabindex="-1" role="dialog" aria-labelledby="returnToShipperModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="returnToShipperForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="returnToShipperModalLabel">Return Shipment to Shipper</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="shipment_id" id="return_shipment_id">

                        <div class="form-group">
                            <label>Receive By <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="receive_by" id="receive_by" required>
                        </div>

                        <div class="form-group">
                            <label>OTP <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="otp" id="otp" required>
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Return</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $('#relation_id').select2({
                placeholder: "Select Relation",
                width: 'resolve',
                dropdownParent: $('#DeliveredModal') // important for modals
            });


            $('#search_tracking').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function () {
                if (this.value.length == 0 || this.value.length >= 6) {
                    table.draw();
                }
            });


            // function for handling special characters
            function parseHtmlEntities(data, keys) {
                let parser = new DOMParser();
                return data.map(item => {
                    keys.forEach(key => {
                        if (item[key]) {
                            item[key] = parser.parseFromString(item[key], "text/html").documentElement.textContent;
                        }
                    });
                    return item;
                });
            }

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
                    var jsonResult = $.ajax({
                        url: '{{ route('retail.last_mile.pending_shipment.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Order ID');
                            head.push('Shipper');
                            head.push('Shipper Phone');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Hub');
                            head.push('Consignee Name');
                            head.push('Consignee Phone');
                            head.push('Address');
                            head.push('Collection Amount');
                            head.push('Shipping Mode');
                            head.push('Service Type');
                            head.push('Status');
                            // head.push('Consolidation');
                            // head.push('Consolidation ID');

                            $.each(result.data, function(index, values) {

                                // Specify the keys you want to parse for HTML entities
                                let keysToParse = [
                                    'tracking',
                                    'order_id',
                                    'shipper',
                                    'shipper_phone1',
                                    'shipper_phone2',
                                    'origin',
                                    'destination',
                                    'hub',
                                    'consignee_name',
                                    'consignee_phone_number_1',
                                    'consignee_phone_number_2',
                                    'consignee_address',
                                    'amount',
                                    'mode',
                                    'service_type',
                                    'status'
                                ];

                                // Parse the values for these keys
                                values = parseHtmlEntities([values], keysToParse)[0];

                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.order_id);
                                row.push(values.shipper);
                                row.push(values.shipper_phone1 + ' | ' + (values.shipper_phone2 ?? ''));
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone_number_1 + ' | ' + (values.consignee_phone_number_2 ?? ''));
                                row.push(values.consignee_address);
                                row.push(values.amount);
                                row.push(values.mode);
                                row.push(values.service_type);
                                row.push(values.status);

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
                        extend: 'excel',
                        title: 'Return Confirmation Pending',
                        className: 'btn btn-primary',
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
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('retail.last_mile.pending_shipment.list') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'shId',
                order: [[0, 'desc']],
                columns: [
                    { data: 'id', defaultContent: '', orderable: false, searchable: false, class: 'align-middle serial_number' },
                    { data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number' },
                    { data: 'order_id', name: 'shipments.order_id', class: 'align-middle order_id' },
                    { data: 'shipper', name: 'u.name', class: 'align-middle shipper' },
                    { data: 'shipper_phone1', name: 'u.phone', class: 'align-middle shipper_phone1' },
                    { data: 'origin', name: 'oc.name', class: 'align-middle origin' },
                    { data: 'destination', name: 'dc.name', class: 'align-middle destination' },
                    { data: 'hub', name: 'h.name', class: 'align-middle hub' },
                    { data: 'consignee_name', name: 'shipments.consignee_name', class: 'align-middle consignee_name' },
                    { data: 'consignee_phone', name: 'shipments.consignee_phone_number_1', class: 'align-middle consignee_phone' },
                    { data: 'consignee_address', name: 'shipments.consignee_address', class: 'align-middle consignee_address' },
                    { data: 'amount', name: 'shipments.amount', class: 'align-middle amount' },
                    { data: 'mode', name: 'sm.mode', class: 'align-middle mode' },
                    { data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type' },
                    { data: 'status', name: 'ss.name', class: 'align-middle status' },
                    { data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false }

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
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

                        if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
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

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            $(document).on('click', '.btn-delivered', function () {
                let shipmentId = $(this).data('id');
                let codValue = $(this).data('cod');
                $('#shipment_id').val(shipmentId);
                $('#cod_value').val(codValue);
                $('#DeliveredModal').modal('show');
            });

            $('#delivered_form').on('submit', function (e) {
                e.preventDefault();

                // Validate required fields
                let isValid = true;
                $('#delivered_form [required]').each(function () {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                if (!isValid) {
                    toastr.error('Please fill all required fields.');
                    return;
                }

                // Confirm swal (SweetAlert v1)
                swal({
                    title: 'Are You Sure?',
                    text: 'You are about to mark this shipment as delivered',
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
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $.ajax({
                            url: "{{ route('retail.last_mile.shipment.delivered') }}",
                            type: "POST",
                            data: $('#delivered_form').serialize(),
                            beforeSend: function () {
                                swal({
                                    title: 'Processing...',
                                    text: 'Please wait...',
                                    icon: 'info',
                                    buttons: false,
                                    closeOnClickOutside: false,
                                    closeOnEsc: false
                                });
                            },
                            success: function (res) {
                                swal.close();
                                if (res.status === 1) {
                                    toastr.success(res.message || 'Shipment marked as delivered.');
                                    $('#DeliveredModal').modal('hide');
                                    $('#delivered_form')[0].reset();
                                    $('#datatable').DataTable().ajax.reload(null, false);
                                } else {
                                    toastr.error(res.message || 'Error processing request.');
                                }
                            },
                            error: function () {
                                swal.close();
                                toastr.error('Something went wrong. Please try again.');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.return-to-shipper-btn', function() {
                const shipmentId = $(this).data('id');
                $('#return_shipment_id').val(shipmentId);
                $('#returnToShipperModal').modal('show');
            });

            $('#returnToShipperForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();

                swal({
                    title: "Are you sure?",
                    text: "Do you really want to return this shipment to the shipper?",
                    icon: "warning",
                    buttons: {
                        cancel: "Cancel",
                        confirm: {
                            text: "Yes, Return it!",
                            value: true,
                            closeModal: false
                        }
                    },
                    dangerMode: true,
                }).then((willReturn) => {
                    if (!willReturn) return;

                    swal({
                        title: 'Please wait...',
                        text: 'Processing shipment return...',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        content: {
                            element: "div",
                            attributes: {
                                innerHTML: '<i class="fa fa-spinner fa-spin fa-2x"></i>',
                            },
                        },
                    });

                    $.ajax({
                        url: "{{ route('retail.last_mile.shipment.return') }}",
                        type: "POST",
                        data: formData,
                        success: function (response) {
                            if (response.status === 1) {
                                swal("Success!", response.message, "success").then(() => {
                                    $('#returnToShipperModal').modal('hide');
                                    $('#returnToShipperForm')[0].reset();
                                    $('#datatable').DataTable().ajax.reload(null, false);
                                });
                            } else {
                                swal("Error!", response.message, "error");
                            }
                        },
                        error: function () {
                            swal("Error!", "Something went wrong! Please try again.", "error");
                        }
                    });
                });
            });




        {{--$('#delivered_form').on('submit', function (e) {--}}
            {{--    e.preventDefault();--}}

            {{--    // validate required fields--}}
            {{--    let isValid = true;--}}
            {{--    $('#delivered_form [required]').each(function () {--}}
            {{--        if (!$(this).val()) {--}}
            {{--            isValid = false;--}}
            {{--            $(this).addClass('is-invalid');--}}
            {{--        } else {--}}
            {{--            $(this).removeClass('is-invalid');--}}
            {{--        }--}}
            {{--    });--}}
            {{--    if (!isValid) {--}}
            {{--        toastr.error('Please fill all required fields.');--}}
            {{--        return;--}}
            {{--    }--}}

            {{--    // confirm swal--}}
            {{--    swal({--}}
            {{--        title: 'Are You Sure?',--}}
            {{--        text: 'You are about to mark this shipment as delivered',--}}
            {{--        icon: 'warning',--}}
            {{--        buttons: {--}}
            {{--            cancel: {--}}
            {{--                text: 'No',--}}
            {{--                value: null,--}}
            {{--                visible: true,--}}
            {{--                closeModal: true,--}}
            {{--            },--}}
            {{--            confirm: {--}}
            {{--                text: 'Yes',--}}
            {{--                value: true,--}}
            {{--                visible: true,--}}
            {{--                closeModal: true--}}
            {{--            }--}}
            {{--        },--}}
            {{--        closeOnClickOutside: false,--}}
            {{--        closeOnEsc: false,--}}
            {{--        dangerMode: true--}}
            {{--    }).then(function (confirm){--}}
            {{--        if (confirm) {--}}
            {{--            $.ajax({--}}
            {{--                url: '{{ route("retail.last_mile.shipment.delivered") }}',--}}
            {{--                type: 'POST',--}}
            {{--                data: $('#delivered_form').serialize(),--}}
            {{--                success: function (res) {--}}
            {{--                    if (res.status === 1) {--}}
            {{--                        toastr.success(res.message || 'Shipment marked as delivered.');--}}
            {{--                        $('#DeliveredModal').modal('hide');--}}
            {{--                        $('#datatable').DataTable().ajax.reload(null, false);--}}
            {{--                    } else {--}}
            {{--                        toastr.error(res.message || 'Error processing request.');--}}
            {{--                    }--}}
            {{--                },--}}
            {{--                error: function (xhr) {--}}
            {{--                    toastr.error('Something went wrong. Please try again.');--}}
            {{--                }--}}
            {{--            });--}}
            {{--        }--}}
            {{--    });--}}

            {{--});--}}

            var hub_ids = [];

            {{--$('#submit_nsa').on('click', function () {--}}
            {{--    var new_selected_rows = selected_rows;--}}
            {{--    var nsa = null;--}}
            {{--    selected_rows_nsa.forEach(function (index) {--}}
            {{--        if($('input[name="confirm['+index+']"]').prop('checked') === false){--}}
            {{--            var key = $.inArray(index, new_selected_rows);--}}
            {{--            new_selected_rows.splice(key, 1);--}}
            {{--        }--}}
            {{--    });--}}
            {{--    table.rows().nodes().each(function (index) {--}}
            {{--        var row = table.row(index);--}}
            {{--        if ($(row.node()).hasClass('selected')) {--}}
            {{--            var id = parseInt(row.id());--}}
            {{--            var remarks = $(row.node()).find('td.shipment_remarks textarea').val();--}}
            {{--            shipment_remarks[id] = remarks;--}}
            {{--        }--}}

            {{--    });--}}
            {{--    $.ajax({--}}
            {{--        url: "{{route('cod.return.pending.reattempt.status')}}",--}}
            {{--        method: 'POST',--}}
            {{--        data: {--}}
            {{--            'shipment_ids': new_selected_rows,--}}
            {{--            '_token': '{{ csrf_token() }}',--}}
            {{--            'remark': shipment_remarks--}}
            {{--        }--}}
            {{--    }).done(function (data) {--}}
            {{--        table.rows().deselect();--}}
            {{--        selected_rows = [];--}}
            {{--        new_selected_rows = [];--}}
            {{--        shipment_remarks = {};--}}
            {{--        table.button('.reattempt').disable();--}}
            {{--        table.draw('false');--}}
            {{--        if (data.not_updated_shipments.length > 0) {--}}
            {{--            var alert_icon = 'warning';--}}
            {{--            var html = '';--}}
            {{--            if (data.updated_shipments.length > 0) {--}}
            {{--                alert_icon = 'success';--}}
            {{--                $.each(data.updated_shipments, function (index, tracking_number) {--}}
            {{--                    html += tracking_number + '<br/>';--}}
            {{--                });--}}
            {{--                html += '<br/>Shipment(s) has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!</br><hr>';--}}

            {{--            }--}}
            {{--            $.each(data.not_updated_shipments, function (index, tracking_number) {--}}
            {{--                html += tracking_number + '<br/>';--}}
            {{--            });--}}
            {{--            html += '<br/>Shipment(s) are already updated';--}}
            {{--            content = document.createElement('div');--}}
            {{--            content.innerHTML = html;--}}
            {{--            swal({--}}
            {{--                title: 'Shipments Re-Attempt Requested',--}}
            {{--                content: content,--}}
            {{--                icon: alert_icon,--}}
            {{--                buttons: {--}}
            {{--                    cancel: {--}}
            {{--                        text: 'Close',--}}
            {{--                        value: null,--}}
            {{--                        visible: true,--}}
            {{--                        closeModal: true,--}}
            {{--                    }--}}
            {{--                },--}}
            {{--                closeOnClickOutside: true,--}}
            {{--                closeOnEsc: false,--}}
            {{--                dangerMode: true--}}
            {{--            });--}}
            {{--        } else {--}}
            {{--            toastr.success(data.success, 'Success!', {--}}
            {{--                positionClass: 'toast-bottom-center',--}}
            {{--                containerId: 'toast-bottom-center'--}}
            {{--            });--}}

            {{--        }--}}


            {{--    });--}}
            {{--});--}}

            {{--$('#datatable tbody').on('click', 'tr td.select-checkbox', function() {--}}

            {{--    var id = parseInt($(this).parent('tr').attr('id'));--}}
            {{--    var hub_id = $(this).parents('tr').data('hub');--}}
            {{--    var con_id = parseInt($(this).parent('tr').attr('consolidation_id'));--}}
            {{--    if(con_id){--}}
            {{--        if(hub_ids.length == 0){--}}
            {{--            hub_ids.push(hub_id);--}}
            {{--        }else if(hub_ids[0] != hub_id){--}}
            {{--            var error = "Selected hubs should be the same!";--}}
            {{--            toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--            return false;--}}
            {{--        }--}}
            {{--        table.rows().nodes().each(function(index) {--}}
            {{--            var row = table.row(index);--}}
            {{--            if ($(row.node()).attr('consolidation_id') == con_id) {--}}
            {{--                var rid = parseInt($(row.node()).attr('id'));--}}
            {{--                var rindex = $.inArray(rid, selected_rows);--}}

            {{--                if (rindex === -1) {--}}
            {{--                    selected_rows.push(rid);--}}
            {{--                    if(id != rid){--}}

            {{--                        table.row(row).select();--}}
            {{--                    }--}}
            {{--                }--}}
            {{--                else {--}}
            {{--                    if(id != rid){--}}

            {{--                        row.deselect();--}}
            {{--                    }--}}
            {{--                    selected_rows.splice(rindex, 1);--}}
            {{--                }--}}
            {{--                if (selected_rows.length > 0) {--}}
            {{--                    table.button('.confirm').enable();--}}
            {{--                    table.button('.reattempt').enable();--}}
            {{--                }--}}
            {{--                else {--}}
            {{--                    table.button('.confirm').disable();--}}
            {{--                    table.button('.reattempt').disable();--}}
            {{--                }--}}
            {{--            }--}}
            {{--        });--}}
            {{--    }else{--}}
            {{--        if(hub_ids.length == 0){--}}
            {{--            hub_ids.push(hub_id);--}}
            {{--            var index = $.inArray(id, selected_rows);--}}

            {{--            if (index === -1) {--}}
            {{--                selected_rows.push(id);--}}
            {{--            }--}}
            {{--            else {--}}
            {{--                selected_rows.splice(index, 1);--}}
            {{--            }--}}

            {{--            if (selected_rows.length > 0) {--}}
            {{--                table.button('.confirm').enable();--}}
            {{--                table.button('.reattempt').enable();--}}
            {{--            }--}}
            {{--            else {--}}
            {{--                table.button('.confirm').disable();--}}
            {{--                table.button('.reattempt').disable();--}}
            {{--            }--}}
            {{--        }else{--}}
            {{--            if(hub_ids[0] == hub_id){--}}
            {{--                var index = $.inArray(id, selected_rows);--}}

            {{--                if (index === -1) {--}}
            {{--                    selected_rows.push(id);--}}
            {{--                }--}}
            {{--                else {--}}
            {{--                    selected_rows.splice(index, 1);--}}
            {{--                }--}}

            {{--                if (selected_rows.length > 0) {--}}
            {{--                    table.button('.confirm').enable();--}}
            {{--                    table.button('.reattempt').enable();--}}
            {{--                }--}}
            {{--                else {--}}
            {{--                    table.button('.confirm').disable();--}}
            {{--                    table.button('.reattempt').disable();--}}
            {{--                }--}}
            {{--            }else{--}}
            {{--                var error = "Selected hubs should be the same!";--}}
            {{--                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--                return false;--}}
            {{--            }--}}

            {{--        }--}}
            {{--    }--}}

            {{--});--}}

            {{--$('body').on('click','.returnMarkStatus',function () {--}}
            {{--    var row_id = $(this).parents('tr').attr('id');--}}
            {{--    var remark = $(this).parents('tr').find('td.shipment_remarks textarea').val();--}}


            {{--    if(row_id != ''){--}}
            {{--        swal({--}}
            {{--            title: 'Are You Sure?',--}}
            {{--            text: 'Select Yes to change shipment status to Return-Confirm!',--}}
            {{--            icon: 'warning',--}}
            {{--            buttons: {--}}
            {{--                cancel: {--}}
            {{--                    text: 'No',--}}
            {{--                    value: null,--}}
            {{--                    visible: true,--}}
            {{--                    closeModal: true,--}}
            {{--                },--}}
            {{--                confirm: {--}}
            {{--                    text: 'Yes',--}}
            {{--                    value: true,--}}
            {{--                    visible: true,--}}
            {{--                    closeModal: true--}}
            {{--                }--}}
            {{--            },--}}
            {{--            closeOnClickOutside: false,--}}
            {{--            closeOnEsc: false,--}}
            {{--            dangerMode: true--}}
            {{--        }).then(function (confirm) {--}}
            {{--            if (confirm) {--}}
            {{--                $.ajax({--}}
            {{--                    url:"{{route('cod.return.pending.marked.status')}}",--}}
            {{--                    method:'POST',--}}
            {{--                    data:{--}}
            {{--                        'shipment_id':row_id,--}}
            {{--                        '_token':'{{ csrf_token() }}',--}}
            {{--                        'remark':remark--}}
            {{--                    }--}}
            {{--                }).done(function (data) {--}}
            {{--                    if(data.status == 1){--}}
            {{--                        table.draw('false');--}}
            {{--                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

            {{--                    }else{--}}
            {{--                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

            {{--                    }--}}

            {{--                });--}}
            {{--            }--}}
            {{--        });--}}


            {{--    }--}}
            {{--});--}}
            {{--$('body').on('click','.returnReattemptStatus',function () {--}}
            {{--    var row_id = $(this).parents('tr').attr('id');--}}
            {{--    var remark = $(this).parents('tr').find('td.shipment_remarks textarea').val();--}}

            {{--    if(row_id != ''){--}}
            {{--        swal({--}}
            {{--            title: 'Are You Sure?',--}}
            {{--            text: 'Select Yes to request shipment for Re-Attempt!',--}}
            {{--            icon: 'warning',--}}
            {{--            buttons: {--}}
            {{--                cancel: {--}}
            {{--                    text: 'No',--}}
            {{--                    value: null,--}}
            {{--                    visible: true,--}}
            {{--                    closeModal: true,--}}
            {{--                },--}}
            {{--                confirm: {--}}
            {{--                    text: 'Yes',--}}
            {{--                    value: true,--}}
            {{--                    visible: true,--}}
            {{--                    closeModal: true--}}
            {{--                }--}}
            {{--            },--}}
            {{--            closeOnClickOutside: false,--}}
            {{--            closeOnEsc: false,--}}
            {{--            dangerMode: true--}}
            {{--        }).then(function (confirm) {--}}
            {{--            if (confirm) {--}}
            {{--                $.ajax({--}}
            {{--                    url: "{{route('retail.return.pending_reattempt_nsa')}}",--}}
            {{--                    method: 'POST',--}}
            {{--                    data: {--}}
            {{--                        'shipment_ids': row_id,--}}
            {{--                        '_token': '{{ csrf_token() }}',--}}
            {{--                        'single' : 1--}}
            {{--                    }--}}
            {{--                }).done(function (data) {--}}
            {{--                    if(data.status == 1){--}}
            {{--                        var html = '';--}}
            {{--                        html += 'Out of Service Area / Non Service Area Shipment against Tracking Number: '+ data.nsa_shipment +'<br/><br/>';--}}
            {{--                        html += '<b>Estimated Charges: '+ data.estimated_charge +'</b><br/><br/>';--}}
            {{--                        html += 'Are you sure you want to deliver shipment with additional charges?';--}}
            {{--                        content = document.createElement('div');--}}
            {{--                        content.innerHTML = html;--}}
            {{--                        swal({--}}
            {{--                            content: content,--}}
            {{--                            icon: 'warning',--}}
            {{--                            buttons: {--}}
            {{--                                cancel: {--}}
            {{--                                    text: 'No',--}}
            {{--                                    value: null,--}}
            {{--                                    visible: true,--}}
            {{--                                    closeModal: true,--}}
            {{--                                },--}}
            {{--                                confirm: {--}}
            {{--                                    text: 'Yes',--}}
            {{--                                    value: true,--}}
            {{--                                    visible: true,--}}
            {{--                                    closeModal: true--}}
            {{--                                }--}}
            {{--                            },--}}
            {{--                            closeOnClickOutside: false,--}}
            {{--                            closeOnEsc: false,--}}
            {{--                            dangerMode: true--}}
            {{--                        }).then(function (confirm) {--}}
            {{--                            if(confirm){--}}
            {{--                                $.ajax({--}}
            {{--                                    url:"{{route('retail.return.mark_reattempt')}}",--}}
            {{--                                    method:'POST',--}}
            {{--                                    data:{--}}
            {{--                                        'shipment_id':row_id,--}}
            {{--                                        '_token':'{{ csrf_token() }}',--}}
            {{--                                        'remark':remark--}}
            {{--                                    }--}}
            {{--                                }).done(function (data) {--}}
            {{--                                    if(data.status == 1){--}}
            {{--                                        table.draw('false');--}}
            {{--                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

            {{--                                    }else{--}}
            {{--                                        table.draw('false');--}}
            {{--                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

            {{--                                    }--}}

            {{--                                });--}}
            {{--                            }--}}
            {{--                        })--}}
            {{--                    }--}}
            {{--                    else{--}}
            {{--                        $.ajax({--}}
            {{--                            url:"{{route('retail.return.mark_reattempt')}}",--}}
            {{--                            method:'POST',--}}
            {{--                            data:{--}}
            {{--                                'shipment_id':row_id,--}}
            {{--                                '_token':'{{ csrf_token() }}',--}}
            {{--                                'remark':remark--}}
            {{--                            }--}}
            {{--                        }).done(function (data) {--}}
            {{--                            if(data.status == 1){--}}
            {{--                                table.draw('false');--}}
            {{--                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}

            {{--                            }else{--}}
            {{--                                table.draw('false');--}}
            {{--                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}

            {{--                            }--}}

            {{--                        });--}}
            {{--                    }--}}
            {{--                });--}}
            {{--            }--}}
            {{--            --}}{{--if (confirm) {--}}

            {{--            --}}{{--}--}}
            {{--        });--}}


            {{--    }--}}
            {{--});--}}


            {{--$('body').on('click','.intercept',function () {--}}
            {{--    var row_id = $(this).parents('tr').attr('id');--}}

            {{--    if(row_id != ''){--}}
            {{--        var redirect = '{!! route('cod.intercept.index', ':id') !!}';--}}
            {{--        var url = redirect.replace(':id', row_id);--}}
            {{--        window.open(url);--}}
            {{--    }--}}
            {{--});--}}

            {{--$('#datatable').on('click', '.selfCollection', function () {--}}
            {{--    var row_id = $(this).parents('tr').attr('id');--}}
            {{--    var remark = $.trim($('tr#' + row_id).find('td.shipment_remarks textarea').val());--}}
            {{--    if(row_id){--}}
            {{--        swal({--}}
            {{--            text: 'Are you sure you want to mark shipment for Self-Collection?',--}}
            {{--            icon: 'info',--}}
            {{--            buttons: {--}}
            {{--                cancel: {--}}
            {{--                    text: 'No',--}}
            {{--                    value: null,--}}
            {{--                    visible: true,--}}
            {{--                    closeModal: true,--}}
            {{--                },--}}
            {{--                confirm: {--}}
            {{--                    text: 'Yes',--}}
            {{--                    value: true,--}}
            {{--                    visible: true,--}}
            {{--                    closeModal: true--}}
            {{--                }--}}
            {{--            },--}}
            {{--            closeOnClickOutside: false,--}}
            {{--            closeOnEsc: false,--}}
            {{--            dangerMode: true--}}
            {{--        }).then(function (confirm) {--}}
            {{--            if (confirm) {--}}
            {{--                blockPagePermanently();--}}
            {{--                $.ajax({--}}
            {{--                    url:"{{route('cod.return.pending.marked.self_collection')}}",--}}
            {{--                    method:'POST',--}}
            {{--                    data:{--}}
            {{--                        'shipment_id':row_id,--}}
            {{--                        'remark': remark,--}}
            {{--                        '_token':'{{ csrf_token() }}',--}}
            {{--                    }--}}
            {{--                }).done(function (data) {--}}
            {{--                    UnblockPagePermanently();--}}
            {{--                    if(data.status == 1){--}}
            {{--                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--                    }else{--}}
            {{--                        table.draw(false);--}}
            {{--                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
            {{--                    }--}}
            {{--                });--}}
            {{--            }--}}
            {{--        });--}}
            {{--    }--}}
            {{--});--}}
            {{--$('body').on('click', 'button.consignee_info_label', function () {--}}
            {{--    var phone = $(this).attr('rel');--}}
            {{--    if(phone){--}}
            {{--        $.ajax({--}}
            {{--            url: '{!! route('cod.return.pending.consignee') !!}',--}}
            {{--            method: 'POST',--}}
            {{--            data: {--}}
            {{--                '_token': '{{ csrf_token() }}',--}}
            {{--                'phone': phone--}}
            {{--            }--}}
            {{--        })--}}
            {{--            .done(function(data) {--}}
            {{--                if (data.status == 0) {--}}
            {{--                    details = data.details;--}}
            {{--                    $('#consignee_information_id').val(details.consignee.id);--}}
            {{--                    var html = '<div class="row mb-1">';--}}

            {{--                    html += '<div class="col-4">Consignee Name :</div><div class="col-8">'+ details.consignee.name +'</div>';--}}
            {{--                    html += '<div class="col-4">Consignee Phone Number 1 :</div><div class="col-8">'+ details.consignee.phone +'</div>';--}}
            {{--                    var consignee_phone = '';--}}
            {{--                    if(details.consignee.phone2 != null){--}}
            {{--                        consignee_phone = details.consignee.phone2;--}}
            {{--                    }--}}
            {{--                    html += '<div class="col-4">Consignee Phone Number 2 :</div><div class="col-8">'+ consignee_phone +'</div>';--}}
            {{--                    html += '<div class="col-4">Consignee Address :</div><div class="col-8">'+ details.consignee.address +'</div>';--}}
            {{--                    html += '<div class="col-4">Consignee City :</div><div class="col-8">'+ details.consignee.city +'</div>';--}}

            {{--                    html += '</div>';--}}
            {{--                    if ('blacklist' in details) {--}}
            {{--                        html += '<div class="row p-1" style="background-color: '+ details.blacklist.color +'; color:white;">';--}}
            {{--                        html += '<div class="col-12">';--}}
            {{--                        html += '<table class="table table-sm table-bordered mb-0">';--}}
            {{--                        html += '<tbody>';--}}
            {{--                        html += '<tr>';--}}
            {{--                        html += '<td><strong>Total Shipments</strong></td>';--}}
            {{--                        html += '<td><strong>Delivered</strong></td>';--}}
            {{--                        html += '<td><strong>Ratio</strong></td>';--}}
            {{--                        html += '<td><strong>Undelivered</strong></td>';--}}
            {{--                        html += '<td><strong>Ratio</strong></td>';--}}
            {{--                        html += '<td><strong>Return Confirmed</strong></td>';--}}
            {{--                        html += '<td><strong>Ratio</strong></td>';--}}
            {{--                        html += '</tr>';--}}
            {{--                        html += '<tr>';--}}
            {{--                        html += '<td>' + details.blacklist.total_shipments + '</td>';--}}
            {{--                        html += '<td>' + details.blacklist.delivered + '</td>';--}}
            {{--                        html += '<td>' + details.blacklist.delivered_ratio + ' %</td>';--}}
            {{--                        html += '<td>' + details.blacklist.undelivered + '</td>';--}}
            {{--                        html += '<td>' + details.blacklist.undelivered_ratio + ' %</td>';--}}
            {{--                        html += '<td>' + details.blacklist.return + '</td>';--}}
            {{--                        html += '<td>Rs. ' + details.blacklist.return_ratio + ' %</td>';--}}
            {{--                        html += '</tr>';--}}
            {{--                        html += '</tbody>';--}}
            {{--                        html += '</table>';--}}
            {{--                        html += '</div></div>';--}}
            {{--                    }--}}

            {{--                    $('#consignee_info_div').html(html);--}}

            {{--                    $('#ConsigneeInformationModal').modal('show');--}}

            {{--                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});--}}
            {{--                }--}}
            {{--                else {--}}

            {{--                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});--}}
            {{--                }--}}
            {{--            });--}}
            {{--    }--}}
            {{--});--}}


        });
    </script>
@endsection