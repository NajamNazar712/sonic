@extends('admin.layout.master')

@section('title', 'Supply Chain Shipment On Hold History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Supply Chain Shipment On Hold History
                </h1>

                <div class="card">
                    <div class="card-body">
                        @include('admin.inc.messages')
                        <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                            <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Tracking No.</th>
                                <th class="border-primary border-darken-1">Origin</th>
                                <th class="border-primary border-darken-1">Destination</th>
                                <th class="border-primary border-darken-1">Shipper Name</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Last Status Date</th>
                                <th class="border-primary border-darken-1">Arrival Status Date</th>
                                <th class="border-primary border-darken-1">Delivery Date</th>
                                <th class="border-primary border-darken-1">Dispatch Date</th>
                                <th class="border-primary border-darken-1">Added By</th>
                                <th class="border-primary border-darken-1">Created At</th>
                                <th class="border-primary border-darken-1">On-Hold</th>
                                <th class="border-primary border-darken-1">Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

    <style>
        table.table.table-sm td {
            border: 1px solid #626E82 !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
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
                        url: '{{ route('admin.cargo.supply_chain.shipment_on_hold.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Shipper Name');
                            head.push('Status');
                            head.push('Last Status');
                            head.push('Arrival Status Date');
                            head.push('Delivery Date');
                            head.push('Dispatch Date');
                            head.push('Added By');
                            head.push('On-Hold');
                            head.push('Created At');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.shipper_name);
                                row.push(values.status);
                                row.push(values.last_status_date);
                                row.push(values.arrival_status_date);
                                row.push(values.delivery_date);
                                row.push(values.dispatch_date);
                                row.push(values.status);
                                row.push(values.added_by);
                                row.push(values.created_at);
                                row.push(values.shipment_on_hold_status);

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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Supply Chain Shipment On Hold History',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                autoWidth: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.cargo.supply_chain.shipment_on_hold.history.list') }}',
                    data: function (d) {
                        d.tracking_numbers = $('#search_tracking_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[8, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number text-center', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number text-center'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin text-center'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination text-center'},
                    {data: 'shipper_name', name: 'u.name', class: 'align-middle shipper_name text-center'},
                    { data:'status' ,name: 'ss.id', class: 'align-middle status text-center'},
                    { data:'last_status_date' ,name: 'sj.created_at', class: 'align-middle last_status_date text-center'},
                    { data:'arrival_status_date' ,name: 'sja.created_at', class: 'align-middle arrival_status_date text-center'},
                    {data: 'delivery_date', name: 'shipment_on_hold.delivery_date', class: 'align-middle delivery_date text-center'},
                    { data:'dispatch_date' ,name: 'shipment_on_hold.dispatch_date', class: 'align-middle dispatch_date text-center'},
                    { data:'added_by' ,name: 'a.name', class: 'align-middle added_by text-center'},
                    { data:'created_at' ,name: 'shipment_on_hold.created_at', class: 'align-middle created_at text-center'},
                    { data:'shipment_on_hold_status' ,name: 'shipment_on_hold.status', class: 'align-middle shipment_on_hold_status text-center'},
                    { data:'action' ,name: 'action', class: 'align-middle action text-center'},
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '</select>';
                    var on_hold_select = '<select name="on_hold_select" id="on_hold_select" class="select2 form-control">' +
                        '<option value="0">Allowed</option>' +
                        '<option value="1">On-Hold</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.status')) {
                            $(status_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.shipment_on_hold_status')) {
                            $(on_hold_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
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

                        return obj;
                    });
                    var data = $.map({!! $shipment_status !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data: data,
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#on_hold_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select On-Hold Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                //var tracking_number = $(this).parents('tr').children('td.tracking_number').text();

                if ($(this).hasClass('allow_dispatch_notes')) {
                    swal({
                        title: 'Are you sure?',
                        text: 'You want to remove this shipment from on-hold?',
                        icon: 'success',
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
                            $.ajax({
                                url: '{!! route('admin.cargo.supply_chain.shipment_on_hold.history.allow_dispatch_delivery') !!}',
                                method: 'POST',
                                data: {
                                    'id': id,
                                    '_token': '{{ csrf_token() }}'
                                }
                            })
                                .done(function(data) {
                                    if (data.status == 0) {
                                        toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                    }
                                    else {
                                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                    }

                                    table.draw(false);
                                });
                        }
                    });
                }
            });
        });
    </script>
@endsection