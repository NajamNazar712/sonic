@extends('client.layout.master')

@section('title', 'Report - Quality of Service')

@section('content')
    <h1 class="mb-1">
        Report - Quality of Service
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')

                <div class="row mb-2 justify-content-center">


                    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
                        <fieldset class="form-group">
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-value="">
                        </fieldset>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-3">
                        <fieldset class="form-group">
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-value="">
                        </fieldset>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">History Status</th>
                        <th class="border-primary border-darken-1">Service</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">COD Amount</th>
                        <th class="border-primary border-darken-1">Aging</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var from_max = '{{ Carbon\Carbon::now() }}';
            var to_max = '{{ Carbon\Carbon::now() }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: from_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    var old_date_formatted = $('input[name="from_date_formatted"]').val();
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: to_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    var current_date_formatted = $('input[name="to_date_formatted"]').val();
                    from_date.pickadate('picker').set('max',new Date(current_date_formatted),{muted:true});
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('cod.reports.qsr.list') }}',
                        data: {
                            'page': 'all',
                            'search_from': $('input[name="from_date_formatted"]').val(),
                            'search_to': $('input[name="to_date_formatted"]').val()
                        },
                        success: function (result) {
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.history_status);
                                row.push(values.service_type);
                                row.push(values.arrival);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.amount);
                                row.push(values.aging);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: $("#datatable thead tr th").map(function() { return this.innerHTML; }).get()};
                }
            } );

            var index_column = [];
            var flag = false;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true,
                buttons: [

                    {
                        extend: 'excel',
                        title: 'QSR Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                        className: 'btn btn-primary',
                    },

                ],
                lengthMenu: [[25, 50, 100], [25, 50, 100]],
                pageLength: 25,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('cod.reports.qsr.list') }}',
                    data: function (d) {

                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'shId',
                order: [[4, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 'shipments.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'history_status', name: 'ss.name', class: 'align-middle history_status'},
                    {data: 'service_type', name: 'bt.booking_type', class: 'align-middle service_type'},
                    {data: 'arrival', name: 'sj.created_at', class: 'align-middle arrival'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'destination', name: 'dc.name', class: 'align-middle destination'},
                    {data: 'amount', name: 'shipments.amount', class: 'align-middle amount'},
                    {data: 'aging', name: 'aging', class: 'align-middle aging',orderable: false, searchable: false}

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

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.aging')) {
                            $(td).appendTo($(search));
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

                    this.api().table().columns.adjust();
                }
            });


            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });

    </script>
@endsection