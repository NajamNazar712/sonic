@extends('admin.layout.master')

@section('title', 'Manifest History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Manifest History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="text-center">
                                <form id="tracking_number_search_form" class="d-inline-block form-inline ml-1 mb-1 justify-content-center" novalidate="novalidate">
                                    <div class="form-group">
                                        <input type="text" name="tracking_number" class="form-control tracking_number" id="tracking_number" placeholder="Tracking Number">
                                    </div>
                                </form>

                                <form id="bag_number_search_form" class="d-inline-block form-inline ml-1 mb-1 justify-content-center" novalidate="novalidate">
                                    <div class="form-group">
                                        <input type="text" name="bag_number" class="form-control bag_number" id="bag_number" placeholder="Bag No">
                                    </div>
                                </form>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Manifest No.</th>
                                    <th class="border-primary border-darken-1">Bag Quantity</th>
                                    <th class="border-primary border-darken-1">Short Received Bags</th>
                                    <th class="border-primary border-darken-1">No. of Shipments</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Total Weight</th>
                                    <th class="border-primary border-darken-1">Transport Mode</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
{{--                                    <th class="border-primary border-darken-1">Route Name</th>--}}
                                    <th class="border-primary border-darken-1">Driver Name</th>
                                    <th class="border-primary border-darken-1">Vehicle</th>
                                    <th class="border-primary border-darken-1">Contact No.</th>
                                    <th class="border-primary border-darken-1">Transit Date</th>
                                    <th class="border-primary border-darken-1">Transit By</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                </tr>
                                </thead>
                            </table>

                            <div class="modal fade" id="info_modal" role="dialog" aria-labelledby="info_modal_title" aria-hidden="true">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bags_data" role="dialog" aria-labelledby="bags_data" aria-hidden="true">
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
    <div class="modal fade" id="short_received_bags" role="dialog" aria-labelledby="short_received_bags" aria-hidden="true">
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
    <div class="modal fade" id="shipments" role="dialog" aria-labelledby="shipments" aria-hidden="true">
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
            @if(session('errors'))
            scan_sound(2);
            @endif
            @if(session('success'))
            scan_sound(1);
            @endif
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.cargo_manifest.print') !!}',
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
                        url: '{{ route('admin.cargo_manifest.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Cargo No.');
                            head.push('Bag Quantity');
                            head.push('Short Received Bags');
                            head.push('No. of Shipments');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Shipping Mode');
                            head.push('Total Weight');
                            head.push('Transport Mode');
                            head.push('Vendor');
                            // head.push('Route Name');
                            head.push('Driver Name');
                            head.push('Vehicle');
                            head.push('Contact No.');
                            head.push('Transit Datetime');
                            head.push('Transit By');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.manifest);
                                row.push(values.bags_count);
                                row.push(values.short_received_bags_count);
                                row.push(values.shipments_count);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.shipping_mode);
                                row.push(values.actual_weight);
                                row.push(values.transport_mode);
                                row.push(values.vendor);
                                // row.push(values.route_name);
                                row.push(values.driver_name);
                                row.push(values.vehicles);
                                row.push(values.phone_number);
                                row.push(values.transit_at);
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
                        title: 'Manifest History',
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
                    url: '{{ route('admin.cargo_manifest.history.list') }}',
                    data: function (d) {
                        d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
                        d.bag_number = $('#bag_number_search_form #bag_number').val();
                    }
                },
                rowId: 'id',
                order: [[14, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'manifest_id', name: 'cargo_manifests.id', class: 'align-middle manifest_id'},
                    {data: 'bags', name: 'cargo_manifests.bags', class: 'align-middle text-center bags'},
                    {data: 'short_received_bags', name: 'cargo_manifests.bags', class: 'align-middle text-center short_received_bags'},
                    {data: 'shipments', name: 'cargo_manifests.shipments', class: 'align-middle text-center shipments'},
                    {data: 'origin', name: 'oh.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dh.name', class: 'align-middle destination'},
                    {data: 'shipping_mode', name: 'shipping_mode', class: 'align-middle shipping_mode'},
                    {data: 'actual_weight', name: 'cargo_manifests.actual_weight', class: 'align-middle actual_weight'},
                    {data: 'transport_mode', name: 'tm.id', class: 'align-middle transport_mode'},
                    {data: 'vendor', name: 'cargo_manifests.vendor_name', class: 'align-middle vendor'},
                    // {data: 'route_name', name: 'cargo_manifests.route_name', class: 'align-middle route_name'},
                    {data: 'driver_name', name: 'cargo_manifests.driver_name', class: 'align-middle driver_name'},
                    {data: 'vehicles', name: 'vehicles', class: 'align-middle vehicles'},
                    {data: 'phone_number', name: 'cargo_manifests.driver_phone', class: 'align-middle phone_number'},
                    {data: 'transit_at', name: 'cargo_manifests.created_at', class: 'align-middle transit_at'},
                    {data: 'transitted_by', name: 'a.name', class: 'align-middle transitted_by'},
                    {data: 'status', name: 'cargo_manifests.status_id', class: 'align-middle status'}
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"><option value="1">Created</option><option value="2">Completed</option></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control"></select>';
                    var transport_select = '<select name="transport_select" id="transport_select" class="select2 form-control"></select>';
                    var vendor_select = '<select name="vendor_select" id="vendor_select" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.aging')  || $(header).is('.junctions') ) {
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

                    $("#status_select").prepend('<option value="" selected></option>').select2({
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
                    var data4 = $.map({!! $transport_vendor !!}, function (obj) {
                        obj.id = obj.name;
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

            $('#datatable tbody').on('click', 'tr td.bags button', function() {
                var manifest_id = table.row($(this).parents('tr')).data().manifest;


                $('#bags_data .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo_manifest.history.bags') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'manifest_id': manifest_id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var head = '';
                            var tracking_numbers = '';

                            head = '<h4 class="modal-title" id="shipments_title">Bag(s)</h4>' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '<span aria-hidden="true">×</span>\n' +
                                '</button>';

                            $.each(data, function(index, $bag_numbers) {
                                tracking_numbers += '<u>'+ $bag_numbers+ '</u><br>';
                            });

                            $('#bags_data .modal-header').html(head);
                            $('#bags_data .modal-body').html(tracking_numbers);

                            $('#bags_data').modal('show');
                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.short_received_bags button', function() {
                var manifest_id = table.row($(this).parents('tr')).data().manifest;


                $('#short_received_bags .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo_manifest.history.short_received_bags') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'manifest_id': manifest_id
                    }
                })
                    .done(function(data) {
                        if (data.status == 1) {
                            var head = '';
                            var tracking_numbers = '';

                            head = '<h4 class="modal-title" id="shipments_title">Short Received Bag(s)</h4>' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                                '<span aria-hidden="true">×</span>\n' +
                                '</button>';

                            $.each(data.tracking_numbers, function(bag_number, tracking_number_array) {
                                tracking_numbers += '<u>'+ bag_number+ '</u><br>';
                                $.each(tracking_number_array, function(index, tracking_nuber) {
                                    tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_nuber+' target="_blank">'+tracking_nuber+'</a></u><br>';
                                });
                                tracking_numbers += '<br>';
                            });

                            $('#short_received_bags .modal-header').html(head);
                            $('#short_received_bags .modal-body').html(tracking_numbers);

                            $('#short_received_bags').modal('show');
                        }
                        else{
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }
                    });
            });

            $('#datatable tbody').on('click', 'tr td.shipments button', function() {
                var manifest_id = table.row($(this).parents('tr')).data().manifest;


                $('#shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.cargo_manifest.history.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'manifest_id': manifest_id
                    }
                })
                    .done(function(data) {

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

                    });
            });
        });

    </script>
@endsection