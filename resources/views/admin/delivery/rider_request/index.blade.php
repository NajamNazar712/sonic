@extends('admin.layout.master')
@section('title','Pending Delivery Note Requests')

@section('content')
    <h1 class="mb-1">
        Pending Delivery Note Requests
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')


                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Request Note No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Rider</th>
                        <th class="border-primary border-darken-1">Area</th>
                        <th class="border-primary border-darken-1">Rider Type</th>
                        <th class="border-primary border-darken-1">Route</th>
                        <th class="border-primary border-darken-1">No. Of Shipments</th>
                        <th class="border-primary border-darken-1">Total Collection</th>
                        <th class="border-primary border-darken-1">Requested At</th>
                        <th class="border-primary border-darken-1">Last Updated By</th>
                        <th class="border-primary border-darken-1">Last Updated At</th>
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
            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Request Note No.');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Rider');
                            head.push('Area');
                            head.push('Rider Type');
                            head.push('Route');
                            head.push('No. Of Shipments');
                            head.push('Total COD');
                            head.push('Requested At');
                            head.push('Last Updated By');
                            head.push('Last Updated (Date)');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.request_note_id_padded);
                                row.push(values.hub);
                                row.push(values.zone_name);
                                row.push(values.rider);
                                row.push(values.area);
                                row.push(values.rt);
                                row.push(values.route);
                                row.push(values.shipments_count);
                                row.push(values.amount);
                                row.push(values.date);
                                row.push(values.admin_name);
                                row.push(values.updated_at);
                                body.push(row);
                            });
                        },
                        url: '{{ route('admin.delivery.rider_request.list') }}',
                        data: params,
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
                        title: 'Pending Delivery Note Requests',
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
                    url: '{{ route('admin.delivery.rider_request.list') }}',
                    data: function (d) {
                        d.delivery_note_number = $('#scan_delivery_note').val();
                        d.search_tracking = $('#search_tracking').val();
                    }
                },
                rowId: 'request_note_id',
                order: [[9, 'desc']],
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
                    {data: 'request_note_id_padded', name: 'rider_delivery_note_requests.id', class: 'align-middle request_note'},
                    {data: 'hub', name: 'c.name', class: 'align-middle hub'},
                    {data: 'zone_name', name: 'z.name', class: 'align-middle zone_name'},
                    {data: 'rider', name: 'r.name', class: 'align-middle rider'},
                    {data: 'area', name: 'ca.name', class: 'align-middle area'},
                    {data: 'rt', name: 'rider_types.name', class: 'align-middle rider_types'},
                    {data: 'route', name: 'route', class: 'align-middle route', orderable: false},
                    {data: 'shipments_count_link',name: 'delivery_notes.shipments_count',class: 'align-middle shipments_count_link text-center',orderable: false, searchable: false},
                    {data: 'amount', name: 'rider_delivery_note_requests.total_cod_amount', class: 'align-middle amount'},
                    {data: 'date', name: 'rider_delivery_note_requests.created_at', class: 'align-middle date'},
                    {data: 'admin_name', name: 'ad.name', class: 'align-middle admin_name'},
                    {data: 'updated_at', name: 'rider_delivery_note_requests.updated_at', class: 'align-middle updated_at'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                drawCallback: function (settings) {
                    var api = new $.fn.dataTable.Api(settings);
                    var data = api.rows({page: 'current'}).data();
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.shipments_count_link')) {
                            $(td).appendTo($(search));
                        }  else {
                            var current = $(input).appendTo($(search)).on('change', function () {
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

            $('#otp_input').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false,
                'rightAlign': false,
                'mask': '999999'
            });

            /*$('body').on('click', '.printdeliverynote', function () {
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
            });*/


            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click', 'tr td.shipments_count_link button', function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.delivery.rider_request.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'request_note_id': id
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

        });
    </script>
@endsection