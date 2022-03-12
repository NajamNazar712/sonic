@extends('admin.layout.master')

@section('title', 'Route Distribution Summary Report')

@section('content')
    <h1 class="mb-1">
        Route Distribution Summary Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_destination" id="search_destination" class="form-control select2">
                                @foreach($destination_cities as $destination_city)
                                    <option value="{{$destination_city->id}}">{{$destination_city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_rider" id="search_rider" class="form-control select2">
                                @foreach($riders as $rider)
                                    <option value="{{$rider->id}}">{{$rider->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_rider_cat" id="search_rider_cat" class="form-control select2">
                                @foreach($riders_cat as $rider_cat)
                                    <option value="{{$rider_cat->id}}">{{$rider_cat->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">

                            <select name="search_cutt_off" id="search_cutt_off" class="form-control">
{{--                                <option value="{{'1'}}" selected>8pm to 2pm</option>--}}
{{--                                <option value="{{'2'}}">2:01pm to 7:59pm</option>--}}
                            </select>

                        </fieldset>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <div id="datatable_wrapper">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">

                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">Rider Name</th>
                            <th class="border-primary border-darken-1">Rider Type</th>
                            <th class="border-primary border-darken-1">Delivery Note</th>
                            <th class="border-primary border-darken-1">DNCC Amount</th>
                            <th class="border-primary border-darken-1">Hub</th>
                            <th class="border-primary border-darken-1">Total Out For Delivery</th>
                            <th class="border-primary border-darken-1">Pending</th>
                            <th class="border-primary border-darken-1">Pending %</th>
                            <th class="border-primary border-darken-1">Delivered</th>
                            <th class="border-primary border-darken-1">Delivered %</th>
                            <th class="border-primary border-darken-1">Undelivered</th>
                            <th class="border-primary border-darken-1">Undelivered %</th>
                            <th class="border-primary border-darken-1">RCP</th>
                            <th class="border-primary border-darken-1">RCP %</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="dn_no_modal" data-backdrop="static" role="dialog"
         aria-labelledby="dn_no_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="dn_no_modal">Delivery Notes</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="dn_data">
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
    <style type="text/css">
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#datatable_wrapper').hide();
            var shipments_count = 0;
            var pending_shipments = 0;
            var pending_shipments_per = 0;
            var delivered_shipments = 0;
            var delivered_shipments_per = 0;
            var undelivered_shipments = 0;
            var undelivered_shipments_per = 0;
            var confirmation_pending_shipments = 0;
            var confirmation_pending_shipments_per = 0;

            $('#search_cutt_off').prepend('<option value="1">8pm to 2pm</option><br><option value="2">2:01pm to 7:59pm</option>').select2({
                // placeholder:'Select Zone',
                width:'100%',
                allowClear:true
            });
            $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Zone',
                width:'100%',
                allowClear:true
            });
            $('#search_rider_cat').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider Category',
                width:'100%',
                allowClear:true
            });
            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
            $('#search_destination').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination',
                width:'100%',
                allowClear:true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Rider',
                width:'100%',
                allowClear:true
            });

            var max = '{{ Carbon\Carbon::now() }}';

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: max,
                // format:'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {

                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: max,
                // format:'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {

                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.route_distribution.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];

                            head.push('S.No');
                            head.push('Rider Name');
                            head.push('Rider Type');
                            head.push('Delivery Note');
                            head.push('DNCC Amount');
                            head.push('Hub');
                            head.push('Total Out For Delivery');
                            head.push('Pending');
                            head.push('Pending %');
                            head.push('Delivered');
                            head.push('Delivered %');
                            head.push('Undelivered');
                            head.push('Undelivered %');
                            head.push('RCP');
                            head.push('RCP %');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.courier_name);
                                row.push(values.rider_type);
                                row.push(values.dn_no_excel);
                                row.push(values.dncc_amount);
                                row.push(values.hub);
                                row.push(values.shipments_count);
                                row.push(values.pending_shipments);
                                row.push(values.pending_shipments_per);
                                row.push(values.delivered_shipments);
                                row.push(values.delivered_shipments_per);
                                row.push(values.undelivered_shipments);
                                row.push(values.undelivered_shipments_per);
                                row.push(values.confirmation_pending_shipments);
                                row.push(values.confirmation_pending_shipments_per);
                                body.push(row);
                            });

                            footer.push('-');
                            footer.push('Total');
                            footer.push('');
                            footer.push('');
                            footer.push('');
                            footer.push('');
                            footer.push(shipments_count.toFixed(2));
                            footer.push(pending_shipments.toFixed(2));
                            footer.push(pending_shipments_per.toFixed(2));
                            footer.push(delivered_shipments.toFixed(2));
                            footer.push(delivered_shipments_per.toFixed(2));
                            footer.push(undelivered_shipments.toFixed(2));
                            footer.push(undelivered_shipments_per.toFixed(2));
                            footer.push(confirmation_pending_shipments.toFixed(2));
                            footer.push(confirmation_pending_shipments_per.toFixed(2));
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head, footer: footer};
                }
            } );

            $('#datatable').append("<tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>");
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Route Distribution Summary Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                        footer: true
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                autoWidth:false,
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.route_distribution.list') }}',
                    data: function (d) {
                        d.search_cutt_off = $('#search_cutt_off').val();
                        d.search_destination = $('#search_destination').val();
                        d.search_zone = $('#search_zone').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_rider = $('#search_rider').val();
                        d.search_from = $('#from_date').val();
                        d.search_to = $('#to_date').val();

                        // d.search_from = $('input[name="from_date_formatted"]').val();
                        // d.search_to = $('input[name="to_date_formatted"]').val();
                        d.search_rider_cat = $('#search_rider_cat').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'courier_name' ,name: 'r.name', class: 'align-middle text-center courier_name'},
                    { data:'rider_type' ,name: 'rt.name', class: 'align-middle text-center rider_type'},
                    { data:'dn_no' ,name: 'delivery_notes.id', class: 'align-middle text-center dn_no'},
                    { data:'dncc_amount' ,name: 'station_deposit_notes.sdn_amount', class: 'align-middle text-center dncc_amount'},
                    { data:'hub' ,name: 'hub', class: 'align-middle text-center hub'},
                    { data:'shipments_count', class: 'align-middle shipments_count', orderable: false, searchable: false},
                    { data:'pending_shipments', class: 'align-middle pending_shipments', orderable: false, searchable: false},
                    { data:'pending_shipments_per', class: 'align-middle pending_shipments_per', orderable: false, searchable: false},
                    { data:'delivered_shipments', class: 'align-middle delivered_shipments', orderable: false, searchable: false},
                    { data:'delivered_shipments_per', class: 'align-middle delivered_shipments_per', orderable: false, searchable: false},
                    { data:'undelivered_shipments', class: 'align-middle undelivered_shipments', orderable: false, searchable: false},
                    { data:'undelivered_shipments_per', class: 'align-middle undelivered_shipments_per', orderable: false, searchable: false},
                    { data:'confirmation_pending_shipments', class: 'align-middle confirmation_pending_shipments', orderable: false, searchable: false},
                    { data:'confirmation_pending_shipments_per', class: 'align-middle confirmation_pending_shipments_per', orderable: false, searchable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    api.columns('.courier_name', {
                        page: 'current'
                    }).every(function() {
                        $(this.footer()).html('Total');
                    });
                    api.columns('.shipments_count', {
                        page: 'current'
                    }).every(function() {
                        shipments_count = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(shipments_count.toFixed(2));
                    });
                    api.columns('.delivered_shipments', {
                        page: 'current'
                    }).every(function() {
                        delivered_shipments = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(delivered_shipments.toFixed(2));
                    });
                    api.columns('.delivered_shipments_per', {
                        page: 'current'
                    }).every(function() {
                        delivered_shipments_per = (delivered_shipments/shipments_count)*100;
                        $(this.footer()).html(delivered_shipments_per.toFixed(2));
                    });
                    api.columns('.undelivered_shipments', {
                        page: 'current'
                    }).every(function() {
                        undelivered_shipments = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(undelivered_shipments.toFixed(2));
                    });
                    api.columns('.undelivered_shipments_per', {
                        page: 'current'
                    }).every(function() {
                        undelivered_shipments_per = (undelivered_shipments/shipments_count)*100;
                        $(this.footer()).html(undelivered_shipments_per.toFixed(2));
                    });
                    api.columns('.confirmation_pending_shipments', {
                        page: 'current'
                    }).every(function() {
                        confirmation_pending_shipments = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(confirmation_pending_shipments.toFixed(2));
                    });
                    api.columns('.confirmation_pending_shipments_per', {
                        page: 'current'
                    }).every(function() {
                        confirmation_pending_shipments_per = (confirmation_pending_shipments/shipments_count)*100;
                        $(this.footer()).html(confirmation_pending_shipments_per.toFixed(2));
                    });

                    api.columns('.pending_shipments', {
                        page: 'current'
                    }).every(function() {
                        pending_shipments = this
                            .data()
                            .reduce(function(a, b) {
                                var x = parseFloat(a) || 0;
                                var y = parseFloat(b) || 0;
                                return x + y;
                            }, 0);
                        $(this.footer()).html(pending_shipments.toFixed(2));
                    });
                    api.columns('.pending_shipments_per', {
                        page: 'current'
                    }).every(function() {
                        pending_shipments_per = (pending_shipments/shipments_count)*100;
                        $(this.footer()).html(pending_shipments_per.toFixed(2));
                    });
                }
            });

            $('#search_filter_btn').on('click',function () {
                $('#datatable_wrapper').show();
                table.draw();
            });


            {{--$('#datatable tbody').on('click', 'tr td.dn_no button', function () {--}}
            {{--    var id = parseInt($(this).parents('tr').attr('id'));--}}
            {{--    if (id) {--}}
            {{--        $.ajax({--}}
            {{--            url: '{!! route('admin.reports.destination_delivery_received.get.dn_no') !!}',--}}
            {{--            method: 'POST',--}}
            {{--            data: {--}}
            {{--                '_token': '{{ csrf_token() }}',--}}
            {{--                'dn_id': id,--}}
            {{--            },--}}
            {{--            success: function (data) {--}}
            {{--                if (data.status == 1) {--}}


            {{--                    $('#dn_data').html(data.html);--}}

            {{--                } else {--}}
            {{--                    toastr.error('Something went wrong!', 'Error!', {--}}
            {{--                        positionClass: 'toast-top-center',--}}
            {{--                        containerId: 'toast-top-center'--}}
            {{--                    });--}}
            {{--                }--}}
            {{--            }--}}
            {{--        })--}}

            {{--    }--}}

            {{--});--}}


        });

        function dn_no_pop(id) {
            if (id) {
                $.ajax({
                    url: '{!! route('admin.reports.destination_delivery_received.get.dn_no') !!}',
                    method: 'get',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'dn_no': id,
                    }
                })
                    .done(function (data) {
                        if (data.status == 1) {
                            var notes = '';

                            notes += data.html
                            $('#dn_no_modal .modal-body').html('');
                            $('#dn_no_modal').modal('show');
                            $('#dn_no_modal .modal-body').html(notes);
                        } else {
                            toastr.error('Something went wrong!', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
            }
        }

    </script>
@endsection