@extends('admin.layout.master')

@section('title', 'Bag(s) History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Bag(s) History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="text-center">
                                <form id="bag_type_search_form" class="d-inline-block form-inline mb-1 justify-content-center text-left" novalidate="novalidate">
                                    <div class="form-group">
                                        <select name="bag_type" class="select2" id="bag_type">
                                            <option value="" selected="selected"></option>
                                            <option value="0">All</option>
                                            <option value="1">Normal</option>
                                            <option value="2">Return</option>
                                        </select>
                                    </div>
                                </form>

                                <form id="tracking_number_search_form" class="d-inline-block form-inline ml-1 mb-1 justify-content-center" novalidate="novalidate">
                                    <div class="form-group">
                                        <input type="text" name="tracking_number" class="form-control tracking_number" id="tracking_number" placeholder="Tracking Number">
                                    </div>
                                </form>

                                <form id="bag_number_search_form" class="d-inline-block form-inline ml-1 mb-1 justify-content-center" novalidate="novalidate">
                                    <div class="form-group">
                                        <input type="text" name="bag_number" class="form-control bag_number" id="bag_number" placeholder="Bag Number">
                                    </div>
                                </form>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Bag No.</th>
                                    <th class="border-primary border-darken-1">Bag Type</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Manifest Id</th>
                                    <th class="border-primary border-darken-1">Shipment(s)</th>
                                    <th class="border-primary border-darken-1">Lost Shipment(s)</th>
                                    <th class="border-primary border-darken-1">Junction(s)</th>
                                   <th class="border-primary border-darken-1">Short Received Shipment(s)</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Transport Mode</th>
                                    <th class="border-primary border-darken-1">Shipments Weight</th>
                                    <th class="border-primary border-darken-1">Actual Weight</th>
                                    <th class="border-primary border-darken-1">Transit Datetime</th>
                                    <th class="border-primary border-darken-1">Transitted By</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="short_received_shipments" role="dialog" aria-labelledby="short_received_shipments" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shipments" role="dialog" aria-labelledby="shipments_title" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="lost_shipments" role="dialog" aria-labelledby="lost_shipments" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
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
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.cargo_manifest.bags.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Bag No.');
                            head.push('Bag Type');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Manifest Id');
                            head.push('Shipment(s)');
                            head.push('Lost Shipment(s)');
                            head.push('Short Received Shipment(s)');
                            head.push('Shipping Mode');
                            head.push('Transport Mode');
                            head.push('Shipments Weight');
                            head.push('Actual Weight');
                            head.push('Transit Datetime');
                            head.push('Transitted By');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.seal_number);
                                row.push(values.bag_type);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.manifest);
                                row.push(values.shipments_count);
                                row.push(values.ls);
                                row.push(values.short_received);
                                row.push(values.shipping_mode);
                                row.push(values.transport_mode);
                                row.push(values.shipments_weight);
                                row.push(values.actual_weight);
                                row.push(values.transitted_at);
                                row.push(values.transitted_by);
                                row.push(values.status);

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
                        title: 'Bags History',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
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
                    url: '{{ route('admin.cargo_manifest.bags.history.list') }}',
                    data: function (d) {
                        d.bag_type = $('#bag_type_search_form #bag_type').val();
                        d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
                        d.bag_number = $('#bag_number_search_form #bag_number').val();
                    }
                },
                rowId: 'id',
                order: [[14, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'seal_number', name: 'cargo_manifest_bags.seal_number', class: 'align-middle seal_number'},
                    {data: 'bag_type', name: 'cargo_manifest_bags.type', class: 'align-middle bag_type'},
                    {data: 'origin', name: 'oh.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dh.name', class: 'align-middle destination'},
                    {data: 'manifest_id', name: 'cm.id', class: 'align-middle manifest_id'},
                    {data: 'shipments', name: 'cargo_manifest_bags.shipments', class: 'align-middle text-center shipments'},
                    {data: 'lost_shipments', name: 'cargo_manifest_bags.lost_shipments', class: 'align-middle text-center lost_shipments'},
                    {data: 'junctions', name: 'junctions', class: 'align-middle text-center junctions',orderable: false},
                    {data: 'short_received_shipments', name: 'short_received_shipments', class: 'align-middle text-center short_received_shipments'},
                    {data: 'shipping_mode', name: 'sm.id', class: 'align-middle shipping_mode'},
                    {data: 'transport_mode', name: 'tm.id', class: 'align-middle transport_mode'},
                    {data: 'shipments_weight', name: 'cargo_manifest_bags.shipments_weight', class: 'align-middle shipments_weight'},
                    {data: 'actual_weight', name: 'cargo_manifest_bags.actual_weight', class: 'align-middle actual_weight'},
                    {data: 'transitted_at', name: 'cargo_manifest_bags.created_at', class: 'align-middle transit_at'},
                    {data: 'transitted_by', name: 'a.name', class: 'align-middle transitted_by'},
                    {data: 'status', name: 'bs.id', class: 'align-middle status'},
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
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var transport_select = '<select name="transport_select" id="transport_select" class="select2 form-control"></select>';
                    var vendor_select = '<select name="vendor_select" id="vendor_select" class="select2 form-control"></select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var bag_type_select = '<select name="bag_type_select" id="bag_type_select" class="select2 form-control">' +
                        '<option value="1">Normal</option>' +
                        '<option value="2">Return</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.shipping_mode')){
                            $(mode_drop_select).appendTo($(search))
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
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.bag_type')){
                            $(bag_type_select).appendTo($(search))
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
                    $('#datatable tbody').on('click', '.manifest_id', function () {
                        var manifest_id = table.row($(this).parents('tr')).data().manifest;
                        console.log(manifest_id);
                        $.ajax({
                            url: '{!! route('admin.cargo_manifest.print') !!}',
                            method: 'POST',
                            data: {
                                'cargo_manifest_ids': manifest_id,
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
                                }
                                else {
                                    tab.document.write(data);
                                    tab.document.close();
                                    tab.focus();
                                }
                            });
                    });
                    $('#datatable tbody').on('click', 'tr td.shipments button', function() {
                        var seal_number = table.row($(this).parents('tr')).data().seal_number;

                        $('#shipments .modal-body').html('');

                        $.ajax({
                            url: '{!! route('admin.cargo_manifest.transitted_shipments') !!}',
                            method: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'seal_number': seal_number
                            }
                        })
                            .done(function(data) {
                                if (data) {
                                    var head = '';
                                    var tracking_numbers = '';

                                    head = '<h4 class="modal-title" id="shipments_title">Shipment(s)</h4>' +
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
                    $("#bag_type_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
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
                        placeholder: "Select Shipping Mode",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data2 = $.map({!! $bag_statuses !!}, function (obj) {
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

                    this.api().table().columns.adjust();
                }
            });
            var route = '{!! route('admin.tracking.index') !!}';

        
            $('#bag_type_search_form #bag_type').select2({
                width: '125px',
                placeholder: 'Bag Type'
            }).bind('change', function() {
                table.draw();
            });

            $('#tracking_number_search_form').bind('submit', function(e) {
                e.preventDefault();

                length = $('#tracking_number_search_form #tracking_number').val().length;

                if (length == 0 || length >= 12) {
                    table.draw();
                }
            });

            $('#tracking_number_search_form #tracking_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                if (this.value.length == 0 || this.value.length >= 6) {
                    table.draw();
                }
            });

            $('#bag_number_search_form').bind('submit', function(e) {
                e.preventDefault();

                table.draw();
            });

            $('#bag_number_search_form #bag_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            }).bind('input', function() {
                table.draw();
            });

            $('#datatable tbody').on('click', 'tr td.short_received_shipments button', function() {
                var seal_number = table.row($(this).parents('tr')).data().seal_number;

                $('#short_received_shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo_manifest.short_received_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'seal_number': seal_number
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

                            $('#short_received_shipments .modal-header').html(head);
                            $('#short_received_shipments .modal-body').html(tracking_numbers);

                            $('#short_received_shipments').modal('show');
                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.lost_shipments button', function() {
                var seal_number = table.row($(this).parents('tr')).data().seal_number;

                $('#lost_shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo_manifest.bags.history.lost_shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'seal_number': seal_number
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var head = '';
                            var tracking_numbers = '';

                            head = '<h4 class="modal-title" id="shipments_title">Lost Shipment(s)</h4>' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '<span aria-hidden="true">×</span>\n' +
                                '</button>';

                            $.each(data, function(index, tracking_number) {
                                tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                            });

                            $('#lost_shipments .modal-header').html(head);
                            $('#lost_shipments .modal-body').html(tracking_numbers);

                            $('#lost_shipments').modal('show');
                        }
                    });
            });
        });
        $('#datatable tbody').on('click', '.manifest_id', function () {
            var manifest_id = table.row($(this).parents('tr')).data().manifest_id;
            console.log(manifest_id);
            $.ajax({
                url: '{!! route('admin.cargo_manifest.print') !!}',
                method: 'POST',
                data: {
                    'ids': manifest_id,
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
                    }
                    else {
                        tab.document.write(data);
                        tab.document.close();
                        tab.focus();
                    }
                });
        });

    </script>
@endsection