@extends('admin.layout.master')

@section('title', 'Utilization of Sack Bag')


@section('content')
    <h1 class="mb-1">
        Utilization of Sack Bag 
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">
                    {{-- <div class="col-4"></div> --}}
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_origin" id="search_origin" class="form-control select2">
                                @foreach ($origins as $origin)
                                    <option value="{{ $origin->id }}"> {{ $origin->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    {{-- <div class="col-4"></div> --}}
                    {{-- <div class="col-4">
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
                    </div> --}}
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <div id="datatable_wrapper">
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            {{-- <th class="border-primary border-darken-1">Date</th>  --}}
                            <th class="border-primary border-darken-1">Destination</th>
                            {{-- <th class="border-primary border-darken-1">Address</th> --}}
                            <th class="border-primary border-darken-1">Stock Sack Bag</th>
                            <th class="border-primary border-darken-1">Re-used Sack Bag</th>
                            {{-- <th class="border-primary border-darken-1">Rider Picked</th>
                            <th class="border-primary border-darken-1">No. of Arrived Shipments</th>
                            <th class="border-primary border-darken-1">Balance Shipments</th> --}}
                              {{-- <th class="border-primary border-darken-1">Rider</th> --}}

                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!---- start of show Address modal---->
    <div class="modal fade text-left" id="AddressModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="AddressModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white">Shipper Booking Address</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <table class="table table-bordered">
                            <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Address</th>
                                        <th class="border-primary border-darken-1">Booked Shipments</th>
                                    </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                </div>
            </div>
        </div>
        </div>
    </div>
<!----end of show Address modal --->


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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
        .pending_pickups{
            background-color: #d63b3b;
            color: white;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            // $('#datatable_wrapper').hide();
            
            

            
            $('#search_origin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Destination',
                width:'100%',
                allowClear:true
            });

            var max = '{{ Carbon\Carbon::now() }}';

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #to_date').pickadate('picker').set('min', $('#search_form #from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #from_date').pickadate('picker').set('min', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
                }
            });

            // $('#datatable').append("<tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>");
           
            var table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    scrollX: true, scrollY: '500px',
                    deferLoading:0,
                    buttons: [
                        // {
                        //     extend: 'excelHtml5',
                        //     title: 'Shipment Report',   
                        //     className: 'btn btn-primary excel',
                        //     text:'<i class="la la-file-excel-o"></i> Excel',
                        //     footer: true
                        // },
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
                        url: '{{ route('admin.reports.sack_bag_utilization.list') }}',
                        data: function (d) {
                            d.destination_id = $('#search_origin').val();
                            d.search_from = $('input[name="from_date_formatted"]').val();
                            d.search_to = $('input[name="to_date_formatted"]').val();
                        }
                    },
                    rowId: 'destination_id',
                    order: [[1, 'desc']],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        // { data:'address_btn', class: 'align-middle text-center shipper_address', orderable: false, searchable: false},
                        
                        { data:'destination_name',class: 'align-middle text-center destination_name', orderable: false, searchable: false},
                        { data:'stock_sack_bag',class: 'align-middle text-center stock_sack_bag', orderable: false, searchable: false},
                        { data:'re_used_sack_bag',class: 'align-middle text-center re_used_sack_bag', orderable: false, searchable: false}
                        // { data:'total_sack_bag',class: 'align-middle text-center total_sack_bag', orderable: false, searchable: false},
        
                    ],
                    rowCallback: function(row, data, index) {
                        var info = table.page.info();
                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    },
                    initComplete: function() {
                        this.api().table().columns.adjust();
                    },
                    // footerCallback: function(row, data, start, end, display) {
                    //     var scanned_shipments_count = 0;
                    //     var arrived_shipments_count = 0;
                    //     var without_scan_shipments_count = 0;
                        
                    //     $.each(data, function(index, shipment_data) {
                    //         scanned_shipments_count += shipment_data.shipments_scanned_by_rider;
                    //         arrived_shipments_count += shipment_data.total_arrived_shipments;
                    //         without_scan_shipments_count += shipment_data.without_scan_shipments;
                    //     });
                    //     var api = this.api();
                    //     api.columns('.date', {
                    //         page: 'current'
                    //     }).every(function() {
                    //         $(this.footer()).html('Total');
                    //     });
                        
                    //     api.columns('.scanned_shipments', {
                    //         page: 'current'
                    //     }).every(function() {
                            
                    //         $(this.footer()).html(scanned_shipments_count);
                    //     });
                    //     api.columns('.arrived_shipments', {
                    //         page: 'current'
                    //     }).every(function() {
                    //         $(this.footer()).html(arrived_shipments_count);
                    //     });
                    //     api.columns('.without_scan_shipments', {
                    //         page: 'current'
                    //     }).every(function() {
                        
                    //         $(this.footer()).html(without_scan_shipments_count);
                    //     });
                    // }
                });
            $('#search_filter_btn').on('click',function () {
                var city_id=$("#search_origin").val();
                if(city_id==''){
                    toastr.error('Please Select Destination', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }else{
                    $('#datatable_wrapper').show();
                    table.draw();
                }
            });
        
            
        });

    


    </script>
@endsection
