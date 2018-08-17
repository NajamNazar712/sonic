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
                    // var old_date_formatted = $('input[name="search_date_formatted"]').val();
                    // to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                }
            });


            $('#search_filter_btn').on('click',function () {
                var table = '';
                var search_date = $('input[name="search_date_formatted"]').val();
                if(search_date != ''){
                    $.ajax({
                        url: '{!! route('admin.reports.qa.list') !!}',
                        method: 'POST',
                        data: {
                            'search_date': search_date,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        console.log(data);
                        // if(table != ''){
                        //     table.clear();
                        // }
                        // $('#qa_table').html('');
                        // $('#datatable').removeClass('nodisplay');

                        // table.columns().eq(0).each( function ( index ) {
                        //     var column = table.column( index );
                        //
                        //     var data = column.data();
                        //     // ... do something with data(), or column.nodes(), etc
                        // } );
                        var shipment = '';
                        shipment += '<table class="table table-bordered datatable " id="datatable" style="z-index: 3;">\n' +
                            '                    <thead>\n' +
                            '                    <tr class="bg-primary white">\n' +
                            '\n' +
                            '                        <th class="border-primary border-darken-1" rowspan="2">Stations</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Parcel Pending for Cargo</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Cargo In Transit</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Pending Deliveries</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Receive Deliveries</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Return Marked</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Confirmed Returns</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Return Cargo In Transit</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Return Pending for Delivery</th>\n' +
                            '                        <th class="border-primary border-darken-1" colspan="2">Return Return Note</th>\n' +
                            '                        <th class="border-primary border-darken-1" rowspan="2">Grand Total</th>\n' +
                            '\n' +
                            '                    </tr>\n' +
                            '                    <tr role="row" class="bg-primary white">\n' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>\n' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>\n' +
                            '                        <th class="border-primary border-darken-1">Pending</th>\n' +
                            '                        <th class="border-primary border-darken-1">Resolved</th>' +
                            '                    </tr>';

                        $.each(data,function (id,details) {
                            shipment += '<tr>';
                            shipment += '<td class="align-middle stations">'+id+'</td>';
                            shipment += '<td class="align-middle cargo_pending">'+details.cargo_pending+'</td>';
                            shipment += '<td class="align-middle cargo_resolved">'+details.cargo_resolved+'</td>';
                            shipment += '<td class="align-middle cargo_transit_pending">'+details.cargo_transit_pending+'</td>';
                            shipment += '<td class="align-middle cargo_transit_resolved">'+details.cargo_transit_resolved+'</td>';
                            shipment += '<td class="align-middle deliveries_pending">'+details.deliveries_pending+'</td>';
                            shipment += '<td class="align-middle deliveries_resolved">'+details.deliveries_resolved+'</td>';
                            shipment += '</tr>';
                        });
                        shipment += '</thead>\n' +
                            '                </table>';
                        $('#qa_table').html(shipment);
                        table = $('#datatable').DataTable({
                            dom: 't',
                            order: [[0, 'desc']],
                            columns: [
                                {name: 'stations', class: 'align-middle stations'},
                                {name: 'cargo_pending', class: 'align-middle cargo_pending'},
                                {name: 'cargo_resolved', class: 'align-middle cargo_resolved'},
                                {name: 'cargo_transit_pending', class: 'align-middle cargo_transit_pending'},
                                {name: 'cargo_transit_resolved', class: 'align-middle cargo_transit_resolved'},
                                {name: 'deliveries_pending', class: 'align-middle deliveries_pending'},
                                {name: 'deliveries_resolved', class: 'align-middle deliveries_resolved'},
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