@extends('admin.layout.master')

@section('title', 'Pickup Report')

@section('content')
    <h1 class="mb-1">
        Pickup Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-1 justify-content-center">
                    <div class="col-12 ">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                        <div class="col-8">
                            <div  class="row">   
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="department" class="select2" id="department" >
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="salesperson" class="select2" id="salesperson" >
                                        @foreach($salespersons as $salesperson)  
                                            <option value="{{ $salesperson->id }}">{{ $salesperson->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="origin" class="select2" id="origin">
                                        @foreach($origins as $origin)
                                            <option value="{{ $origin->id }}">{{ $origin->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="category" class="select2" id="category">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="cut_off_time" class="select2" id="cut_off_time">
                                            
                                            <option value="0">Before</option>
                                            <option value="1">After</option>
                                        
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group pb-1">
                                        <select name="pickup_status" class="select2" id="pickup_status" >
                                            @foreach($pickup_request_statuses as $pickup_request_status)
                                                <option value="{{ $pickup_request_status->id }}">{{ $pickup_request_status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" title="Date From" data-rule-required="true" data-msg-required="Date(From) is required" data-value="{{ Carbon\Carbon::today() }}">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                        </div>
                                        <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" title="Date To" data-rule-required="true" data-msg-required="Date(To) is required" data-value="{{ Carbon\Carbon::today() }}">
                                    </div>
                                </div>

                                <div class="col-2">
                                <!-- <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button> -->
                                    <button type="submit" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>

                            </div>
                        </div>
                        <div class="col-4"> 
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">Legend</legend>
                                <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    @foreach($legends as $legend)
                                         <li><span style="background-color: {{$legend->color}}"></span>{{ $legend->name }}</li> 
                                    @endforeach
                                </ul>
                                </div>
                            </fieldset>
                        </div>
                        </form>
                    </div>
                </div>
                <div class="" id="report_data">
                    <h2 class="mb-1">
                        Summary
                    </h2>
               
                    <div class="row">
                        <input type="hidden" id="cards_filter_input">
                        <div class="col-4">
                            <div class="card pull-up">
                                <div class="card-content border rounded" id="totals">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <span>Total Pickups</span>
                                            </div>
                                            <div class="media-body text-right">
                                            
                                                <h3 id="total">{{$stats['total']}}</h3>
                                             
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                     
                    <div class="row">    
                        <div class="col-4">
                            <div class="card bg-gradient-directional-primary pull-up">
                                <div class="card-content" id="pending_operation">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-left">
                                                <span>Pending Operations</span>
                                            </div>
                                            <div class="media-body  text-right">
                                           
                                                <h3 class="text-white" id="pending_operations">{{$stats['pending_operations']}}</h3>
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>    
                        <div class="col-4">
                            <div class="card bg-gradient-directional-info pull-up">
                                <div class="card-content" id="pending_sale">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-left">
                                                <span>Pending Sales</span>
                                            </div>
                                            <div class="media-body text-right">
                                            
                                                <h3 class="text-white" id="pending_sales">{{$stats['pending_sales']}}</h3>
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                    <div class="row">      
                        <div class="col-4">
                            <div class="card bg-gradient-directional-booked_shipments pull-up">
                                <div class="card-content" id="before_cut_off_times">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-left">
                                                <span>Before Cut-off</span>
                                            </div>
                                            <div class="media-body text-right">
                                            
                                                <h3 class="text-white" id="before_cut_off_time">{{$stats['before_cut_off_time']}}</h3>
                                             
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-gradient-directional-pending_confirmation pull-up">
                                <div class="card-content" id="after_cut_off_times">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-left">
                                                <span>After Cut-off</span>
                                            </div>
                                            <div class="media-body text-right">
                                            
                                                <h3 class="text-white" id="after_cut_off_time">{{$stats['after_cut_off_time']}}</h3>
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                    <div class="row">    
                        <div class="col-4">
                            <div class="card bg-gradient-directional-pending_return pull-up">
                                <div class="card-content" id="attempted_and_pick">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-left">
                                                <span>Attempted & Picked</span>
                                            </div>
                                            <div class="media-body text-right">
                                            
                                                <h3 class="text-white" id="attempted_and_picked">{{$stats['attempted_and_picked']}}</h3>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-gradient-directional-return_delivered pull-up">
                                <div class="card-content " id="attempted_and_not_pick">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-left">
                                                <span>Attempted & Not Picked</span>
                                            </div>
                                            <div class="media-body text-right">
                                            
                                                <h3 class="text-white" id="attempted_and_not_picked">{{$stats['attempted_and_not_picked']}}</h3>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card bg-gradient-directional-red pull-up">
                                <div class="card-content" id="attempted_fail">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="media-body text-white text-left">
                                                <span>Attempted Failed</span>
                                            </div>
                                            <div class="media-body text-right">
                                           
                                                <h3 class="text-white" id="attempted_failed">{{$stats['attempted_failed']}}</h3>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                   
                    <table class="table table-bordered datatable" id="datatable" style="width:100%;z-index: 3;">
                        <thead>
                            <tr role="row" class="bg-primary white">
                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Request ID</th>
                                <th class="border-primary border-darken-1">Requested Date</th>
                                <th class="border-primary border-darken-1">Status</th>
                                <th class="border-primary border-darken-1">Shipper</th>
                                <th class="border-primary border-darken-1">Salesperson</th>
                                <th class="border-primary border-darken-1">Expected Shipment(s)</th>
                                <th class="border-primary border-darken-1">Received Shipments</th>
                                <th class="border-primary border-darken-1">Shipments Difference</th>
                                <th class="border-primary border-darken-1">Department</th>
                                <th class="border-primary border-darken-1">Attempt Date/Time</th>
                                <th class="border-primary border-darken-1">Trax Reason</th>
                                <th class="border-primary border-darken-1">Trax Remarks</th>
                                <th class="border-primary border-darken-1">Attempt Count</th>
                                <th class="border-primary border-darken-1">Contact Person</th>
                                <th class="border-primary border-darken-1">Vendor</th>
                                <th class="border-primary border-darken-1">Contact No(s)</th>
                                <th class="border-primary border-darken-1">Address</th>
                                <th class="border-primary border-darken-1">City</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">
    <style>
        .bg-gradient-directional-inprocess {
            background-image: linear-gradient(45deg, #d6a42a, #ffec07fa);
            background-repeat: repeat-x;
        }
        .show_active{
            -webkit-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -moz-box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            box-shadow: 1px 3px 8px 0px rgba(0,0,0,0.8);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        .bg-gradient-directional-pending_confirmation {
            background-image: linear-gradient(45deg, #6a1fa2 , #ff4961);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-return_delivered {
            background-image: linear-gradient(45deg, #02c123, #99ff12d1);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-pending_return {
            background-image: linear-gradient(45deg, #7d491c  , #e0b668de);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-booked_shipments {
            background-image: linear-gradient(45deg, #5e187b, #ed86ff);
            background-repeat: repeat-x;
        }
        .bg-gradient-directional-arrived_shipments {
            background-image: linear-gradient(45deg, #074077, #2fbef5);
            background-repeat: repeat-x;
        }
        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
             padding: 0 1.4em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
            width:100%;
            -webkit-box-shadow:  0px 0px 0px 0px #000;
                    box-shadow:  0px 0px 0px 0px #000;
        }

        legend.scheduler-border {
            width:auto; 
            border-bottom:none;
        }
        fieldset.scheduler-border .legend-scale ul {
            margin: 0;
            margin-bottom: 10px;
            padding: 0;
            float: left;
            list-style: none;
        }
        fieldset.scheduler-border .legend-scale ul li {
            font-size: 100%;
            list-style: none;
            margin-left: 0;
            line-height: 18px;
            margin-bottom: 3px;
        }
        fieldset.scheduler-border ul.legend-labels li span {
            display: block;
            float: left;
            height: 18px;
            width: 40px;
            margin-right: 5px;
            margin-left: 0;
            /* border: 1px solid #999; */
        }
        span{
            font-size: 15px;
        }

    @foreach($legends as $legend)
        @if($legend->id == 1)
            .attempted_and_picked_less_than_ten{
                background-color: #228B22 ;
                color: #ffffff;
            }
        @elseif($legend->id == 2)
            .attempted_and_picked_greater_than_ten{
                background-color: #98FB98 ;
            }
        @elseif($legend->id == 3)
            .attempt_and_notpicked{
                background-color: #FFDEAD ;
            }
        @elseif($legend->id == 4)
            .cancelled{
                background-color: #D3D3D3 ;
            }
        @elseif($legend->id == 5)
            .attempt_failed{
                background-color: #FA8072 ;
                color: #ffffff;
            }
        @endif
    @endforeach

    </style>

@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
        $(document).ready(function () {
            $('#search_form #department').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Department',
                allowClear:true
            });
            $('#search_form #salesperson').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select SalesPerson',
                allowClear:true
            });
            $('#search_form #origin').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Origin',
                allowClear:true
            });
            $('#search_form #pickup_status').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Status',
                allowClear:true
            });
            $('#search_form #category').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Category',
                allowClear:true
            });
            $('#search_form #cut_off_time').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Select Cut-Off-Time',
                allowClear:true
            });
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
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
                        $('#search_form #from_date').pickadate('picker').set('max', $('#search_form #to_date').pickadate('picker').get('select'));
                    }
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
                            url: '{{ route('admin.reports.pickup_report.list') }}',
                            data: params,
                            success: function (result) {
                                head = [];

                                head.push('S. No.');
                                head.push('Request ID');
                                head.push('Requested Date');
                                head.push('Status');
                                head.push('Shipper');
                                head.push('Salesperson');
                                head.push('Expected Shipment(s)');
                                head.push('Received Shipments');
                                head.push('Shipments Difference');
                                head.push('Department');
                                head.push('Attempt Date/Time');
                                head.push('Trax Reason');
                                head.push('Trax Remarks');
                                head.push('Attempt Count');
                                head.push('Contact Person');
                                head.push('Vendor');
                                head.push('Contact No(s)');
                                head.push('Address');
                                head.push('City');

                                $.each(result.data, function(index, values) {
                                    row = [];

                                    row.push(index + 1);
                                    row.push(values.pickup_request_id);
                                    row.push(values.requested_date);
                                    row.push(values.status);
                                    row.push(values.shipper);
                                    row.push(values.salesperson);
                                    row.push(values.expected_shipments);
                                    row.push(values.received_shipments);
                                    row.push(values.difference_shipments);
                                    row.push(values.department);
                                    row.push(values.attempted_date);
                                    row.push(values.trax_reason);
                                    row.push(values.trax_remarks);
                                    row.push(values.attempted_count);
                                    row.push(values.contact_person);
                                    row.push(values.vendor);
                                    row.push(values.contact_number);
                                    row.push(values.address);
                                    row.push(values.city);
                                    body.push(row);
                                });
                            },
                            async: false
                        });
                        UnblockPagePermanently();

                        return {body: body, header:head};
                }
            });
            
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Pickup Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.pickup_report.list') }}',
                    data: function (d) {
                        d.search_department = $('#department').val();
                        d.search_salesperson = $('#salesperson').val();
                        d.search_origin = $('#origin').val();
                        d.search_category = $('#category').val();
                        d.search_pickup_status = $('#pickup_status').val();
                        d.search_cut_off_time= $('#cut_off_time').val();
                        d.search_date_from = $('input[name="from_date_formatted"]').val();
                        d.search_date_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'pickup_request_id',
                order: [[2, 'desc']],
                columns: [
                    {orderable: false, searchable: false,name: 'serial_number',class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'pickup_request_id' ,name: 'v.id', class: 'align-middle text-center pickup_request_id'},
                    { data:'requested_date' ,name: 'v.created_at', class: 'align-middle requested_date'},
                    { data:'status' ,name: 'vprs.name', class: 'align-middle status'},
                    { data:'shipper' ,name: 'u.name', class: 'align-middle shipper'},
                    { data:'salesperson' ,name: 'a.name', class: 'align-middle salesperson'},
                    { data:'expected_shipments' ,name: 'v2_pickup_reports.expected_shipments', class: 'align-middle expected_shipments'},
                    { data:'received_shipments' ,name: 'v2_pickup_reports.received_shipments', class: 'align-middle received_shipments'},
                    { data:'difference_shipments' ,name: 'v2_pickup_reports.difference_shipments', class: 'align-middle difference_shipments'},
                    { data:'department' ,name: 'ad.name', class: 'align-middle department'},
                    { data:'attempted_date' ,name: 'attempted_date', class: 'align-middle attempted_date'},
                    { data:'trax_reason' ,name: 'trax_reason', class: 'align-middle trax_reason'},
                    { data:'trax_remarks' ,name: 'trax_remarks', class: 'align-middle trax_remarks'},
                    { data:'attempted_count' ,name: 'v.attempts', class: 'align-middle attempted_count'},
                    { data:'contact_person' ,name: 'usi.poc', class: 'align-middle contact_person'},
                    { data:'vendor' ,name: 'usi.vendor', class: 'align-middle vendor'},
                    { data:'contact_number' ,name: 'usi.phone', class: 'align-middle contact_number'},
                    { data:'address' ,name: 'usi.pickup_address', class: 'align-middle address'},
                    { data:'city' ,name: 'ci.name', class: 'align-middle city'},
                    
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                //    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                //     var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                //     var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                //     var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                //     this.api().columns().every(function(column_id) {
                //         var column = this;
                //         var header = column.header();


                //         if ($(header).is('.serial_number') || $(header).is('.booked')  || $(header).is('.received') || $(header).is('.revenue') || $(header).is('.commission') || $(header).is('.commission_amount') || $(header).is('.counts')) {
                //             $(td).appendTo($(search));
                //         }
                //         else {
                //             var current = $(input).appendTo($(search)).on('change', function() {
                //                 column.search($(this).val(), false, false, true).draw();
                //             }).wrap(td).after(icon);

                //             if (column.search()) {
                //                 current.val(column.search());
                //             }
                //         }
                //     });
                //     this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                 table.draw();
            });

            function add_animation(box) {
                 $("#report_data div").removeClass("show_active");
                 box.addClass('show_active');
            }

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                var from_date = $('#search_form input[name="from_date_formatted"]').val();
                var to_date = $('#search_form input[name="to_date_formatted"]').val();
                       
                    $.ajax({
                        url: '{!! route('admin.reports.pickup_report.data') !!}',
                        method: 'post',
                        data: {
                             '_token': '{{ csrf_token() }}',
                             'search_date_from': from_date,
                             'search_date_to': to_date,
                            }
                    }).done(function (data) {
                            if(data.status){
                                $('#total').text(data.stats.total);
                                $('#pending_operations').text(data.stats.pending_operations);
                                $('#pending_sales').text(data.stats.pending_sales);
                                $('#before_cut_off_time').text(data.stats.before_cut_off_time);
                                $('#after_cut_off_time').text(data.stats.after_cut_off_time);
                                $('#attempted_and_picked').text(data.stats.attempted_and_picked);
                                $('#attempted_and_not_picked').text(data.stats.attempted_and_not_picked);
                                $('#attempted_failed').text(data.stats.attempted_failed);
                                table.draw();

                            }else{
                                $('#total').text(0);
                                $('#pending_operations').text(0);
                                $('#pending_sales').text(0);
                                $('#before_cut_off_time').text(0);
                                $('#after_cut_off_time').text(0);
                                $('#attempted_and_picked').text(0);
                                $('#attempted_and_not_picked').text(0);
                                $('#attempted_failed').text(0);
                                table.draw();
                            }
                            UnblockPagePermanently();
                    }); 
                }
            });


        });

    </script>
@endsection