@extends('admin.layout.master')
@section('title','Receive Deliveries')

@section('content')
    <h1 class="mb-1">
        Receive Deliveries
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')


                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Scan Delivery Note Number"
                                   name="scan_delivery_note" id="scan_delivery_note">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="position-relative has-icon-left">
                            <input type="text" class="form-control" placeholder="Search By Tracking Number"
                                   name="search_tracking" id="search_tracking">
                            <div class="form-control-position">
                                <i class="ft-search"></i>
                            </div>
                        </fieldset>
                    </div>


                </div>


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Delivery Note No.</th>
                        <th class="border-primary border-darken-1">Vigilance Verification</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Business Category</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Rider Type</th>
                        <th class="border-primary border-darken-1">Rider Category</th>
                        <th class="border-primary border-darken-1">Route</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Pending Shipments</th>
                        <th class="border-primary border-darken-1">No. Of Delivered Shipments</th>
                        <th class="border-primary border-darken-1">Assigned By</th>
                        <th class="border-primary border-darken-1">Assigned Date</th>
                        <th class="border-primary border-darken-1">Total Collection</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Last Updated (Date)</th>
                        <th class="border-primary border-darken-1">Last Updated By</th>
                        <th class="border-primary border-darken-1">Created Via</th>
                        <th class="border-primary border-darken-1">Action</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <!--Shipments popup -->
    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title">Shipment(s)</h4>

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
    <!--Shipments popup -->
    <!--reassign popup -->
    <div class="modal fade" id="reassign_modal" data-backdrop="static" role="dialog" aria-labelledby="reassign_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="reassign_rider_form" class="form" nonvalidate="nonvalidate">
                <input type="hidden" id="delivery_note_id" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="reassign_modal_title">Reassign Rider</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="row justify-content-center">
                            <div class="col">
                                <fieldset class="form-group">
                                    <input type="hidden" id="rider_otp" name="rider_otp" value="{{$rider_otp}}">
                                    <select name="operation_rider_id" id="operation_rider_id"
                                            class="form-control select2" required>
                                        @foreach($operation_rider_category as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="danger" id="operation_error" style="display:none;">This field is
                                        required
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="rider" id="riders" class="form-control select2" required>

                                    </select>
                                    <div class="danger" id="rider_error" style="display:none;">This field is required
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col">
                                <fieldset class="form-group">
                                    <select name="route" id="route" class="form-control select2" required>
                                        @foreach($routes as $route)
                                            <option value="{{$route->id}}">{{$route->code}} ({{$route->start}}
                                                to {{$route->end}})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="danger" id="route_error" style="display:none;">This field is required
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="reassign_button" class="btn btn-primary" disabled>Reassign</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--reassign popup -->

    <!--otp popup -->
    <div class="modal fade" id="OtpModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="OtpModal"
         aria-hidden="true" style="top:30%;">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content col">
                <div class="modal-header text-center">
                    <div class="row align-items-center">
                        <div class="col sonic_logo align-middle text-left">
                            <img src="{{asset('img/sonic_logo_new.png')}}" alt="Sonic"
                                 class="d-inline-block mx-auto w-50">
                        </div>

                        <div class="col trax_logo align-middle text-right">
                            <img src="{{asset('img/trax_logo_new.png')}}" alt="Trax"
                                 class="d-inline-block mx-auto w-50">
                        </div>
                    </div>
                </div>
                <div class="modal-body  text-center">
                    <div class="row justify-content-center">
                        <div class="form-group form-inline">
                            <input type="text" class="form-control otp" autofocus id="otp_input"
                                   placeholder="Enter Verification Code">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button tabindex="-1" type="button" class="btn btn-primary ml-1" id="otp_submit" disabled>Enter
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--otp popup -->

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>

    {{--    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>--}}
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#riders').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Rider',
                width: '100%',
                allowClear: true
            });
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.receive.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Delivery Note No.');
                            head.push('Vigilance Verification');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Business Category');
                            head.push('Rider');
                            head.push('Rider Type');
                            head.push('Rider Category');
                            head.push('Route');
                            head.push('No. Of Shipments');
                            head.push('No. Of Pending Shipments');
                            head.push('No. Of Delivered Shipments');
                            head.push('Assigned By');
                            head.push('Assigned Date');
                            head.push('Total COD');
                            head.push('Status');
                            head.push('Last Updated (Date)');
                            head.push('Last Updated By');
                            head.push('Created Via');
                            $.each(result.data, function (index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.delivery_note_id_padded);
                                row.push(values.vigilance_verification_excel);
                                row.push(values.hub);
                                row.push(values.zone_name);
                                row.push(values.business_category);
                                row.push(values.rider);
                                row.push(values.rt);
                                row.push(values.operation_rider_id);
                                row.push(values.route);
                                row.push(values.shipments_count);
                                row.push(values.shipments_unverified_count);
                                row.push(values.delivered_shipments);
                                row.push(values.assignee);
                                row.push(values.created_at);
                                row.push(values.amount);
                                row.push(values.pending_status);
                                row.push(values.last_updated_at);
                                row.push(values.updated_by);
                                row.push(values.created_via);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Receive Deliveries',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.delivery.receive.list') }}',
                    data: function (d) {
                        d.delivery_note_number = $('#scan_delivery_note').val();
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'delivery_note_id',
                order: [[1, 'desc']],
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'delivery_note', name: 'delivery_notes.id', class: 'align-middle delivery_note'},
                    {data: 'vigilance_verification', name: 'vigilance_verification', class: 'align-middle vigilance_verification', orderable: false},

                    {data: 'hub', name: 'oc.name', class: 'align-middle hub'},
                    {data: 'zone_name', name: 'z.name', class: 'align-middle zone_name'},
                    {
                        data: 'business_category',
                        name: 'oc.business_category_id',
                        class: 'align-middle business_category'
                    },
                    {data: 'rider', name: 'riders.name', class: 'align-middle rider'},
                    {data: 'rt', name: 'rider_types.name', class: 'align-middle rider_types'},
                    {data: 'operation_rider_id', name: 'riders.operation_rider_id', class: 'align-middle operation_rider_id'},
                    {data: 'route', name: 'route', class: 'align-middle route'},
                    {
                        data: 'shipments_count_link',
                        name: 'delivery_notes.shipments_count',
                        class: 'align-middle shipments_count_link text-center'
                    },
                    {
                        data: 'shipments_unverified_link',
                        name: 'shipments_unverified_count',
                        class: 'align-middle shipments_unverified_link text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'delivered_shipments',
                        name: 'delivery_notes.delivered_shipments',
                        class: 'align-middle delivered_shipments text-center',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'assignee', name: 'admins.name', class: 'align-middle assignee'},
                    {data: 'created_at', name: 'delivery_notes.created_at', class: 'align-middle created_at'},
                    {data: 'amount', name: 'delivery_notes.total_cod_amount', class: 'align-middle amount'},
                    {data: 'pending_status', name: 'pending_status', class: 'align-middle pending_status'},
                    {
                        data: 'last_updated_at',
                        name: 'delivery_notes.last_updated_at',
                        class: 'align-middle last_updated_at'
                    },
                    {data: 'updated_by', name: 'delivery_notes.updated_by', class: 'align-middle updated_by'},
                    {data: 'created_via', name: 'delivery_notes.created_via_app', class: 'align-middle created_via'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api(settings);
                    var data = api.rows({page: 'current'}).data();

                    if ($('#scan_delivery_note').val() != '') {
                        if (data.length > 0) {
                            scan_sound(1);
                        } else {
                            scan_sound(2);
                        }
                    }
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Pending for Update</option>' +
                        '<option value="1">Pending for Verification</option>' +
                        '</select>';
                    var business_drop = '<select name="business_select" id="business_select" class="select2 form-control">' +
                        '<option value="1">Domestic</option>' +
                        '<option value="2">International</option>' +
                        '</select>';
                  /*  var vigilance_drop = '<select name="vigilance_select" id="vigilance_select" class="select2 form-control">' +
                        '<option value="1">Yes</option>' +
                        '<option value="2">Partial</option>' +
                        '<option value="3">No</option>' +
                        '</select>';*/
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.shipments_unverified_link')) {
                            $(td).appendTo($(search));
                        } else if ($(header).is('.pending_status')) {
                            $(drop_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.business_category')) {
                            $(business_drop).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        /*} else if ($(header).is('.vigilance_verification')) {
                            $(vigilance_drop).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);*/
                        } else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#business_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Category",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    /*$("#vigilance_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Verification",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });*/
                    
                    this.api().table().columns.adjust();
                }
            });

            $('#otp_input').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'mask': '999999'
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

            $('#scan_delivery_note').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function () {
                table.draw();
            });


            function print(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        var tab = window.open('', '_blank');

                        if (!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        } else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }

            $('body').on('click', '.printdeliverynote', function () {
                var deliverynote = $(this).parents('tr').attr('id');
                print(deliverynote);
            });
            $('body').on('click', '.printTempDNCC', function () {
                var note_id = $(this).parents('tr').attr('id');
                var temporary = 'temporary';
                printTemp(note_id, temporary);
            });
            $('body').on('click', '.printUndeliveredDNCC', function () {
                var note_id = $(this).parents('tr').attr('id');
                printUndelivered(note_id);
            });
            $('body').on('keyup change', '#otp_input', function () {
                if ($(this).val().length === 6) {
                    $('#otp_submit').attr('disabled', false);
                } else {
                    $('#otp_submit').attr('disabled', true);
                }
            });

            $('#operation_rider_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Category*',
                width: '100%',
            }).bind('select2:select', function () {
                if (this.value) {
                    $.ajax({
                        url: '{!! route('admin.delivery.note.operation_riders') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'operation_rider_type': this.value,
                        }
                    }).done(function (data) {

                        if (data.status == 1) {
                            var html = "";
                            $.each(data.riders, function (key, value) {
                                if (value.trax_id)
                                    html += `<option value="${value.id}">${value.name} - ${value.trax_id}</option>`;
                                else
                                    html += `<option value="${value.id}">${value.name}</option>`;

                            });
                            $('#riders').html(html);
                            $('#riders').val('').trigger('change');
                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });
            $('#route').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Route*',
                width: '100%',
            });
            $('#riders').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Rider*',
                width: '100%',
            });
            $('#riders').on('change', function () {
                var route = $(this).find(":selected").data("id");
                var rider_id = $(this).val();
                if (rider_id != null) {
                    $.ajax({
                        url: '{!! route('admin.delivery.note.rider_dncc_status') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'rider_id': rider_id,
                        }
                    }).done(function (data) {
                        if (data.status == 1) {
                            ccd_rider = parseInt(data.ccd_rider);
                            $('#route').val(route).trigger('change');
                            $("#reassign_button").attr('disabled', false);
                        } else {
                            toastr.error(data.error, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                            $("#reassign_button").attr('disabled', true);
                        }
                    });
                } else {
                    $('#route').val(route).trigger('change');
                }

            });

            function printTemp(id, temp = null) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.dncc.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        'temporary': temp,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        var tab = window.open('', '_blank');

                        if (!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        } else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }

            function printUndelivered(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.receive.undelivered.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
                    }
                })
                    .done(function (data) {
                        var tab = window.open('', '_blank');

                        if (!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        } else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }

            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click', 'tr td.shipments_count_link button', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.receive.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                    .done(function (data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function (index, tracking_number) {
                                    html += '<u><a href=' + route + '?tracking_number=' + tracking_number + ' target="_blank">' + tracking_number + '</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });

            });

            $('#datatable tbody').on('click', 'tr td.vigilance_verification button.verified_count', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.receive.shipments_verified') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                    .done(function (data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function (index, tracking_number) {
                                    html += '<u><a href=' + route + '?tracking_number=' + tracking_number + ' target="_blank">' + tracking_number + '</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });

            });

            $('#datatable tbody').on('click', 'tr td.vigilance_verification button.partial_count', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.receive.shipment_partial') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'delivery_note_id': id
                    }
                })
                    .done(function (data) {
                        if (data) {
                            var html = '';
                            console.log(data);
                            if (data.shipments) {
                                $.each(data.shipments, function (index, tracking_number) {
                                    html += '<u><a href=' + route + '?tracking_number=' + tracking_number + ' target="_blank">' + tracking_number + '</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });

            });


            $('body').on('click', '.reassign_rider', function () {
                var note_id = $(this).parents('tr').attr('id');
                $('#delivery_note_id').val(note_id);
                $('#reassign_modal').modal('show');
            });
            // $('#scan_delivery_note').on('change', function () {
            //     var count = table.rows().count();
            //     if(count > 0){
            //         scan_sound(1);
            //     }else{
            //         scan_sound(2);
            //     }
            // });
            {{--$('#scan_tracking').on('change',function () {--}}
            {{--var scan = $(this);--}}
            {{--var tracking = $(this).val();--}}
            {{--var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;--}}
            {{--if(numberRegex.test(tracking)) {--}}

            {{--var url = "{{route("admin.delivery.receive.status","id")}}";--}}
            {{--url = url.replace('id',tracking);--}}

            {{--window.location.href = url;--}}
            {{--}else{--}}
            {{--scan.val('');--}}
            {{--}--}}
            {{--});--}}

            $('#reassign_button').on('click', function () {
                var operation_id = $('#operation_rider_id').val();
                var route = $('#route').val();
                var rider = $('#riders').val();
                var rider_otp = $('#rider_otp').val();
                var errors = 0;
                if (rider !== '' && rider !== null) {
                    $('#rider_error').css('display', 'none');
                } else {
                    var error = "Rider not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#rider_error').css('display', 'block');
                }
                if (route !== '' && route !== null) {
                    $('#route_error').css('display', 'none');
                } else {
                    var error = "Route not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    errors = 1;
                    $('#route_error').css('display', 'block');
                }
                if (errors == 0) {
                    if (operation_id === '2' || rider_otp === '0') {
                        reassign_rider();
                    } else {
                        otp_generation();
                    }
                }
            });

            $('#otp_submit').on('click', function () {
                otp_verification();
            });

            $('#otp_input').keypress(function (event) {
                if (event.keyCode == 13) {
                    otp_verification();
                }
            });

            function reassign_rider() {
                var rider = $('#riders').val();
                if (rider) {
                    swal({
                        text: 'Are you sure, you want to Reassign rider?',
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
                                url: '{!! route('admin.delivery.receive.reassign_rider') !!}',
                                method: 'post',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'rider': rider,
                                    'oper_id': $('#operation_rider_id').val(),
                                    'delivery_note_id': $('#delivery_note_id').val()
                                }
                            })
                                .done(function (data) {
                                    if (data.status == 0) {
                                        toastr.success(data.success, 'Success!', {
                                            positionClass: 'toast-bottom-center',
                                            containerId: 'toast-bottom-center'
                                        });
                                    } else {
                                        toastr.error(data.error, 'Error!', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center'
                                        });
                                    }
                                    table.draw(true);
                                    $('#reassign_modal').modal('hide');
                                });
                        }
                    });
                } else {
                    var error = "Rider not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            }

            function otp_generation() {
                var rider = $('#riders').val();
                if (rider) {
                    $('#OtpModal').modal('show');
                    $.ajax({
                        url: '{!! route('admin.delivery.note.otp.generate') !!}',
                        method: 'POST',
                        data: {
                            'rider': rider,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        $('#otp_input').focus();
                    });
                } else {
                    var error = "Rider not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            }

            function otp_verification() {
                var otp = $('#otp_input').val();
                var rider = $('#riders').val();
                if (rider) {
                    if (otp.length == 6) {
                        $.ajax({
                            url: '{!! route('admin.delivery.note.otp.verify') !!}',
                            type: 'POST',
                            data: {
                                'rider': rider,
                                'otp': otp,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            $('#otp_input').val('');
                            $('#otp_submit').attr('disabled', true);
                            if (data.status === 0) {
                                toastr.error(data.error, 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            } else {
                                $('#OtpModal').modal('hide');
                                reassign_rider();
                            }
                        });
                    }
                } else {
                    var error = "Rider not selected!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }

            }

            $('#reassign_modal').on('hide.bs.modal', function (e) {
                $('#reassign_rider_form')[0].reset();
                $("#reassign_button").attr('disabled', true);
                $('#riders').html("");
                $('#operation_rider_id').val('').trigger('change');
                $('#route').val('').trigger('change');
                $('#rider_error').css('display', 'none');
                $('#route_error').css('display', 'none');
                $('#delivery_note_id').val('');
                $('#otp_input').val('');
                $('#OtpModal').modal('hide');
            });



        });
    </script>
@endsection