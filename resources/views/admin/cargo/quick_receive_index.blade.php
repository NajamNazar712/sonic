@extends('admin.layout.master')

@section('title', 'Quick Receive List')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Quick Receive List
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
                                    <th class="border-primary border-darken-1"> S. No.</th>
                                    <th class="border-primary border-darken-1"> Created At</th>
                                    <th class="border-primary border-darken-1"> Created By</th>
                                    <th class="border-primary border-darken-1"> Cargoes</th>
                                    <th class="border-primary border-darken-1"> Shipments</th>
                                    <th class="border-primary border-darken-1"> Excel</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
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
    <div class="modal fade" id="cargoes_modal" data-backdrop="static" role="dialog" aria-labelledby="cargoes_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="cargoes_modal_title">Cargo ID(s)</h4>

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
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
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
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.cargo.history.print') !!}',
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

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cargo.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Cargo No.');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Shipment(s)');
                            head.push('Short Received Shipment(s)');
                            head.push('Shipping Mode');
                            head.push('Junction 1');
                            head.push('Junction 1 Received At');
                            head.push('Junction 1 Received By');
                            head.push('Junction 2');
                            head.push('Junction 2 Received At');
                            head.push('Junction 2 Received By');
                            head.push('Send From Junction Date');
                            head.push('Send From Junction By');
                            head.push('Transport Mode');
                            head.push('Cargo Type');
                            head.push('Vendor');
                            head.push('Builty No.');
                            head.push('Seal No.');
                            head.push('Shipments Weight');
                            head.push('Chargeable Weight');
                            head.push('Actual Weight');
                            head.push('Vendor Weight');
                            head.push('Transit Datetime');
                            head.push('Transitted By');
                            head.push('Status');
                            head.push('Received By');
                            head.push('Received Datetime');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id_padded);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.shipments_count);
                                row.push(values.short_received_shipments_count);
                                row.push(values.shipping_mode);
                                row.push(values.junction_1);
                                row.push(values.junction_1_received_at);
                                row.push(values.junction_1_received_by);
                                row.push(values.junction_2);
                                row.push(values.junction_2_received_at);
                                row.push(values.junction_2_received_by);
                                row.push(values.junction_send_at);
                                row.push(values.junction_send_by);
                                row.push(values.transport_mode);
                                row.push(values.cargo_type);
                                row.push(values.vendor);
                                row.push(values.builty_number);
                                row.push(values.seal_number);
                                row.push(values.shipments_weight);
                                row.push(values.chargeable_weight);
                                row.push(values.actual_weight);
                                row.push(values.vendor_weight);
                                row.push(values.transit_at);
                                row.push(values.transitted_by);
                                row.push(values.status);
                                row.push(values.received_by);
                                row.push(values.received_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: 'tp',
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                autoWidth: false,
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.cargo.receive.quick.list.ajax') }}',
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle text-center serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'created_at', name: 'cargo_consignment_excels.created_at', class: 'align-middle text-center created_at'},
                    {data: 'created_by', name: 'a.name', class: 'align-middle text-center created_by'},
                    {data: 'cargoes_button', name: 'cargo_consignment_excels.cargoes', class: 'align-middle text-center cargoes'},
                    {data: 'shipments_button', name: 'cargo_consignment_excels.shipments', class: 'align-middle text-center shipments'},
                    {data: 'excel_button', name: 'cargo_consignment_excels.excel', class: 'align-middle text-center excel'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click', 'tr td.shipments button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#shipments_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo.receive.quick.list.details') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'cargo_excel_id': id,
                        'shipments': 1
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.details) {
                                $.each(data.details, function(index, tracking_numbers) {
                                    shipments += '<u><a href='+route+'?tracking_number='+tracking_numbers+' target="_blank">'+tracking_numbers+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(shipments);
                            $('#shipments_modal').modal('show');


                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.cargoes button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#cargoes_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo.receive.quick.list.details') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'cargo_excel_id': id,
                        'shipments': 0
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var shipments = '';
                            if (data.details) {
                                $.each(data.details, function(index, cargo_id) {
                                    shipments += cargo_id + '<br>';
                                });
                            }
                            $('#cargoes_modal .modal-body').html(shipments);
                            $('#cargoes_modal').modal('show');


                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.short_received_shipments button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo.in_transit.short_received_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var head = '';
                            var tracking_numbers = '';

                            head = '<h4 class="modal-title" id="shipments_title">Short Received Shipment(s)</h4>' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '<span aria-hidden="true">×</span>\n' +
                                '</button>';

                            $.each(data, function(index, tracking_number) {
                                tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                            });

                            $('#shipments .modal-header').html(head);
                            $('#shipments .modal-body').html(tracking_numbers);

                            $('#shipments').modal('show');
                        }
                    });
            });



            $('#datatable tbody').on('click','tr td.cargo_number button.print',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                print(id);

            });
        });
    </script>
@endsection