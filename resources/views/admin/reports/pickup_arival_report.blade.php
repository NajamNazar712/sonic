@extends('admin.layout.master')

@section('title', 'Pickup versus Arrival Report')


@section('content')
    <h1 class="mb-1">
        Pickup versus Arrival Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                               
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-5">
                        <fieldset class="form-group">
                            <select name="search_rider" id="search_rider" class="form-control select2">
                           
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
                            <th class="border-primary border-darken-1">Shipper</th>
                            {{-- <th class="border-primary border-darken-1">Address</th> --}}
                            {{-- <th class="border-primary border-darken-1">Created Shipments</th>
                            <th class="border-primary border-darken-1">Not Picked Shipments</th> --}}
                            <th class="border-primary border-darken-1">Rider Picked</th>
                            <th class="border-primary border-darken-1">No. of Arrived Shipments</th>
                            <th class="border-primary border-darken-1">Balance Shipments</th>
                              {{-- <th class="border-primary border-darken-1">Rider</th> --}}

                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="modal fade" id="shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="shipments_modal_title"></h4>

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
    </div> --}}
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

<!---- start of show Created Shipment modal---->
<div class="modal fade text-left" id="ShipmentModal" data-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="ShipmentModal"
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
                                        <th class="border-primary border-darken-1">Tracking No</th>
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
<!----end of show Created Shipment modal --->

<div class="modal fade text-left" id="NotPickedShipmentModal" data-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="NotPickedShipmentModal"
aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white">Not Pick Shipments</h4>
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
                                        <th class="border-primary border-darken-1">Tracking No</th>
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
<!---- start of show Rider Detail  modal---->
<div class="modal fade text-left" id="RiderdetailModal" data-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="RiderdetailModal"
aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white">Rider Pickups</h4>
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
                                        <th class="border-primary border-darken-1">Tracking No</th>
                                        <th class="border-primary border-darken-1">Picked Rider</th>
                                        <th class="border-primary border-darken-1">Pickup Request Id</th>
                                        <th class="border-primary border-darken-1">Assigned Rider</th>
                                        <th class="border-primary border-darken-1">Pickup Request Date</th>
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
<!----end of show Rider Detail modal --->

<!---- start of show Shipment Arrived   modal---->
<div class="modal fade text-left" id="ShipmentArrivedModal" data-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="ShipmentArrivedModal"
aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white">Shipment Arrived</h4>
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
                                        <th class="border-primary border-darken-1">Rider</th>
                                        <th class="border-primary border-darken-1">Global Rider</th>
                                        <th class="border-primary border-darken-1">Tracking No</th>
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
<!----end of show  Shipment Arrived modal --->


<!---- start of show Balance Shipment   modal---->
<div class="modal fade text-left" id="BalanceShipmentModal" data-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="BalanceShipmentModal"
aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white">Balance Shipment</h4>
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
                                        <th class="border-primary border-darken-1">Rider</th>
                                        <th class="border-primary border-darken-1">Tracking No</th>
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
<!----end of show Balance Shipment  modal --->

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

    <script type="text/javascript">
        $(document).ready(function () {
            // $('#datatable_wrapper').hide();
            
            

            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
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
             

            $('#search_filter_btn').on('click',function () {

                $('#datatable_wrapper').show();
                var table = $('#datatable').DataTable({
                    dom: '<"d-inline-block"l><"pull-right"B>tipr',
                    scrollX: true, scrollY: '500px',
                    buttons: [
                        // {
                        //     extend: 'excelHtml5',
                        //     title: 'Rider Wise Pickup Report',   
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
                        url: '{{ route('admin.reports.pickup_arival.list') }}',
                        data: function (d) {
                            d.search_hub = $('#search_hub').val();
                            d.search_rider = $('#search_rider').val();
                            d.search_from = $('input[name="from_date_formatted"]').val();
                            d.search_to = $('input[name="to_date_formatted"]').val();
                        }
                    },
                    rowId: 'shipper_id',
                    order: [[1, 'desc']],
                    columns: [
                        {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                        { data:'shipper_id' ,name: 'shipper_id', class: 'align-middle text-center shipper_id',render:function(data,type,row){
                            return row.shipper_id +'-'+ row.shipper_name;
                        }},
                        // { data:'address_btn', class: 'align-middle text-center shipper_address', orderable: false, searchable: false},
                        { data:'rider_picked_btn',class: 'align-middle text-center rider_picked', orderable: false, searchable: false},
                        { data:'shipment_arrived_btn', class: 'align-middle text-center shipment_arrived', orderable: false, searchable: false},
                        { data:'shipment_balance_btn', class: 'align-middle text-center shipment_balance', orderable: false, searchable: false},

                    ],
                    rowCallback: function(row, data, index) {
                        var info = table.page.info();
                        $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    },
                    initComplete: function() {
                        this.api().table().columns.adjust();
                    },
                    footerCallback: function(row, data, start, end, display) {
                        var scanned_shipments_count = 0;
                        var arrived_shipments_count = 0;
                        var without_scan_shipments_count = 0;
                        
                        $.each(data, function(index, shipment_data) {
                            scanned_shipments_count += shipment_data.shipments_scanned_by_rider;
                            arrived_shipments_count += shipment_data.total_arrived_shipments;
                            without_scan_shipments_count += shipment_data.without_scan_shipments;
                        });
                        var api = this.api();
                        api.columns('.date', {
                            page: 'current'
                        }).every(function() {
                            $(this.footer()).html('Total');
                        });
                        
                        api.columns('.scanned_shipments', {
                            page: 'current'
                        }).every(function() {
                            
                            $(this.footer()).html(scanned_shipments_count);
                        });
                        api.columns('.arrived_shipments', {
                            page: 'current'
                        }).every(function() {
                            $(this.footer()).html(arrived_shipments_count);
                        });
                        api.columns('.without_scan_shipments', {
                            page: 'current'
                        }).every(function() {
                        
                            $(this.footer()).html(without_scan_shipments_count);
                        });
                    }
                });
                // table.draw();
            });

        
            
        });
        
        $("body").on('click','.rider_picked_btn',function(){
            // 
            $("#RiderdetailModal table tbody").empty();
            var shipper_id=$(this).parent('td').parent('tr').attr('id');
            var from_date=$("#from_date").val();
            var to_date=$("#to_date").val();
            $.ajax({
                url:'{{ route('admin.reports.pickup_arival.rider_details') }}',
                method:'POST',
                data:{
                    'shipper_id':shipper_id,
                    'from_date':from_date,
                    'to_date':to_date,
                    '_token':'{{ csrf_token() }}'
                }
            }).done(function(data){
               if(data.status==1){
                    $.each(data.rider_details ,function(key,value){
                        if(value.arrived_status!=2){
                            $("#RiderdetailModal table tbody").append('<tr id="8" role="row" class="odd pending_pickups"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle tracking_number">'+value.tracking_number+'</td><td class=" align-middle rider">'+value.picked_rider_id+'-'+value.picked_rider_name+'</td><td class=" align-middle pickup_request_id">'+(value.pickup_request_id!=null?value.pickup_request_id:'')+'</td><td class=" align-middle assigned_rider">'+(value.assigned_rider_id!=null?value.assigned_rider_id+'-'+value.assigned_rider_name:'')+'</td><td class=" align-middle pickup_request_id">'+(value.pickup_date!=null?value.pickup_date:'')+'</td></tr>');

                        }else{
                            $("#RiderdetailModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle tracking_number">'+value.tracking_number+'</td><td class=" align-middle picked_rider">'+value.picked_rider_id+'-'+value.picked_rider_name+'</td><td class=" align-middle pickup_request_id">'+(value.pickup_request_id!=null?value.pickup_request_id:'')+'</td><td class=" align-middle assigned_rider">'+(value.assigned_rider_id!=null?value.assigned_rider_id+'-'+value.assigned_rider_name:'')+'</td><td class=" align-middle pickup_request_id">'+(value.pickup_date!=null?value.pickup_date:'')+'</td></tr>');
                        }
                    });
                    $("#RiderdetailModal").modal('show');
               }
            });
        });
        $("body").on('click','.shipment_arrived_btn',function(){
            $("#ShipmentArrivedModal table tbody").empty();
            var shipper_id=$(this).parent('td').parent('tr').attr('id');
            var from_date=$("#from_date").val();
            var to_date=$("#to_date").val();
            $.ajax({
                url:'{{ route('admin.reports.pickup_arival.arrived_shipments') }}',
                method:'POST',
                data:{
                    'shipper_id':shipper_id,
                    'from_date':from_date,
                    'to_date':to_date,
                    '_token':'{{ csrf_token() }}'
                }
            }).done(function(data){
               if(data.status==1){
                    $.each(data.arrived_shipments ,function(key,value){
                        $("#ShipmentArrivedModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle rider">'+(value.rider_id!=null?value.rider_id+'-'+value.rider_name:'')+'</td><td class=" align-middle global_rider">'+(value.global_rider_id!=null?value.global_rider_id+'-'+value.global_rider_name:'')+'</td><td class=" align-middle tracking_number">'+value.tracking_number+'</td></tr>');
                    });
                    $("#ShipmentArrivedModal").modal('show');
               }
            });
        });
        
        $("body").on('click','.shipment_balance_btn',function(){
            $("#BalanceShipmentModal table tbody").empty();
            var shipper_id=$(this).parent('td').parent('tr').attr('id');
            var from_date=$("#from_date").val();
            var to_date=$("#to_date").val();
            $.ajax({
                url:'{{ route('admin.reports.pickup_arival.balance_Shipments') }}',
                method:'POST',
                data:{
                    'shipper_id':shipper_id,
                    'from_date':from_date,
                    'to_date':to_date,
                    '_token':'{{ csrf_token() }}'
                }
            }).done(function(data){
               if(data.status==1){
                    $.each(data.balance_Shipments ,function(key,value){
                        $("#BalanceShipmentModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle rider">'+(value.id==null?'':value.id +'-'+ value.name)+'</td><td class=" align-middle tracking_number">'+value.tracking_number+'</td></tr>');
                    });
                    $("#BalanceShipmentModal").modal('show');
               }
            });
        });
        

    </script>
@endsection
