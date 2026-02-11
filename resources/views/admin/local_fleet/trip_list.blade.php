@extends('admin.layout.master')

@section('title', 'Vehicle Trips')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row"></div>

            <div class="content-body">
                <h1 class="mb-1">Vehicle Trips</h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Vehicle No</th>
                                    <th class="border-primary border-darken-1">Make</th>
                                    <th class="border-primary border-darken-1">Date</th>
                                    <th class="border-primary border-darken-1">City</th>
                                    <th class="border-primary border-darken-1">Vendor</th>
                                    <th class="border-primary border-darken-1">Out Time</th>
                                    <th class="border-primary border-darken-1">In Time</th>
                                    <th class="border-primary border-darken-1">Out Meter</th>
                                    <th class="border-primary border-darken-1">In Meter</th>
                                    <th class="border-primary border-darken-1">Mileage</th>
                                    <th class="border-primary border-darken-1">Fuel Liters</th>
                                    <th class="border-primary border-darken-1">Total DN</th>
                                    <th class="border-primary border-darken-1">Total RN</th>
                                    <th class="border-primary border-darken-1">Total Pickup's</th>
                                    <th class="border-primary border-darken-1">Total Cost</th>
                                    <th class="border-primary border-darken-1">Incident Report</th>
                                    <th class="border-primary border-darken-1">Action</th>
                                </thead>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="TripCostModal" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">

                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title">Trip Cost Details</h4>
                    </div>

                    <div class="modal-body">

                        <h5 class="text-primary">OUT Cost</h5>
                        <p><strong>Amount:</strong> <span id="out_amount">-</span></p>
                        <p><strong>Remarks:</strong> <span id="out_remarks">-</span></p>
                        <p><strong>Date:</strong> <span id="out_date">-</span></p>
                        <p><strong>Receipt:</strong>
                            <a href="#" target="_blank" id="out_receipt" style="display:none">View</a>
                        </p>

                        <hr>

                        <h5 class="text-success">IN Cost</h5>
                        <p><strong>Amount:</strong> <span id="in_amount">-</span></p>
                        <p><strong>Remarks:</strong> <span id="in_remarks">-</span></p>
                        <p><strong>Date:</strong> <span id="in_date">-</span></p>
                        <p><strong>Receipt:</strong>
                            <a href="#" target="_blank" id="in_receipt" style="display:none">View</a>
                        </p>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {


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
                ajax: "{{ route('admin.cargo.supply_chain.local_fleet.vehicle.trips.list') }}",
                rowId: 'id',
                order: [[3, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'vehicle_number', name: 'local_fleet_vehicles.vehicle_number', class: 'align-middle vehicle_number'},
                    {data: 'make', name: 'local_fleet_vehicles.make', class: 'align-middle make', orderable: false},
                    {data: 'created_at', name: 'local_fleet_vehicle_trips.created_at', class: 'align-middle created_at'},
                    {data: 'city_name', name: 'c.name', class: 'align-middle city_name'},
                    {data: 'vendor_name', name: 'local_fleet_vehicles.vendor_name', class: 'align-middle vendor_name', orderable: false},
                    {data: 'out_time', name: 'local_fleet_vehicle_trips.out_time', class: 'align-middle out_time',searchable: false},
                    {data: 'in_time', name: 'local_fleet_vehicle_trips.in_time', class: 'align-middle in_time',searchable: false},
                    {data: 'out_meter', name: 'local_fleet_vehicle_trips.out_meter', class: 'align-middle out_meter'},
                    {data: 'in_meter', name: 'local_fleet_vehicle_trips.in_meter', class: 'align-middle in_meter'},
                    {data: 'mileage', name: 'local_fleet_vehicle_trips.mileage', class: 'align-middle mileage'},
                    {data: 'fuel_liters', name: 'local_fleet_vehicle_trips.fuel_liters', class: 'align-middle fuel_liters'},
                    {data: 'total_dn_count', name: 'local_fleet_vehicle_trips.total_dn_count', class: 'text-center align-middle total_dn_count'},
                    {data: 'total_rn_count', name: 'local_fleet_vehicle_trips.total_rn_count', class: 'text-center align-middle total_rn_count'},
                    {data: 'total_pickup_count', name: 'local_fleet_vehicle_trips.total_pickup_count', class: 'text-center align-middle total_pickup_count'},
                    {data: 'total_trip_cost', name: 'local_fleet_vehicle_trips.total_trip_cost', class: 'align-middle total_trip_cost'},
                    {data: 'incident_report', name: 'local_fleet_vehicle_trips.incident_report', class: 'align-middle incident_report', orderable: false, searchable: false},
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

                        if (($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.out_time') || $(header).is('.in_time') || $(header).is('.incident_report'))) {
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


            $('body').on('click', '.view-trip-cost', function () {

                var trip_id = $(this).closest('tr').attr('id');

                if (!trip_id) {
                    toastr.error('Trip ID not found');
                    return;
                }
                var url = "{{ route('admin.cargo.supply_chain.local_fleet.vehicle.trips.cost_detail', ':id') }}";
                url = url.replace(':id', trip_id);

                $.get( url,
                    function (res) {

                        if (res.status !== 0) {
                            toastr.error('Failed to load cost details');
                            return;
                        }

                        // OUT
                        $('#out_amount').text(res.data.out.amount);
                        $('#out_remarks').text(res.data.out.remarks);
                        $('#out_date').text(res.data.out.date);

                        if (res.data.out.receipt) {
                            $('#out_receipt')
                                .attr('href', res.data.out.receipt)
                                .show();
                        } else {
                            $('#out_receipt').hide();
                        }

                        // IN
                        $('#in_amount').text(res.data.in.amount);
                        $('#in_remarks').text(res.data.in.remarks);
                        $('#in_date').text(res.data.in.date);

                        if (res.data.in.receipt) {
                            $('#in_receipt')
                                .attr('href', res.data.in.receipt)
                                .show();
                        } else {
                            $('#in_receipt').hide();
                        }

                        $('#TripCostModal').modal('show');
                    }
                );
            });
            $('body').on('click', '.show-more', function () {

                var $cell = $(this).closest('td');

                $cell.find('.short-text').toggleClass('d-none');
                $cell.find('.full-text').toggleClass('d-none');

                $(this).text(
                    $(this).text() === 'Show more' ? 'Show less' : 'Show more'
                );
            });

        });
    </script>

@endsection
