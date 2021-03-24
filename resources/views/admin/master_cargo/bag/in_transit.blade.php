@extends('admin.layout.master')

@section('title', 'Pending Bags for Receiving')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pending Bags for Receiving
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            @if (session('role_id') == 1 || in_array(31, session('permissions')))
                                <form id="receive_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.master_cargo.bag.in_transit.receive') }}">
                                    {{ csrf_field() }}

                                    <div class="form-group">
                                        <input type="text" name="bag_number" class="form-control bag_number" placeholder="Bag Number*" data-rule-required="true" data-msg-required="Bag Number is required">
                                    </div>

                                    <div class="form-group ml-1">
                                        <button type="submit" name="receive" class="btn btn-primary" value="Receive">Receive</button>
                                    </div>
                                </form>
                            @endif
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
                                    <th class="border-primary border-darken-1">Master Cargo No.</th>
                                    <th class="border-primary border-darken-1">Origin</th>
                                    <th class="border-primary border-darken-1">Destination</th>
                                    <th class="border-primary border-darken-1">Shipment(s)</th>
                                    <th class="border-primary border-darken-1">Short Received Shipment(s)</th>
                                    <th class="border-primary border-darken-1">Quantity</th>
                                    <th class="border-primary border-darken-1">Shipping Mode</th>
                                    <th class="border-primary border-darken-1">Junction 1</th>
                                    <th class="border-primary border-darken-1">Junction 2</th>
                                    <th class="border-primary border-darken-1">Transport Mode</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Shipments Weight</th>
                                    <th class="border-primary border-darken-1">Actual Weight</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Transit Datetime</th>
                                    <th class="border-primary border-darken-1">Transitted By</th>
                                    <th class="border-primary border-darken-1">Received Datetime</th>
                                    <th class="border-primary border-darken-1">Received By</th>
                                    <th class="border-primary border-darken-1">Aging</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
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
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.master_cargo.in_transit.print') !!}',
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
            $('#receive_form input.bag_number').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.master_cargo.bag.in_transit.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Bag No.');
                            head.push('Bag Type');
                            head.push('Master Cargo No.');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Shipment(s)');
                            head.push('Short Received Shipment(s)');
                            head.push('Quantity');
                            head.push('Shipping Mode');
                            head.push('Junction 1');
                            head.push('Junction 2');
                            head.push('Transport Mode');
                            head.push('Vendor');
                            head.push('Shipments Weight');
                            head.push('Actual Weight');
                            head.push('Status');
                            head.push('Transit Datetime');
                            head.push('Transitted By');
                            head.push('Received Datetime');
                            head.push('Received By');
                            head.push('Aging');


                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.seal_number);
                                row.push(values.bag_type);
                                row.push(values.id_padded);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.shipments_count);
                                row.push(values.short_received);
                                row.push(values.quantity);
                                row.push(values.shipping_mode);
                                row.push(values.junction_1);
                                row.push(values.junction_2);
                                row.push(values.transport_mode);
                                row.push(values.vendor);
                                row.push(values.shipments_weight);
                                row.push(values.actual_weight);
                                row.push(values.status);
                                row.push(values.transit_at);
                                row.push(values.transitted_by);
                                row.push(values.cargo_received_at);
                                row.push(values.received_by);
                                row.push(values.aging);

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
                        title: 'Pending Bags for Receiving',
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
                    url: '{{ route('admin.master_cargo.bag.in_transit.list') }}',
                    data: function (d) {
                        d.bag_type = $('#bag_type_search_form #bag_type').val();
                        d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
                        d.bag_number = $('#bag_number_search_form #bag_number').val();
                    }
                },
                rowId: 'id',
                order: [[17, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'seal_number', name: 'bags.seal_number', class: 'align-middle seal_number'},
                    {data: 'bag_type', name: 'bags.type', class: 'align-middle bag_type'},
                    {data: 'id_padded_link', name: 'mc.id', class: 'align-middle master_cargo_number'},
                    {data: 'origin', name: 'oh.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dh.name', class: 'align-middle destination'},
                    {data: 'shipments', name: 'bags.shipments', class: 'align-middle text-center shipments'},
                    {data: 'short_received_shipments', name: 'bags.short_received', class: 'align-middle text-center short_received_shipments'},
                    {data: 'quantity', name: 'bags.quantity', class: 'align-middle text-center quantity'},
                    {data: 'shipping_mode', name: 'shipping_mode', class: 'align-middle shipping_mode'},
                    {data: 'junction_1', name: 'jh1.name', class: 'align-middle junction_1'},
                    {data: 'junction_2', name: 'jh2.name', class: 'align-middle junction_2'},
                    {data: 'transport_mode', name: 'tm.id', class: 'align-middle transport_mode'},
                    {data: 'vendor', name: 'tmv.id', class: 'align-middle vendor'},
                    {data: 'shipments_weight', name: 'bags.shipments_weight', class: 'align-middle shipments_weight'},
                    {data: 'actual_weight', name: 'bags.actual_weight', class: 'align-middle actual_weight'},
                    {data: 'status', name: 'bs.id', class: 'align-middle status'},
                    {data: 'transit_at', name: 'bags.created_at', class: 'align-middle transit_at'},
                    {data: 'transitted_by', name: 'a.name', class: 'align-middle transitted_by'},
                    {data: 'cargo_received_at', name: 'mc.created_at', class: 'align-middle cargo_received_at'},
                    {data: 'received_by', name: 'ra.name', class: 'align-middle received_by'},
                    {data: 'aging', name: 'aging', class: 'align-middle aging', searchable: false, orderable: false},
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
                    var bag_statuses_select = '<select name="bag_statuses_select" id="bag_statuses_select" class="select2 form-control"></select>';
                    var bag_type_select = '<select name="bag_type_select" id="bag_type_select" class="select2 form-control">' +
                        '<option value="1">Normal</option>' +
                        '<option value="2">Return</option>' +
                        '</select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.aging')) {
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
                        }
                        else if($(header).is('.bag_type')){
                            $(bag_type_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.status')){
                            $(bag_statuses_select).appendTo($(search))
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
                    $("#bag_type_select").prepend('<option value="" selected></option>').select2({
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
                    var data5 = $.map({!! $bag_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;
                        return obj;
                    });


                    $("#bag_statuses_select").prepend('<option value="" selected></option>').select2({
                        data:data5,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });
            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click', 'tr td.short_received_shipments button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#info_modal .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.master_cargo.bag.in_transit.short_received') !!}',
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

                            head = '<h4 class="modal-title" id="info_modal_title">Short Received Shipments(s)</h4>' +
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
            $('#datatable tbody').on('click', 'tr td.shipments button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));

                $('#shipments .modal-body').html('');

                $.ajax({
                    url: '{!! route('admin.master_cargo.pending.shipments') !!}',
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
                if (this.value.length == 0 || this.value.length >= 12) {
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

            $('#datatable tbody').on('click','tr td.master_cargo_number button.print',function () {
                var cargo_id = parseInt(table.row($(this).parents('tr')).data().master_cargo_id);
                print(cargo_id);
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var bag_id = parseInt($(this).parents('tr').data('seal_number'));
                @if (session('role_id') == 1 || in_array(31, session('permissions')))
                    if ($(this).hasClass('receive')) {
                        $('#receive_form .bag_number').val(bag_id);

                        $('#receive_form').submit();
                    }
                @endif
            });
        });
    </script>
@endsection