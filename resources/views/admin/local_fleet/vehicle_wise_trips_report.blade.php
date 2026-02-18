@extends('admin.layout.master')

@section('title', 'Daily Activity Report')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row"></div>

            <div class="content-body">
                <h1 class="mb-1">Daily Activity Report</h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <form id="track_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                <div class="col-4 mt-2">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                      <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                      </span>
                                        </div>
                                        <input type="text" name="booking_from_date"
                                               class="form-control bg-primary border-primary white rounded-right"
                                               id="booking_from_date" placeholder=" Date From">
                                    </div>
                                </div>
                                <div class="col-4 mt-2">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                      <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o small-calender-icon"></span>
                                      </span>
                                        </div>
                                        <input type="text" name="booking_to_date"
                                               class="form-control bg-primary border-primary white rounded-right"
                                               id="booking_to_date" placeholder="Date To">
                                    </div>
                                </div>
                                <div class="col-4 mt-2">
                                    <div class="form-group">
                                        <button id="datatable_filter_btn" type="submit" class=" btn btn-outline-primary btn-min-width"><i
                                                    class="la la-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Date</th>
                                    <th class="border-primary border-darken-1">Vehicle No</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Make</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Vehicle Type</th>
                                    <th class="border-primary border-darken-1">Capacity</th>
                                    <th class="border-primary border-darken-1">Total Trips Day Wise</th>
                                    <th class="border-primary border-darken-1">Fuel Liters per day</th>
                                    <th class="border-primary border-darken-1">Total DN</th>
                                    <th class="border-primary border-darken-1">Total DN Shipments</th>
                                    <th class="border-primary border-darken-1">Total DN Shipment Weight</th>
                                    <th class="border-primary border-darken-1">Total RN </th>
                                    <th class="border-primary border-darken-1">Total RN Shipments</th>
                                    <th class="border-primary border-darken-1">Total RN Shipment Weight</th>
                                    <th class="border-primary border-darken-1">Total Pickup's </th>
                                    <th class="border-primary border-darken-1">Total Pickup's Shipments</th>
                                    <th class="border-primary border-darken-1">Total Pickup's Shipment Weight</th>
                                    <th class="border-primary border-darken-1">Total Trips Cost</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ViewDetailsModal" data-backdrop="static" tabindex="-1" role="dialog"
             aria-labelledby="ViewDetailsModal" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    <div class="modal-header text-center">
                        <h4 class="modal-title w-100 font-weight-bold">
                            Daily Activity – Remarks & Costs
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- AJAX CONTENT WILL LOAD HERE -->
                        <div class="text-center text-muted">
                            Loading...
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .short-text, .full-text {
            white-space: normal;
            word-break: break-word;
        }
        .show-more {
            margin-left: 5px;
            font-size: 12px;
            color: #007bff;
            cursor: pointer;
        }
        .selectize-control {
            width:  100%  !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {

            var booking_from_date = $('#booking_from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#booking_from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #booking_to_date').pickadate('picker').set('min', $('#track_form #booking_from_date').pickadate('picker').get('select'));
                    }
                }
            });

            var booking_to_date = $('#booking_to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#booking_to_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #booking_from_date').pickadate('picker').set('max', $('#track_form #booking_to_date').pickadate('picker').get('select'));
                    }
                }
            });
            // ------------------------------------------
            // DATATABLE
            // ------------------------------------------
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    'reset'
                ],
                lengthMenu: [[50,100,500,1000,-1],[50,100,500,1000,'All']],
                pageLength: 50,
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                scrollY:'300px',
                scrollX:'100%',
                ajax: {
                    url: "{{ route('admin.cargo.supply_chain.local_fleet.vehicle.consolidated_trips.list') }}",
                    type: "GET",
                    data: function (d) {
                        d.from_date = $('input[name="booking_from_date_formatted"]').val();
                        d.to_date   = $('input[name="booking_to_date_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'trip_date', name: 'local_fleet_vehicle_trips.trip_date', class: 'align-middle trip_date'},
                    {data: 'vehicle_number', name: 'local_fleet_vehicles.vehicle_number', class: 'align-middle vehicle_number'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'make', name: 'local_fleet_vehicles.make', class: 'align-middle make', orderable: false},
                    {data: 'vendor_name', name: 'local_fleet_vehicles.vendor_name', class: 'align-middle vendor_name', orderable: false},
                    {data: 'vehicle_type', name: 'local_fleet_vehicles.vehicle_type', class: 'align-middle vehicle_type', orderable: false},
                    {data: 'capacity', name: 'local_fleet_vehicles.capacity', class: 'align-middle capacity', orderable: false},
                    {data: 'total_trips', name: 'total_trips', class: 'text-center align-middle total_trips',searchable: false},
                    {data: 'fuel_liters_day', name: 'fuel_liters_day', class: 'align-middle fuel_liters_day',searchable: false},
                    {data: 'total_dns', name: 'total_dns', class: 'text-center align-middle total_dns',searchable: false},
                    {data: 'total_dn_shipments', name: 'total_dn_shipments', class: 'text-center align-middle total_dn_shipments',searchable: false},
                    {data: 'total_dn_shipment_weight', name: 'total_dns', class: 'text-center align-middle total_dn_shipment_weight',searchable: false},
                    {data: 'total_rns', name: 'total_rns', class: 'text-center align-middle total_rns',searchable: false},
                    {data: 'total_rn_shipments', name: 'total_rn_shipments', class: 'text-center align-middle total_rn_shipments',searchable: false},
                    {data: 'total_rn_shipment_weight', name: 'total_rn_shipment_weight', class: 'text-center align-middle total_rn_shipment_weight',searchable: false},
                    {data: 'total_pickup_count', name: 'total_pickup_count', class: 'text-center align-middle total_pickup_count',searchable: false},
                    {data: 'total_pickup_shipments', name: 'total_pickup_shipments', class: 'text-center align-middle total_pickup_shipments',searchable: false},
                    {data: 'total_pickup_shipment_weight', name: 'total_pickup_shipment_weight', class: 'text-center align-middle total_pickup_shipment_weight',searchable: false},
                    {data: 'total_trip_cost', name: 'total_trip_cost', class: 'align-middle total_trip_cost',searchable: false},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
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

                        if (($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.total_trips') || $(header).is('.fuel_liters_day') || $(header).is('.total_dns') || $(header).is('.total_dn_shipments') || $(header).is('.total_dn_shipment_weight')  || $(header).is('.total_rns')  || $(header).is('.total_rn_shipments') || $(header).is('.total_rn_shipment_weight') || $(header).is('.total_pickup_count') || $(header).is('.total_pickup_shipments') || $(header).is('.total_pickup_shipment_weight') || $(header).is('.total_trip_cost'))) {
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


            $('body').on('click', '.view-day-remarks', function () {

                let vehicle_id = $(this).data('vehicle');
                let trip_date  = $(this).data('date');

                let url = "{{ route('admin.cargo.supply_chain.local_fleet.vehicle.consolidated_trips.cost_details') }}";

                $('#ViewDetailsModal .modal-body').html(
                    '<div class="text-center text-muted">Loading...</div>'
                );

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        vehicle_id: vehicle_id,
                        trip_date: trip_date,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {

                        if (res.status !== 0) {
                            toastr.error('Failed to load remarks');
                            return;
                        }

                        let html = '';
                        html += '<table class="table table-sm datatable text-center">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<th>#</th>';
                        html += '<th>Type</th>';
                        html += '<th>Remarks</th>';
                        html += '<th>Cost</th>';
                        html += '<th>Receipt</th>';
                        html += '<th>Date</th>';
                        html += '</tr>';
                        html += '</thead><tbody>';

                        if (res.data.length === 0) {
                            html += '<tr><td colspan="6">No data found</td></tr>';
                        } else {
                            res.data.forEach(function (r, index) {
                                html += '<tr>';
                                html += '<td>' + (index + 1) + '</td>';
                                html += '<td>' + r.type + '</td>';
                                html += '<td>' + (r.remarks ?? '-') + '</td>';
                                html += '<td>' + (r.cost ?? '-') + '</td>';
                                html += '<td>' +
                                    (r.receipt
                                        ? '<a href="' + r.receipt + '" target="_blank">View</a>'
                                        : '-') +
                                    '</td>';
                                html += '<td>' + r.date + '</td>';
                                html += '</tr>';
                            });
                        }

                        html += '</tbody></table>';

                        $('#ViewDetailsModal .modal-body').html(html);
                        $('#ViewDetailsModal').modal('show');
                    },
                    error: function () {
                        toastr.error('Server error occurred');
                    }
                });
            });

            $('#track_form').bind('submit',function (e) {
                e.preventDefault();
                var booking_from_date = $('#track_form #booking_from_date').val();
                var booking_to_date = $('#track_form #booking_to_date').val();
                if ((booking_from_date != '' && booking_to_date != '')) {
                    table.draw();
                }
            });

        });
    </script>

@endsection
