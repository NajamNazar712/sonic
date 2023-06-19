@extends('admin.layout.master')
@section('title','RCP Agent Productivity')


@section('content')
    <h1 class="mb-1">
        RCP Agent Productivity
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                    <div class="col-12">
                        <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                            <div class="col-4">
                                <fieldset class="form-group pb-1">
                                    <select name="search_agent[]" id="search_agent" class="form-control select2"   multiple="multiple" requireddata-rule-required="true" data-msg-required="This field is required">
                                        @foreach($agents as $agent)
                                            <option value="{{$agent->id}}">{{$agent->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <fieldset class="form-group pb-1">
                                    <select name="search_hub" id="search_hub" class="form-control select2" requireddata-rule-required="true" data-msg-required="This field is required">
                                        @foreach($hubs as $hub)
                                            <option value="{{$hub->id}}">{{$hub->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-4">
                                <fieldset class="form-group pb-1">
                                    <select name="search_category" id="search_category" class="form-control select2" requireddata-rule-required="true" data-msg-required="This field is required">
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            {{-- <div class="col-4">
                                <fieldset class="form-group pb-1">
                                    <select name="search_shipper" id="search_shipper" class="form-control select2" requireddata-rule-required="true" data-msg-required="Shipper is required">
                                        @foreach($shippers as $shipper)
                                        <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div> --}}
                            <div class="col-4">
                                <div class="form-group input-group  pb-1">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" data-rule-required="true" data-msg-required="Date(From) is required" data-value="{{$today}}">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group input-group pb-1">
                                    <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                                    </div>
                                    <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" data-rule-required="true" data-msg-required="Date(To) is required" data-value="{{$today}}">
                                </div>
                            </div>
                            
                            <div class="col-4">
                                <div class="form-group pb-1">
                                    <button type="submit" class="btn btn-outline-info btn-min-width"><i class="la la-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="report_data">
                    <div class="row">
                       
                        <div class="col-3">
                            <div class="card pull-up cursor-pointer">
                                <div class="card-content border rounded" id="total_main">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-grid font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-right">
                                                <h3 id="total">0</h3>
                                                <span>Total Assigned</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card bg-gradient-directional-primary pull-up cursor-pointer">
                                <div class="card-content" id="completed_main">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-hourglass text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="completed">0</h3>
                                                <span>Total Completed</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      
                        <div class="col-3">
                            <div class="card bg-gradient-directional-info pull-up cursor-pointer">
                                <div class="card-content" id="rcp_reattempt_main">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-layers text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="rcp_reattempt">0</h3>
                                                <span class="font-13">Re-Attempt RCP</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3" id=" ">
                            <div class="card bg-gradient-directional-booked_shipments pull-up cursor-pointer">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-grid text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="total_return_confirm">0</h3>
                                                <span>Total Return Confirm</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-3">
                            <div class="card bg-gradient-directional-out_for_delivery pull-up cursor-pointer">
                                <div class="card-content" id="">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-hourglass text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="on_hold_for_sc">0</h3>
                                                <span>Total Mark For Self Collection</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card bg-gradient-directional-success pull-up cursor-pointer">
                                <div class="card-content" id="productivity_main">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-check text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="productivity">0</h3>
                                                <span>Productivity %</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card bg-gradient-directional-return_confirm pull-up cursor-pointer">
                                <div class="card-content" id="">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-hourglass text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="total_unresponsive_in_percent">0</h3>
                                                <span> Unresponsive %</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card bg-gradient-directional-pending_shipments pull-up cursor-pointer">
                                <div class="card-content" id="">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-layers text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="unresponsive_return">0</h3>
                                                <span class="font-13">Total Unresponsive</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="card bg-gradient-directional-pending_confirmation pull-up cursor-pointer">
                                <div class="card-content" id="">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <div class="align-self-center">
                                                <i class="icon-check text-white font-large-2 float-left"></i>
                                            </div>
                                            <div class="media-body text-white text-right">
                                                <h3 class="text-white" id="total_intercept">0</h3>
                                                <span>Total Intercept</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Agent Name</th>
                        <th class="border-primary border-darken-1">Agent Category</th>
                        <th class="border-primary border-darken-1">Start Time</th>
                        <th class="border-primary border-darken-1">End Time</th>
                        <th class="border-primary border-darken-1">Total Assigning</th>
                        <th class="border-primary border-darken-1">Actual Productivity</th>
                        <th class="border-primary border-darken-1">Reattempt</th>
                        <th class="border-primary border-darken-1">Return</th>
                        <th class="border-primary border-darken-1">Intercept</th>
                        <th class="border-primary border-darken-1">On Hold For Self Collection</th>
                        <th class="border-primary border-darken-1">Pending</th>
                        <th class="border-primary border-darken-1">Already Updated</th>
                        <th class="border-primary border-darken-1">Unresponsive Return</th>
                        <th class="border-primary border-darken-1">Productivity(%)</th>
                    </tr>
                    </thead>
                </table>

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

        
        .bg-gradient-directional-booked_shipments {
            background-image: linear-gradient(45deg, #5e187b, #ed86ff);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-out_for_delivery {
            background-image: linear-gradient(45deg, #ff9819, #fff824);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_shipments {
            background-image: linear-gradient(45deg, #39546d, #90929a);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_confirmation {
            background-image: linear-gradient(45deg, #6a1fa2, #ff4961);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-delivered {
            background-image: linear-gradient(45deg, #076500, #11f118);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_confirm {
            background-image: linear-gradient(45deg, #ff0c0c, #ff9191);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-pending_return {
            background-image: linear-gradient(45deg, #7d491c, #e0b668de);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-return_delivered {
            background-image: linear-gradient(45deg, #02c123, #99ff12d1);
            background-repeat: repeat-x;
        }

        .bg-gradient-directional-cancelled_shipments {
            background-image: linear-gradient(45deg, #ff6a00, #ffb74c);
            background-repeat: repeat-x;
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
    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_agent').select2({
                width:'100%',
                placeholder:"Select Agent",
                allowClear:true,
            });
            $('#search_hub').prepend('<option value="" selected></option>').select2({
                width:'100%',
                placeholder:"Select Hub",
                allowClear:true,
            });
            $('#search_category').prepend('<option value="" selected></option>').select2({
                width:'100%',
                placeholder:"Select Category",
                allowClear:true,
            });
            // $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
            //     width: '100%',
            //     placeholder: 'Select Shipper*',
            //     allowClear:true,
            // });
            var today = '{{ $today }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                // min: new Date(thirtydays),
                max : new Date(today),
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
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(29, 'days');
                    to_date.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    // to_date.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    to_date.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max : new Date(today),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                }
            });

            // get_rcp_cards_data();
            

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.rcp_agent.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Agent Name');
                            head.push('Agent Category');
                            head.push('Start Time');
                            head.push('End Time');
                            head.push('Total Assigning');
                            head.push('Actual Productivity');
                            head.push('Reattempt');
                            head.push('Return');
                            head.push('Intercept');
                            head.push('On Hold for Self Collection');
                            head.push('Pending');
                            head.push('Already Updated');
                            head.push('Unresponsive Return');
                            head.push('Productivity(%)');


                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.agent_name);
                                row.push(values.agent_category); 
                                row.push(values.start_time);
                                row.push(values.end_time);
                                row.push(values.total_assigning);
                                row.push(values.actual_productivity);
                                row.push(values.reattempt);
                                row.push(values.return);
                                row.push(values.intercept);
                                row.push(values.on_hold_for_sc);
                                row.push(values.pending);
                                row.push(values.already_updated);
                                row.push(values.unresponsive_return);
                                row.push(values.productivity);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var total_shipments = 0;
            var reattempt_shipments = 0;
            var dbf_shipments = 0;
            var shipment_remarks = {};
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'RCP Agent Productivity',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'
                ],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.return.rcp_agent.list') }}',
                    data: function (d) {
                        d.agent = $('#search_agent').val();
                        d.hub = $('#search_hub').val();
                        d.category = $('#search_category').val();
                        // d.shipper = $('#search_shipper').val();
                        d.from_date = $('#search_form input[name="from_date_formatted"]').val();
                        d.to_date = $('#search_form input[name="to_date_formatted"]').val();
                    }
                },
                order: [[3, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'agent_name', name: 'a.name', class: 'align-middle agent_name'},
                    {data: 'agent_category', orderable: false, name: 'e.staff_category_id', class: 'align-middle agent_category'}, 
                    {data: 'start_time', name: 'start_time', class: 'align-middle start_time'},
                    {data: 'end_time', name: 'end_time', class: 'align-middle end_time'},
                    {data: 'total_assigning', orderable: false, searchable: false, name: 'total_assigning', class: 'align-middle total_assigning'},
                    {data: 'actual_productivity', orderable: false, searchable: false, name: 'actual_productivity', class: 'align-middle actual_productivity'},
                    {data: 'reattempt', orderable: false, searchable: false, name: 'reattempt', class: 'align-middle reattempt'},
                    {data: 'return', orderable: false, searchable: false, name: 'return', class: 'align-middle return'},
                    {data: 'intercept', orderable: false, searchable: false, name: '', class: 'align-middle intercept'},
                    {data: 'on_hold_for_sc', orderable: false, searchable: false, name: 'on_hold_for_sc', class: 'align-middle pending'},
                    {data: 'pending', orderable: false, searchable: false, name: 'pending', class: 'align-middle pending'},
                    {data: 'already_updated', orderable: false, searchable: false, name: '', class: 'align-middle already_updated'}, 
                    {data: 'unresponsive_return', orderable: false, searchable: false, name: 'unresponsive_return', class: 'align-middle unresponsive_return'}, 
                    {data: 'productivity', orderable: false, searchable: false, name: 'productivity', class: 'align-middle productivity'},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    if(index == 0){
                    total_shipments = data.total_assigning;
                    completed_shipments = data.actual_productivity;
                    reattempt_shipments = data.reattempt;
                    total_return_confirm = data.total_return_confirm;
                    total_mark_for_self_collection = data.total_mark_for_self_collection;
                    total_unresponsive_in_percent = data.total_unresponsive_in_percent;
                    total_intercept = data.intercept;
                    unresponsive_return = data.unresponsive_return;
                    on_hold_for_sc = data.on_hold_for_sc;
                    }
                    else{
                    total_shipments += data.total_assigning;
                    completed_shipments += data.actual_productivity;
                    reattempt_shipments += data.reattempt;
                    total_return_confirm += data.total_return_confirm;
                    total_unresponsive_in_percent += data.total_unresponsive_in_percent;
                    total_mark_for_self_collection += data.total_mark_for_self_collection;
                    total_intercept += data.total_intercept;
                    unresponsive_return += data.unresponsive_return;
                    on_hold_for_sc += data.on_hold_for_sc;

                    }
                    
                    if(index == (info.end - 1)){
                        $('#total').text(total_shipments);
                        $('#completed').text(completed_shipments);
                        $('#productivity').text(((completed_shipments/total_shipments)*100).toFixed(2));
                        $('#total_return_confirm').text(total_return_confirm);
                        $('#total_mark_for_self_collection').text(total_mark_for_self_collection);
                        $('#total_intercept').text(total_intercept);
                        $('#rcp_reattempt').text(reattempt_shipments);
                        $('#unresponsive_return').text(unresponsive_return);
                        $('#total_unresponsive_in_percent').text(((unresponsive_return/total_shipments)*100).toFixed(2));
                        $('#on_hold_for_sc').text(on_hold_for_sc);
                    }
                },
                initComplete: function() {
                var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                this.api().columns().every(function(column_id) {
                    var column = this;
                    var header = column.header();
                    //for removing search filter from columns
                    if ($(header).is('.serial_number') || $(header).is('.total_assigning')  || $(header).is('.actual_productivity') 
                    || $(header).is('.reattempt') || $(header).is('.return') || $(header).is('.intercept') || $(header).is('.pending') 
                    || $(header).is('.productivity')  || $(header).is('.un_assigned') || $(header).is('.already_updated') || $(header).is('.unresponsive_return') ) {
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
            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                        $('#total').text(0);
                        $('#completed').text(0);
                        $('#rcp_reattempt').text(0);
                        $('#productivity').text(0);
                        $('#total_return_confirm').text(0);
                    table.draw(true);
                }
            });
        });
    </script>
@endsection
