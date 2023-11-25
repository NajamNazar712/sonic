@extends('admin.layout.master')

@section('title', 'Pickups History')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Pickups History
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="container">
                                <div class="row justify-content-center">

                                    <div class="col">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                   <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_from_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_from_date" placeholder="Requested Date From">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                                     <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                            </div>
                                            <input type="text" name="requested_to_date"
                                                   class="form-control bg-primary border-primary white rounded-right"
                                                   id="requested_to_date" placeholder="Requested Date To">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button type="button" id="search_filter_btn"
                                                class="mb-1 btn btn-outline-primary btn-min-width"><i
                                                    class="la la-search" style="margin-right: 10px"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <input type="hidden" value="0" id="status_filter_input" name="pickup_status_id">
                                    <input type="hidden" value="0" id="status_filter_input_reason" name="pickup_reason_id">
                                    <button type="button" class="btn btn-outline-secondary btn-min-width mr-1 mb-1 pickup_status_btn" rel="0">All
                                    </button>
                                   
                                    @foreach($statuses as $id => $status)
                                        <div>
                                          
                                            <button type="button" class="btn btn-outline-secondary btn-min-width mr-1 mb-1 pickup_status_btn" rel="{{ $id }}">{{ $status['name'] }}
                                                ({{ $status['count'] }})
                                            </button>
                                        </div>
                                    @endforeach
                                   
                                    <select class="select select2 mb-1" id="select_status">
                                        <option value="0" selected>All</option>
                                        @foreach ($pickup_reasons as $id => $pickup_reason)
                                             <option value="{{ $id }}">{{  $pickup_reason['name'] }}</option>
                                        @endforeach
                                        
                                    </select>
                                   
                                  {{-- <div class="col">
                                    <div class="form-group">
                                        <select  id="status_filter_input" name="pickup_status_id" class="select2 pickup_status_select" >
                                            @foreach($statuses as $id => $status)
                                                <option value="{{ $id }}" selected="selected">{{ $status['name'] }} ({{ $status['count'] }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                  </div> --}}
                                </div>
                            </div>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    {{-- <th class="border-primary border-darken-1"></th> --}}
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Pickup Request ID</th>
                                    <th class="border-primary border-darken-1">Pickup Date</th>
                                    <th class="border-primary border-darken-1">Ask Time</th>
                                    <th class="border-primary border-darken-1">Shipments/Pieces</th>
                                    <th class="border-primary border-darken-1">Weight (KG)</th>
                                    <th class="border-primary border-darken-1">Additional Services</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Product</th>
                                    <th class="border-primary border-darken-1">Service</th>
                                    <th class="border-primary border-darken-1">Shippment Type</th>
                                    <th class="border-primary border-darken-1">Shipments Picked</th>
                                    <th class="border-primary border-darken-1">Shipper</th>
                                    <th class="border-primary border-darken-1">Address</th>
                                    <th class="border-primary border-darken-1">Special Request</th>
                                    <th class="border-primary border-darken-1">Station</th>
                                    <th class="border-primary border-darken-1">Route Code</th>
                                    <th class="border-primary border-darken-1">Rider</th>
                                    <th class="border-primary border-darken-1">Rider Phone</th>
                                    <th class="border-primary border-darken-1">Assigned Courier</th>
                                    <th class="border-primary border-darken-1">Assigned Courier Phone</th>
                                    <th class="border-primary border-darken-1">Admin Generated</th>
                                    <th class="border-primary border-darken-1">Client Generated</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


   
    <!---- start of show additional services modal---->
    <div class="modal fade text-left" id="AdditionalServiceModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="AdditionalServiceModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title white">Additional Services</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <!-- start additional services -->
                    {{-- <div class="d-flex justify-content-start vh-100 pl-1">
                            <b class="text-dark"> Additional Services </b>
                    </div>--}}
                    {{-- <div class="d-flex justify-content-start align-items-center">
                            <!-- start service list -->
                                <div class="services-list col-12 additionalservices">
                                
                                </div>
                            <!-- end service list -->
                    </div>  --}}
                    <table class="table table-bordered">
                            <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Service</th>
                                        <th class="border-primary border-darken-1">Qty</th>
                                    </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    <!-- end additional services -->

                    
                </div>
            </div>
        </div>
        </div>
    </div>
    <!----end of show additional services modal --->

    <!---- start of show Shipment Arrived   modal---->
    <div class="modal fade text-left" id="ShipmentPickedModal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="ShipmentPickedModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Shipments Picked</h4>
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
                                            {{--<th class="border-primary border-darken-1">Rider</th>
                                            <th class="border-primary border-darken-1">Global Rider</th> --}}
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
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <style>
        .btn-min-width {
            min-width: 5.5rem;
        }

        .legends {
            cursor: pointer;
        }

        .custom-nav {
           margin-left: 4px;
        }

        .custom-nav li {

           border: 1px solid #CCCCCC;
           border-radius: 4px;
        }

        .custom-nav li:first-child {

           margin-right: 4px !important;
        }

        .custom-nav li:last-child {

           margin-left: 4px !important;
        }

        .custom-nav .nav-item a.nav-link {

            color: #CCCCCC;
            border: 1px solid #CCCCCC !important;
        }

        .custom-nav .nav-item p {

            line-height: 1.4;
        }

        .custom-nav .nav-item a.active {

            /* color: #64A0D2 !important; */
            border: 1px solid #64A0D2 !important;
            /* background-color: #F7FAFC !important; */
            color: #fff!important;
            background: #5587b4!important;
        }

        .custom-nav .nav-item a:hover {
            color: #64A0D2 !important;
            border: 1px solid #64A0D2 !important;
            background-color: #F7FAFC !important;
        }

        /* start scheduled days area */

        /* Hide checkboxes */
        .scheduled_days_area input[type="checkbox"] {
        display: none;
        }

        /* Style labels for checkboxes */
        .scheduled_days_area input + label {
        display: inline-block;
        border: 1px solid #CCCCCC;
        background: #fff;
        padding: 5px 1px;
        color: #A3A3A3;
        border-radius: 5px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s;
        }

        /* Style the checkbox's unchecked state */
        .scheduled_days_area input:checked + label {
            background: #5587b4;
            border-color: #64A0D2;
            color: #fff;

        }

        /* Style the checkbox's unchecked state icon */
        .scheduled_days_area input:checked + label:before {
        font-size: 17px;
        position: absolute;
        left: 24px;
        top: 6px;
        opacity: 1;
        }

        /* .scheduled_days_area input + label:hover {
            background: #fff;
            border-color: #CCCCCC;
            color: #000;
        } */

        .scheduled_days_area input:not(:checked) + label:hover {
            background: #F7FAFC;
            border-color: #64A0D2;
            color: #64A0D2;
        }

        .scheduled_days_area .item-column {
            margin-right: 10px;
        }

        /* end scheduled days area */

        /* start addition services */

        .service-item {

            border: 1px solid #CCCCCC;
            border-radius: 4px;
            padding-top: 12px;
            padding-bottom: 12px;

        }

        .btn-service {
            border-radius: 50%;
            padding: 4px;
            width: 30px;
            height: 30px;
            transition: all 0.4s;
        }

        .btn-service:hover {

            background: #6496BE !important;
        }

        .btn-service:active,
        .btn-service:focus {

            background: #6496BE !important;
        }

        .service-item .custom-input-number[type="number"]::-webkit-inner-spin-button,
        .service-item .custom-input-number[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            appearance: none;
            margin: 0;
        }

        /* end addition services */
        .delay_time{
            background-color: #8fc5ea;
            /* background-color: #9fa1ae; */
            color: white;
            /* background-color: #FF0000; */
            /* background-color: #FFA500; */
        }
        .status_tab_active{
            color: #fff;
            background-color: #649bc8;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script>


        $(document).ready(function () {

            // $('#status_filter_input').prepend('<option value="0" selected="selected">All</option>').select2({
            //     placeholder: 'Select Status',
            //     width: '30%',
            //     // dropdownParent:$('#add_pickup_request')
            // });

            $('#select_status').select2({
                width: '15%',
                placeholder: 'Select Status*'
            });

            var booking_from_date = $('#requested_from_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#requested_to_date').pickadate('picker').set('min', $('#requested_from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var booking_to_date = $('#requested_to_date').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                // format: 'dd mmmm, yyyy',
                format: 'yyyy-mm-dd',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
                    }
                }
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.v3_pickups.history.list') }}',
                        data: params,
                        success: function (result) {
                          
                            head = [];

                            head.push('S.No');
                            head.push('Pickup Request ID');
                            head.push('Pickup Date');
                            head.push('Ask Time');
                            head.push('Shipments/Pieces');
                            head.push('Weight (KG)');
                            head.push('Additional Services');
                            head.push('Status');
                            head.push('Product');
                            head.push('Service');
                            head.push('Shipment Type');
                            head.push('Shipper');
                            head.push('Contact Person');
                            head.push('Contact No(s).');
                            head.push('Address');
                            head.push('City');
                            head.push('Route Code');
                            head.push('Route Rider');
                            head.push('Route Rider Phone');
                            head.push('Assigned Courier');
                            head.push('Assigned Courier Phone');
                     
                            // head.push('Current Rider');
                            // head.push('Last Rider');
                            // head.push('Pickup Note ID');
                            // head.push('Shipment(s) Booked');
                            // head.push('Shipment(s) Rider Picked');
                            // head.push('Shipment(s) Received');
                        
                            // head.push('Territory');
                         
                            // head.push('Vendor');
                            // head.push('Brand Name');
                        
                        
                            // head.push('Trax Reason');
                            // head.push('Trax Remark(s)');
                            // head.push('Shipper Remark(s)');
                            // head.push('Rider Remark(s)');
                            // head.push('Assigned Date');
                            // head.push('Attempt Date');
                            // head.push('Aging');
                            // head.push('Attempt(s)');


                            $.each(result.data, function (index, values) {
                            
                                row = [];

                                row.push(index + 1);
                                row.push(values.pickup_request_id);
                                row.push(values.pickup_date);
                                row.push(values.time_range);
                                // row.push(values.last_rider);
                                // row.push(values.pickup_note_id);
                                // row.push(values.booked);
                                // row.push(values.shipments_rider_picked);
                                // row.push(values.received);
                                row.push(values.shipment_pieces);
                                row.push(values.weight);
                                row.push(values.services_count);
                                row.push(values.status);
                                row.push(values.product);
                                row.push(values.service);
                                row.push(values.shippment_type);
                                row.push(values.shipper);
                                // row.push(values.territory);
                                row.push(values.contact_person);
                                // row.push(values.vendor_name);
                                // row.push(values.brand_name);
                                row.push(values.contact_number);
                                row.push(values.address);
                                row.push(values.city);
                                row.push(values.route_code);
                                row.push((values.rider_id!=null?values.rider_id + '-' + values.rider_name:''));
                                row.push(values.rider_phone);
                                row.push((values.current_rider_id !=null?values.current_rider_id + '-' + values.current_rider:''));
                                row.push(values.current_rider_phone);
                                // row.push(values.pickup_status);
                                // row.push(values.trax_reason);
                                // row.push(values.trax_remarks);
                                // row.push(values.shipper_remarks);
                                // row.push(values.rider_remarks);
                                // row.push(values.assigned_date);
                                // row.push(values.attempted_date);
                                // row.push(values.aging);
                                // row.push(values.attempts);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });


            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                @if (session('role_id') == 1 || count(array_intersect([18, 19], session('permissions'))) !== 0)

                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pickups History',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                @else
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Pending Pickups Requests',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'
                ],
                @endif
                scrollX: true, scrollY: '500px',
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.v3_pickups.history.list') }}',
                    data: function (d) {
                        d.before_cut_off_time = $('#search_filter').val();
                        d.requested_from_date = $('#requested_from_date').val();
                        d.requested_to_date = $('#requested_to_date').val();
                        d.pickup_status_id = $('#status_filter_input').val();
                        d.pickup_reason_id = $('#status_filter_input_reason').val();
                    }
                },
                rowId: 'id',
                order: [[2, 'desc']],
                columns: [
                    // {
                    //     data: 'id',
                    //     orderable: false,
                    //     searchable: false,
                    //     class: 'text-center align-middle select select-checkbox p-1',
                    //     targets: 0,
                    //     render: function (data, type, row) {
                    //         return '';
                    //     }
                    // },
                    {
                        data: 'serial_number',
                        orderable: false,
                        searchable: false,
                     
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'pickup_request_id', name: 'pickup_request_id', class: 'align-middle pickup_request_id'},
                    {data: 'pickup_date', name: 'pickup_date', class: 'align-middle pickup_date',render:function(data,type,row){
                        console.log(row.pickup_date);
                        return row.pickup_date;
                    }},
                    {data: 'time_range', name: 'time_range', class: 'align-middle ask_time'},
                    {data: 'shipment_pieces', name: 'shipment_pieces', class: 'align-middle shipments', orderable: false},
                    {data: 'weight', name: 'weight', class: 'align-middle weight'},
                    {data: 'services_count_btn', name: 'services_count', class: 'align-middle text-center services_count'},
                    {data: 'status', name: 'status', class: 'align-middle text-center status', orderable: false, searchable: false},
                    {data: 'product', name: 'product', class: 'align-middle product'},
                    {data: 'service', name: 'service', class: 'align-middle service'},
                    {data: 'shippment_type', name: 'shippment_type', class: 'align-middle text-center shippment_type'},
                    {data: 'shipments_picked_btn',name:'shipments_picked_btn',class: 'align-middle shipments_picked'},
                    {data: 'shipper', name: 'shipper', class: 'align-middle shipper',render:function(data,type,row){
                        return row.user_id +'-'+ row.shipper;
                    }},
                    
                    {data: 'address', name: 'address', class: 'align-middle address'},
                    {data: 'special_request', name: 'special_request', class: 'align-middle special_request'},

                    {data: 'hub', name: 'hub', class: 'align-middle station'},
                    {data: 'route_code', name: 'route_code', class: 'align-middle route_code'},
                    {data: 'rider_id', name: 'rider_id', class: 'align-middle rider_id',
                    render: function (data, type, row) {
                            if (row.rider_id !== null) {
                                return row.rider_id + ' - ' + row.rider_name;
                            } else {
                                return ''; 
                            }
                        }
                    },
                    {data: 'rider_phone', name: 'rider_phone', class: 'align-middle rider_phone'},
                    {data: 'current_rider', name: 'current_rider', class: 'align-middle current_rider',
                      render:function(data,type,row){
                        if(row.current_rider_id!==null){
                            return row.current_rider_id + ' - ' + row.current_rider;
                        }else{
                            return '';
                        }
                      }
                
                    },
                    {data: 'current_rider_phone', name: 'current_rider_phone', class: 'align-middle current_rider_phone'},
                  
                    {
                        data:'adminname',name:'adminname',class:'align-middle adminname',orderable: false,
                        render:function(data,type,row){
                            return row.adminname != null ?  row.adminname : '';
                        }
                    },
                    {
                        data:'username',name:'username',class:'align-middle username',orderable: false,
                        render:function(data,type,row){
                            return row.username != null ?  row.username : '';
                        }
                    },
                 
                
                   

                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        
                        // if ($(header).is('.action') || $(header).is('.select') || $(header).is('.serial_number') || $(header).is('.shipments') || $(header).is('.status') || $(header).is('.trax_reason') || $(header).is('.trax_remarks') || $(header).is('.shipper_remarks') || $(header).is('.attempted_date') || $(header).is('.action') || $(header).is('.rider_remarks') || $(header).is('.brand_name') || $(header).is('.all_remarks')) {
                        //     $(td).appendTo($(search));
                        // } else {
                        //     var current = $(input).appendTo($(search)).on('change', function () {
                        //         column.search($(this).val(), false, false, true).draw();
                        //     }).wrap(td).after(icon);

                        //     if (column.search()) {
                        //         current.val(column.search());
                        //     }
                        // }
                    });

                    this.api().table().columns.adjust();
                }

            });

            
                $('#search_filter_btn').on('click', function () {
                    table.draw(true);
                });


                $('body').on('click', 'button.pickup_status_btn', function(){
                        var status_id = $(this).attr('rel');
                        $('.pickup_status_btn').removeClass('status_tab_active');
                        $(this).addClass('status_tab_active');
                $('#status_filter_input').val(status_id);
                    table.draw();
                });
                $("body").on('change','#select_status',function(){
                        var status_id=$(this).val();
                        $('#status_filter_input_reason').val(status_id);
                        table.draw();
                });

                $('body').on('click','button.shipments_picked_btn',function(){
                    var pickup_request_id=$(this).closest('tr').attr('id');
                    var tracking_route='{{ route('admin.tracking.index') }}';
                    $("#ShipmentPickedModal tbody").empty();
                    $.ajax({
                        url:'{{ route('admin.v3_pickups.pending.shipment_picked_detail') }}',
                        method:'POST',
                        data:{
                            'pickup_request_id':pickup_request_id,
                            '_token':'{{ csrf_token() }}'
                        }
                    }).done(function(data){
                        if(data.status==1){
                                $.each(data.shipment_picked_detail ,function(key,value){
                                    $("#ShipmentPickedModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle tracking_number"><u><a href="'+tracking_route+'?tracking_number='+value.tracking_number+'" class"tracking" target="_blank">'+value.tracking_number+'</a></u></td></tr>');
                                });
                                $("#ShipmentPickedModal").modal('show');
                        }
                    });

                });
                
       

            $('.apply-checked').change(function() {

                if ($(this).is(':checked')) {

                    $(this).attr('checked', 'checked');

                } else {

                    $(this).removeAttr('checked');
                }
            });

        });
        
        function showadditionalservices(event,pickup_request_id){
            $("#AdditionalServiceModal table tbody").empty();
            $.ajax({
                url:'{{ route('admin.v3_pickups.pending.pickup_request_services') }}',
                method:'POST',
                data:{
                    'pickup_request_id':pickup_request_id,
                    '_token':'{{ csrf_token() }}'
                }
            }).done(function(data){
                if (data.status == 0) {
                    // console.log(data);
                    // var pickup_request_services=data.additional_services[0].pickup_request_services;
                            $.each(data.pickup_request_services,function(key,value) {
                            //   $(".additionalservices").append('<div class="d-flex align-items-center service-item mt-md-1"><div class="col-md-6"><p class="mb-0 text-dark">'+value.service_name+' </p></div> <div class="col-md-6"><div class="form-group input-group mb-0"><input type="text" value='+value.count+' class="form-control text-center" readonly></div></div> </div>')
                              $("#AdditionalServiceModal table tbody").append('<tr id="8" role="row" class="odd"><td class=" align-middle status">'+(key+1)+'</td><td class=" align-middle service_name">'+value.service_name+'</td><td class=" align-middle service_count">'+value.count+'</td></tr>')
                            });
                        
                        } else {
                            toastr.error('No Additional Service found!', 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                         });
                }
            })
           $("#AdditionalServiceModal").modal("show");
        }
    </script>
@endsection