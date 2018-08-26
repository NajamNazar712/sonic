@extends('admin.layout.master')

@section('content')
    <h1 class="mb-1">
       QA Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">

                    <div class="col-3">
                        <fieldset class="form-group">
                            <input type="text" name="search_date" class="form-control bg-primary border-primary white rounded-right" id="search_date" placeholder="Search Date" data-value="">
                        </fieldset>
                    </div>

                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <div id="qa_table"></div>
                {{--<table class="table table-bordered datatable nodisplay" id="datatable" style="z-index: 3;">--}}
                    {{--<thead>--}}
                    {{--<tr role="row" class="bg-primary white">--}}

                        {{--<th class="border-primary border-darken-1" >Stations</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Parcel Pending for Cargo</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Cargo In Transit</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Pending Deliveries</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Receive Deliveries</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Return Marked</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Confirmed Returns</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Return Cargo In Transit</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Return Pending for Delivery</th>--}}
                        {{--<th class="border-primary border-darken-1" colspan="2">Return Return Note</th>--}}
                        {{--<th class="border-primary border-darken-1" rowspan="2">Grand Total</th>--}}

                    {{--</tr>--}}
                    {{--<tr role="row" class="bg-primary white">--}}


                        {{--<th class="border-primary border-darken-1"></th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}
                        {{--<th class="border-primary border-darken-1">Pending</th>--}}
                        {{--<th class="border-primary border-darken-1">Resolved</th>--}}

                    {{--</tr>--}}
                    {{--</thead>--}}
                {{--</table>--}}
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .nodisplay{
            display: none;
        }
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
            border-color: #666EE8;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #666ee8;
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
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            var from_max = '{{ Carbon\Carbon::yesterday()}}';

            var from_date = $('#search_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: from_max,
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_date_root').css('top','40px');
                },
                onSet: function(context) {
                }
            });


            $('#search_filter_btn').on('click',function () {
                // var table = '';

                var search_date = $('input[name="search_date_formatted"]').val();
                if(search_date !== ''){
                    $.ajax({
                        url: '{!! route('admin.reports.qa.list') !!}',
                        method: 'POST',
                        data: {
                            'search_date': search_date,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        var shipment = '';
                        var cargo_pending = 0;
                        var cargo_resolved = 0;
                        var cargo_transit_pending = 0;
                        var cargo_transit_resolved = 0;
                        var deliveries_pending = 0;
                        var deliveries_resolved = 0;
                        var receive_deliveries_pending = 0;
                        var receive_deliveries_resolved = 0;
                        var return_marked_pending = 0;
                        var return_marked_resolved = 0;
                        var return_confirmed_pending = 0;
                        var return_confirmed_resolved = 0;
                        var return_cargo_pending = 0;
                        var return_cargo_resolved = 0;
                        var return_delivery_pending = 0;
                        var return_delivery_resolved = 0;
                        var return_receive_pending = 0;
                        var return_receive_resolved = 0;
                        var total_pendings = 0;
                        var grand_total_pendings = 0;
                        var total_resolved = 0;
                        var grand_total_resolved = 0;
                        var total_unresolved = 0;
                        var grand_total_unresolved = 0;
                        shipment += '<table class="table table-bordered datatable " id="datatable" style="z-index: 3;">\n' +
                            '                    <thead>\n' +
                            '                    <tr class="bg-primary white">\n' +
                            '\n' +
                            '                        <th class="border-primary border-darken-1" rowspan="2">Stations</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Parcel Pending for Cargo</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Cargo In Transit</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Pending Deliveries</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Receive Delivery Note</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Return Marked</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Confirmed Returns</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Return Cargo In Transit</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Return Pending for Delivery</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Receive Return Note</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="3">Grand Total</th>\n' +
                            '\n' +
                            '                    </tr>\n' +
                            '                    <tr role="row" class="bg-primary white">\n' +

                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>\n' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>\n' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>' +                                                    '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>' +
                            '                        <th class="border-primary border-darken-1">Total Pendings</th>' +
                            '                        <th class="border-primary border-darken-1">Total Resolved</th>' +
                            '                        <th class="border-primary border-darken-1">Total Unresolved</th>' +
                            '                    </tr></thead>';

                        $.each(data,function (id,details) {
                            total_pendings = details.cargo_pending + details.cargo_transit_pending + details.deliveries_pending + details.receive_deliveries_pending + details.return_marked_pending + details.return_confirmed_pending + details.return_cargo_pending + details.return_delivery_pending + details.return_receive_pending;
                            total_resolved = details.cargo_resolved + details.cargo_transit_resolved + details.deliveries_resolved + details.receive_deliveries_resolved + details.return_marked_resolved + details.return_confirmed_resolved + details.return_cargo_resolved + details.return_delivery_resolved + details.return_receive_resolved;
                            total_unresolved = total_pendings - total_resolved;
                            grand_total_pendings += total_pendings;
                            grand_total_resolved += total_resolved;
                            grand_total_unresolved += total_unresolved;
                            shipment += '<tbody><tr>';
                            shipment += '<td class="align-middle stations">'+id+'</td>';
                            shipment += '<td class="align-middle cargo_pending">'+details.cargo_pending+'</td>';
                            cargo_pending += details.cargo_pending;
                            shipment += '<td class="align-middle cargo_resolved">'+details.cargo_resolved+'</td>';
                            cargo_resolved += details.cargo_resolved;
                            shipment += '<td class="align-middle cargo_transit_pending">'+details.cargo_transit_pending+'</td>';
                            cargo_transit_pending += details.cargo_transit_pending;
                            shipment += '<td class="align-middle cargo_transit_resolved">'+details.cargo_transit_resolved+'</td>';
                            cargo_transit_resolved += details.cargo_transit_resolved;
                            shipment += '<td class="align-middle deliveries_pending">'+details.deliveries_pending+'</td>';
                            deliveries_pending += details.deliveries_pending;
                            shipment += '<td class="align-middle deliveries_resolved">'+details.deliveries_resolved+'</td>';
                            deliveries_resolved += details.deliveries_resolved;
                            shipment += '<td class="align-middle receive_deliveries_pending">'+details.receive_deliveries_pending+'</td>';
                            receive_deliveries_pending += details.receive_deliveries_pending;
                            shipment += '<td class="align-middle receive_deliveries_resolved">'+details.receive_deliveries_resolved+'</td>';
                            receive_deliveries_resolved += details.receive_deliveries_resolved;
                            shipment += '<td class="align-middle return_marked_pending">'+details.return_marked_pending+'</td>';
                            return_marked_pending += details.return_marked_pending;
                            shipment += '<td class="align-middle return_marked_resolved">'+details.return_marked_resolved+'</td>';
                            return_marked_resolved += details.return_marked_resolved;
                            shipment += '<td class="align-middle return_confirmed_pending">'+details.return_confirmed_pending+'</td>';
                            return_confirmed_pending += details.return_confirmed_pending;
                            shipment += '<td class="align-middle return_confirmed_resolved">'+details.return_confirmed_resolved+'</td>';
                            return_confirmed_resolved += details.return_confirmed_resolved;
                            shipment += '<td class="align-middle return_cargo_pending">'+details.return_cargo_pending+'</td>';
                            return_cargo_pending += details.return_cargo_pending;
                            shipment += '<td class="align-middle return_cargo_resolved">'+details.return_cargo_resolved+'</td>';
                            return_cargo_resolved += details.return_cargo_resolved;
                            shipment += '<td class="align-middle return_delivery_pending">'+details.return_delivery_pending+'</td>';
                            return_delivery_pending += details.return_delivery_pending;
                            shipment += '<td class="align-middle return_delivery_resolved">'+details.return_delivery_resolved+'</td>';
                            return_delivery_resolved += details.return_delivery_resolved;
                            shipment += '<td class="align-middle return_receive_pending">'+details.return_receive_pending+'</td>';
                            return_receive_pending += details.return_receive_pending;
                            shipment += '<td class="align-middle return_receive_resolved">'+details.return_receive_resolved+'</td>';
                            return_receive_resolved += details.return_receive_resolved;
                            shipment += '<td class="align-middle return_receive_resolved">'+total_pendings+'</td>';
                            shipment += '<td class="align-middle total_resolved">'+total_resolved+'</td>';
                            shipment += '<td class="align-middle total_unresolved">'+total_unresolved+'</td>';
                            shipment += '</tr>';
                        });
                        shipment += '<tr>';
                        shipment += '<td class="align-middle stations bg-primary white" rowspan="2">Grand Total</td>';
                        shipment += '<td class="align-middle cargo_pending bg-primary white">'+cargo_pending+'</td>';
                        shipment += '<td class="align-middle cargo_resolved bg-primary white">'+cargo_resolved+'</td>';
                        shipment += '<td class="align-middle cargo_transit_pending bg-primary white">'+cargo_transit_pending+'</td>';
                        shipment += '<td class="align-middle cargo_transit_resolved bg-primary white">'+cargo_transit_resolved+'</td>';
                        shipment += '<td class="align-middle deliveries_pending bg-primary white">'+deliveries_pending+'</td>';
                        shipment += '<td class="align-middle deliveries_resolved bg-primary white">'+deliveries_resolved+'</td>';
                        shipment += '<td class="align-middle receive_deliveries_pending bg-primary white">'+receive_deliveries_pending+'</td>';
                        shipment += '<td class="align-middle receive_deliveries_resolved bg-primary white">'+receive_deliveries_resolved+'</td>';
                        shipment += '<td class="align-middle return_marked_pending bg-primary white">'+return_marked_pending+'</td>';
                        shipment += '<td class="align-middle return_marked_resolved bg-primary white">'+return_marked_resolved+'</td>';
                        shipment += '<td class="align-middle return_confirmed_pending bg-primary white">'+return_confirmed_pending+'</td>';
                        shipment += '<td class="align-middle return_confirmed_resolved bg-primary white">'+return_confirmed_resolved+'</td>';
                        shipment += '<td class="align-middle return_cargo_pending bg-primary white">'+return_cargo_pending+'</td>';
                        shipment += '<td class="align-middle return_cargo_resolved bg-primary white">'+return_cargo_resolved+'</td>';
                        shipment += '<td class="align-middle return_delivery_pending bg-primary white">'+return_delivery_pending+'</td>';
                        shipment += '<td class="align-middle return_delivery_resolved bg-primary white">'+return_delivery_resolved+'</td>';
                        shipment += '<td class="align-middle return_receive_pending bg-primary white">'+return_receive_pending+'</td>';
                        shipment += '<td class="align-middle return_receive_resolved bg-primary white">'+return_receive_resolved+'</td>';
                        shipment += '<td class="align-middle grand_total_pendings bg-primary white">'+grand_total_pendings+'</td>';
                        shipment += '<td class="align-middle grand_total_resolved bg-primary white">'+grand_total_resolved+'</td>';
                        shipment += '<td class="align-middle grand_total_unresolved bg-primary white">'+grand_total_unresolved+'</td>';
                        shipment += '</tr>';
                        shipment += '<tr>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((cargo_pending != 0? (cargo_resolved/cargo_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((cargo_transit_pending !=0? (cargo_transit_resolved/cargo_transit_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((deliveries_pending !=0?(deliveries_resolved/deliveries_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((receive_deliveries_pending !=0?(receive_deliveries_resolved/receive_deliveries_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((return_marked_pending !=0?(return_marked_resolved/return_marked_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((return_confirmed_pending != 0? (return_confirmed_resolved/return_confirmed_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((return_cargo_pending!=0? (return_cargo_resolved/return_cargo_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((return_delivery_pending !=0? (return_delivery_resolved/return_delivery_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((return_receive_pending !=0? (return_receive_resolved/return_receive_pending):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">100%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+((grand_total_pendings != 0?(grand_total_resolved/grand_total_pendings):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '<td class="align-middle stations bg-primary white">'+(((grand_total_resolved+grand_total_pendings) != 0? grand_total_unresolved/(grand_total_resolved+grand_total_pendings):0) * 100).toPrecision(2)+'%</td>';
                        shipment += '</tr>';
                        shipment += '</tbody></table>';
                        $('#qa_table').html(shipment);
                        var table = $('#datatable').DataTable({
                            "scrollX": true,
                            "dom":'t',
                            // dom: '<"d-inline-block"><"pull-right"B>t',
                            // buttons: [
                            //     {
                            //         extend: 'excelHtml5',
                            //         title: 'Lead Time Report',
                            //         exportOptions: {
                            //             columns: ':visible',
                            //             format: {
                            //                 body: function ( data, row, column, node ) {
                            //                     return (column == 0)? row+1:data;
                            //                 }
                            //             }
                            //         }
                            //     },
                            //     {
                            //         extend: 'print',
                            //         exportOptions: {
                            //             columns: ':visible',
                            //             format: {
                            //                 body: function ( e, dt, column, node ) {
                            //                     return (column == 0)? dt+1:e;
                            //                 }
                            //             }
                            //         }
                            //     },
                            // ],
                            order: [[0, 'asc']],
                            // "aoColumns": [{ "bSortable": false },{ "bSortable": false }],
                            columns: [
                                {name: 'stations', class: 'align-middle stations'},
                                {name: 'cargo_pending', class: 'align-middle cargo_pending'},
                                {name: 'cargo_resolved', class: 'align-middle cargo_resolved'},
                                {name: 'cargo_transit_pending', class: 'align-middle cargo_transit_pending'},
                                {name: 'cargo_transit_resolved', class: 'align-middle cargo_transit_resolved'},
                                {name: 'deliveries_pending', class: 'align-middle deliveries_pending'},
                                {name: 'deliveries_resolved', class: 'align-middle deliveries_resolved'},
                                {name: 'receive_deliveries_pending', class: 'align-middle receive_deliveries_pending'},
                                {name: 'receive_deliveries_resolved', class: 'align-middle receive_deliveries_resolved'},
                                {name: 'return_marked_pending', class: 'align-middle return_marked_pending'},
                                {name: 'return_marked_resolved', class: 'align-middle return_marked_resolved'},
                                {name: 'return_confirmed_pending', class: 'align-middle return_confirmed_pending'},
                                {name: 'return_confirmed_resolved', class: 'align-middle return_confirmed_resolved'},
                                {name: 'return_cargo_pending', class: 'align-middle return_cargo_pending'},
                                {name: 'return_cargo_resolved', class: 'align-middle return_cargo_resolved'},
                                {name: 'return_delivery_pending', class: 'align-middle return_delivery_pending'},
                                {name: 'return_delivery_resolved', class: 'align-middle return_delivery_resolved'},
                                {name: 'return_receive_pending', class: 'align-middle return_receive_pending'},
                                {name: 'return_receive_resolved', class: 'align-middle return_receive_resolved'},
                                {name: 'grand_total_pendings', class: 'align-middle grand_total_pendings'},
                                {name: 'grand_total_resolved', class: 'align-middle grand_total_resolved'},
                                {name: 'grand_total_unresolved', class: 'align-middle grand_total_unresolved'}
                            ]
                        });
                        // if ( ! $.fn.DataTable.isDataTable( '#datatable' ) ) {
                        //
                        // }else{
                        //     console.log(table)
                        //
                        // }

                    });
                }



            });

        });

    </script>
@endsection