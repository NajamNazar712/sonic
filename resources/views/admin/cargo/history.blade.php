@extends('admin.layout.master')

@section('title', 'Cargo History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Cargo History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Cargo No.</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Shipment(s)</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Junction 1</th>
                                    <th class="border-primary border-darken-1">Junction 2</th>
                                    <th class="border-primary border-darken-1">Transport Mode</th>
                                    <th class="border-primary border-darken-1">Cargo Type</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Builty No.</th>
                                    <th class="border-primary border-darken-1">Shipments Weight</th>
                                    <th class="border-primary border-darken-1">Actual Weight</th>
                                    <th class="border-primary border-darken-1">Vendor Weight</th>
                                    <th class="border-primary border-darken-1">Transit Datetime</th>
                                    <th class="border-primary border-darken-1">Transitted By</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Received By</th>
                                    <th class="border-primary border-darken-1">Received At</th>
                                </tr>
                                </thead>
                            </table>


                            <div class="modal fade" id="shipments" role="dialog" aria-labelledby="shipments_title" aria-hidden="true">
                                <div class="modal-dialog modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="shipments_title">Shipment(s)</h4>

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
                        </div>
                    </div>
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

    <script>
        $(document).ready(function() {
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

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cargo.history.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Cargo No.');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Shipment(s)');
                            head.push('Shipping Mode');
                            head.push('Junction 1');
                            head.push('Junction 2');
                            head.push('Transport Mode');
                            head.push('Cargo Type');
                            head.push('Vendor');
                            head.push('Builty No.');
                            head.push('Shipments Weight');
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
                                row.push(values.shipping_mode);
                                row.push(values.junction_1);
                                row.push(values.junction_2);
                                row.push(values.transport_mode);
                                row.push(values.cargo_type);
                                row.push(values.vendor);
                                row.push(values.builty_number);
                                row.push(values.shipments_weight);
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
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Cargo In-transit',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.cargo.history.list') }}',
                rowId: 'id',
                order: [[15, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'id_padded_link', name: 'cargo_consignments.id', class: 'align-middle cargo_number'},
                    {data: 'origin', name: 'oh.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dh.name', class: 'align-middle destination'},
                    {data: 'shipments', name: 'cargo_consignments.shipments', class: 'align-middle text-center shipments'},
                    {data: 'shipping_mode', name: 'shipping_mode', class: 'align-middle shipping_mode'},
                    {data: 'junction_1', name: 'jh1.name', class: 'align-middle junction_1'},
                    {data: 'junction_2', name: 'jh2.name', class: 'align-middle junction_2'},
                    {data: 'transport_mode', name: 'tm.id', class: 'align-middle transport_mode'},
                    {data: 'cargo_type', name: 'cargo_consignments.type', class: 'align-middle cargo_type'},
                    {data: 'vendor', name: 'tmv.id', class: 'align-middle vendor'},
                    {data: 'builty_number', name: 'cargo_consignments.builty_number', class: 'align-middle builty_number'},
                    {data: 'shipments_weight', name: 'cargo_consignments.shipments_weight', class: 'align-middle shipments_weight'},
                    {data: 'actual_weight', name: 'cargo_consignments.actual_weight', class: 'align-middle actual_weight'},
                    {data: 'vendor_weight', name: 'cargo_consignments.vendor_weight', class: 'align-middle vendor_weight'},
                    {data: 'transit_at', name: 'cargo_consignments.created_at', class: 'align-middle transit_at'},
                    {data: 'transitted_by', name: 'a.name', class: 'align-middle transitted_by'},
                    {data: 'status', name: 'status', class: 'align-middle status'},
                    {data: 'received_by', name: 'ri.name', class: 'align-middle received_by'},
                    {data: 'received_at', name: 'cargo_consignments.updated_at', class: 'align-middle received_at'},
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var transport_select = '<select name="transport_select" id="transport_select" class="select2 form-control"></select>';
                    var vendor_select = '<select name="vendor_select" id="vendor_select" class="select2 form-control"></select>';
                    var cargo_type_select = '<select name="cargo_type_select" id="cargo_type_select" class="select2 form-control">' +
                        '<option value="1">Normal</option>' +
                        '<option value="2">Return</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.shipping_mode')){
                            $(mode_drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.transport_mode')){
                            $(transport_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.vendor')){
                            $(vendor_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.cargo_type')){
                            $(cargo_type_select).appendTo($(search))
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
                    var data2 = $.map({!! $cargo_status !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data2,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#cargo_type_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data3 = $.map({!! $transport_mode !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });

                    $("#transport_select").prepend('<option value="" selected></option>').select2({
                        data:data3,
                        placeholder: "Select Transport",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data4 = $.map({!! $transport_vendor !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });


                    $("#vendor_select").prepend('<option value="" selected></option>').select2({
                        data:data4,
                        placeholder: "Select Transport",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            var route = '{!! route('admin.tracking.index') !!}';

            $('#datatable tbody').on('click', 'tr td.shipments button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo.history.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var details = '<table class="table table-sm table-bordered"><tbody>';

                            details += '<tr>';

                            details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Shipment</strong></td>';
                            details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Estimated Weight</strong></td>';
                            details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Actual Weight</strong></td>';
                            details += '<td class="border-primary border-darken-1 align-middle text-center"><strong>Chargeable Weight</strong></td>';

                            details += '</tr>';

                            $.each(data, function(index, shipment) {
                                details += '<tr>';

                                details += '<td class="align-middle text-center"><u><a href=' + route + '?tracking_number=' + shipment.tracking_number + ' target="_blank">' + shipment.tracking_number + '</a></u></td>';

                                details += '<td class="align-middle text-center">' + shipment.estimated_weight + '</td>';
                                details += '<td class="align-middle text-center">' + shipment.actual_weight + '</td>';
                                details += '<td class="align-middle text-center">' + shipment.chargeable_weight + '</td>';

                                details += '</tr>';
                            });

                            details += '</tbody></table>';

                            $('#shipments .modal-body').html(details);

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