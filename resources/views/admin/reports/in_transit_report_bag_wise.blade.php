@extends('admin.layout.master')

@section('title', 'In Transit Report Bag Wise')

@section('content')
    <h1 class="mb-1">
        In Transit Report Bag Wise
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Bag No.</th>
                        <th class="border-primary border-darken-1">Bag Type</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">No of Shipments</th>
                        <th class="border-primary border-darken-1">Short Received Shipments</th>
                        <th class="border-primary border-darken-1">Shipping Mode</th>
                        <th class="border-primary border-darken-1">Shipments Weight</th>
                        <th class="border-primary border-darken-1">Transit Datetime</th>
                        <th class="border-primary border-darken-1">Transitted By</th>
                        <th class="border-primary border-darken-1">Master Cargo Received Datetime</th>
                        <th class="border-primary border-darken-1">Master Cargo Received By</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Aging</th>
                    </tr>
                    </thead>
                </table>
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
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
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
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.master_cargo.bag.in_transit.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Bag No.');
                            head.push('Bag Type');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('No. of Shipments');
                            head.push('Short Received Shipments');
                            head.push('Shipping Mode');
                            head.push('Shipments Weight');
                            head.push('Transit Datetime');
                            head.push('Transitted By');
                            head.push('Received Datetime');
                            head.push('Received By');
                            head.push('Status');
                            head.push('Aging');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.bag_no);
                                row.push(values.type);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.total_shipments);
                                row.push(values.short_received_shipments);
                                row.push(values.shipping_mode);
                                row.push(values.shipments_weight);
                                row.push(values.transitted_date);
                                row.push(values.transitted_by);
                                row.push(values.received_at);
                                row.push(values.received_by);
                                row.push(values.status);
                                row.push(values.aging);


                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'In-Transit Report Bag Wise',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },

                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('admin.reports.master_cargo.bag.in_transit.list') }}',
                },
                rowId: 'bag_no',
                order: [[9, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'bag_no', name: 'bags.seal_number', class: 'align-middle bag_no'},
                    {data: 'type', name: 'type', class: 'align-middle type'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'shipments', name: 'shipments', class: 'align-middle text-center shipments'},
                    {data: 'short_received', name: 'short_received', class: 'align-middle short_received text-center'},
                    {data: 'shipping_mode', name: 'sm.mode', class: 'align-middle shipping_mode'},
                    {data: 'shipments_weight', name: 'shipments_weight', class: 'align-middle shipments_weight'},
                    {data: 'transitted_date', name: 'mc.created_at', class: 'align-middle transitted_date'},
                    {data: 'transitted_by', name: 'ad.name', class: 'align-middle transitted_date'},
                    {data: 'received_at', name: 'received_at', class: 'align-middle received_at'},
                    {data: 'received_by', name: 'a.name', class: 'align-middle received_by'},
                    {data: 'status_id', name: 'bs.id', class: 'align-middle status_id'},
                    {orderable : false, searchable : false, data: 'aging', name: 'aging', class: 'align-middle aging'},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {

                   /* var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.aging') || $(header).is('.bag_no') || $(header).is('.type') || $(header).is('.origin') || $(header).is('.destination') || $(header).is('.aging') || $(header).is('.aging')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status_id')){
                            $(status_select).appendTo($(search))
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
                    var data1 = $.map({!! $bag_statuses !!}, function (obj) {
                        obj.id = obj.id // replace pk with your identifier

                        return obj;
                    });
                    var data1 = $.map({!! $bag_statuses !!}, function (obj) {
                        obj.text = obj.name; // replace name with the property used for the text

                        return obj;
                    });

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });*/

                    this.api().table().columns.adjust();
                }
            });
            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click','tr td.shipments button',function () {
                var id =  parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.reports.master_cargo.bag.in_transit.shipments') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'bag_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

            });

            $('#datatable tbody').on('click','tr td.short_received button',function () {
                var id =  parseInt($(this).parents('tr').attr('id'));
                $.ajax({
                    url: '{!! route('admin.reports.master_cargo.bag.in_transit.short_received') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'bag_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, tracking_number) {
                                    html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                });
                            }
                            $('#shipments_modal .modal-body').html(html);
                        }
                    });
                $('#shipments_modal .modal-body').html('');
                $('#shipments_modal').modal('show');

            });

        });

    </script>
@endsection