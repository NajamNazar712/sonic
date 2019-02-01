@extends('admin.layout.master')

@section('title', 'Debriefing Report')
@section('content')
    <h1 class="mb-1">
        Debriefing Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">

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
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="form-group input-group col-4">
                        <div class="input-group-prepend">
										<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
											<span class="la la-calendar-o"></span>
										</span>
                        </div>

                        <input type="text" name="search_date" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date" placeholder="Search Date" data-value="{{Carbon\Carbon::now()}}">
                    </div>

                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Hubs</th>
                        <th class="border-primary border-darken-1">Shipment - Pending</th>
                        <th class="border-primary border-darken-1">Shipment - Delivered</th>
                        <th class="border-primary border-darken-1">Shipment - Delivery Unsuccessful</th>
                        <th class="border-primary border-darken-1">Shipment - Not Attempted</th>
                        <th class="border-primary border-darken-1">Shipment - On Hold</th>
                        <th class="border-primary border-darken-1">Shipment - Non Service Area</th>
                        <th class="border-primary border-darken-1">Shipment - Misrouted</th>
                        <th class="border-primary border-darken-1">Shipment - On Hold for Self Collection</th>
                        <th class="border-primary border-darken-1">Return - Confirmation Pending</th>
                        <th class="border-primary border-darken-1">Shipment - Lost</th>
                        <th class="border-primary border-darken-1">Return - Confirm</th>
                        <th class="border-primary border-darken-1">Correct Status</th>
                        <th class="border-primary border-darken-1">Fake Status</th>
                        <th class="border-primary border-darken-1">Total</th>
                        <th class="border-primary border-darken-1">Ratio</th>
                        <th class="border-primary border-darken-1">Delivery Tomorrow</th>
                        <th class="border-primary border-darken-1">Deivery Note Pending Shipment</th>
                        <th class="border-primary border-darken-1">Grand Total</th>
                        <th class="border-primary border-darken-1">Ratio</th>
                    </tr>
                    </thead>
                </table>
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
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_region').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Region',
                width:'100%',
                allowClear:true
            });

            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });

            var date = '{{ Carbon\Carbon::now()}}';
            $('#search_date').pickadate({
                firstDay: 1,
                clear: '',
                max:date,
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
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.lead_time.list') }}',
                        data: {
                            'page': 'all',
                            'search_tracking_no': $('#search_tracking_no').val(),
                            'search_origin': $('#search_origin').val(),
                            'search_destination': $('#search_destination').val(),
                            'search_hub': $('#search_hub').val(),
                            'search_status': $('#search_status').val(),
                            'search_shipper': $('#search_shipper').val(),
                            'search_from': $('input[name="from_date_formatted"]').val(),
                            'search_to': $('input[name="to_date_formatted"]').val()
                        },
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Hubs');
                            head.push('Total No of Shipments');
                            head.push('Shipment Delivered');
                            head.push('Shipment - Delivery Unsuccessful');
                            head.push('Shipment - Not Attempted');
                            head.push('Shipment - On Hold');
                            head.push('Shipment - Non Service Area');
                            head.push('Shipment - Misrouted');
                            head.push('Shipment - On Hold for Self Collection');
                            head.push('Return - Confirmation Pending');
                            head.push('Shipment - Lost');
                            head.push('Return Confirm');
                            head.push('Correct Status');
                            head.push('Fake Status');
                            head.push('Total');
                            head.push('Ratio');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.account_no);
                                row.push(values.cargo_number);
                                row.push(values.cargo_date_time);
                                row.push(values.vendor);
                                row.push(values.shipper);
                                row.push(values.origin);
                                row.push(values.destination);
                                row.push(values.hub);
                                row.push(values.current_status);
                                row.push(values.payment_status);
                                row.push(values.arrival_date);
                                row.push(values.reached_at_destination);
                                row.push(values.transit_tat);
                                row.push(values.junction);
                                row.push(values.first_delivery_note_id);
                                row.push(values.first_attempt);
                                row.push(values.first_status);
                                row.push(values.first_status_date);
                                row.push(values.first_verification);
                                row.push(values.verification_status_date);
                                row.push(values.last_delivery_note_id);
                                row.push(values.last_status);
                                row.push(values.last_status_date);
                                row.push(values.last_verification);
                                row.push(values.last_verification_status_date);
                                row.push(values.attempt_tat);
                                row.push(values.dispatch_tat);
                                row.push(values.delivered_date);
                                row.push(values.delivered_tat);
                                row.push(values.return_confirm);
                                row.push(values.return_cargo_number);
                                row.push(values.return_cargo_date_time);
                                row.push(values.return_reached_at_destination);
                                row.push(values.return_transit_tat);
                                row.push(values.return_delivered_status);
                                row.push(values.return_delivered_date);
                                row.push(values.return_dispatch_tat);
                                row.push(values.return_tat);
                                row.push(values.payment_done_date);
                                row.push(values.payment_tat);
                                row.push(values.total_tat);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );            var index_column = 0;
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Debriefing Report',
                        text:'<i class="la la-file-excel-o"></i> Excel'
                    }
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.debriefing.list') }}',
                    // data: function (d) {
                    // d.search_date = $('input[name="search_date_formatted"]').val(),
                    // d.rider = $('#search_region').val();
                    // d.hub = $('#search_hub').val(),
                    // d.zone = $('#zone_class').val()
                    // }
                },
                // order: [[5, 'desc']],
                // rowId: 'delivery_note_id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'hub', name: 'cities.name', class: 'align-middle hub'},
                    {data: 'pending_shipments', name: 'pending_shipments', class: 'align-middle pending_shipments'},
                    {data: 'delivered_shipments', name: 'delivered_shipments', class: 'align-middle delivered_shipments'},
                    {data: 'unsuccessful_shipments', name: 'unsuccessful_shipments', class: 'align-middle unsuccessful_shipments'},
                    {data: 'notattempted_shipments', name: 'notattempted_shipments', class: 'align-middle notattempted_shipments'},
                    {data: 'onhold_shipments', name: 'onhold_shipments', class: 'align-middle onhold_shipments'},
                    {data: 'nonservicearea_shipments', name: 'nonservicearea_shipments', class: 'align-middle nonservicearea_shipments'},
                    {data: 'misrouted_shipments', name: 'misrouted_shipments', class: 'align-middle misrouted_shipments'},
                    {data: 'selfcollection_shipments', name: 'selfcollection_shipments', class: 'align-middle selfcollection_shipments'},
                    {data: 'returnconfirmationpending_shipments', name: 'returnconfirmationpending_shipments', class: 'align-middle returnconfirmationpending_shipments'},
                    {data: 'lost_shipments', name: 'lost_shipments', class: 'align-middle lost_shipments'},
                    {data: 'returnconfirm_shipments', name: 'returnconfirm_shipments', class: 'align-middle returnconfirm_shipments'},
                    {data: 'correct_status', name: 'correct_status', class: 'align-middle correct_status'},
                    {data: 'fake_status', name: 'fake_status', class: 'align-middle fake_status'},
                    {data: 'total', name: 'total', class: 'align-middle total'},
                    {data: 'total_ratio', name: 'total_ratio', class: 'align-middle total_ratio'},
                    {data: 'tomorrow_shipments', name: 'tomorrow_shipments', class: 'align-middle tomorrow_shipments'},
                    {data: 'dnpending_shipments', name: 'dnpending_shipments', class: 'align-middle dnpending_shipments'},
                    {data: 'grand_total', name: 'grand_total', class: 'align-middle grand_total'},
                    {data: 'grand_total_ratio', name: 'grand_total_ratio', class: 'align-middle grand_total_ratio'}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

        });

    </script>
@endsection