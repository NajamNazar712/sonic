@extends('admin.layout.master')
@section('title','New RCP Agent Productivity Shipment')


@section('content')
    <h1 class="mb-1">
        New RCP Agent Productivity Shipments
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
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper Name</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Destination</th>
                        <th class="border-primary border-darken-1">Consignee Name</th>
                        <th class="border-primary border-darken-1">Consignee Phone</th>
                        <th class="border-primary border-darken-1">Address</th>
                        <th class="border-primary border-darken-1">Collection Amount</th>
                        <th class="border-primary border-darken-1">Current Status</th>
                        <th class="border-primary border-darken-1">Current Status Date</th>
                        <th class="border-primary border-darken-1">Reason</th>
                        <th class="border-primary border-darken-1">Call Findings</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Assigned To</th>
                        <th class="border-primary border-darken-1">Updated By</th>
                        <th class="border-primary border-darken-1">Agent Status</th>
                        <th class="border-primary border-darken-1">Agent Status Date</th>
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
            $('#search_category').prepend('<option value="" selected></option>').select2({
                width:'100%',
                placeholder:"Select Category",
                allowClear:true,
            });
            var today = '{{ $today }}';
            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
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
            

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.return.rcp_agent_cn.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Tracking No.');
                            head.push('Shipper Name');
                            head.push('Origin');
                            head.push('Destination');
                            head.push('Consignee Name');
                            head.push('Consignee Phone');
                            head.push('Address');
                            head.push('Collection Amount');
                            head.push('Current Status');
                            head.push('Current Status Date');
                            head.push('Reason');
                            head.push('Call Findings');
                            head.push('Arrival Date');
                            head.push('Assigned To');
                            head.push('Updated By');
                            head.push('Agent Status');
                            head.push('Agent Status Date');


                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.agent_name);
                                row.push(values.shipper_name); 
                                row.push(values.origin);
                                row.push(values.consignee_city);
                                row.push(values.consignee_name);
                                row.push(values.consignee_phone_number);
                                row.push(values.consignee_address);
                                row.push(values.collection_amount);
                                row.push(values.current_status);
                                row.push(values.current_status_date);
                                row.push(values.reason);
                                row.push(values.call_findings);
                                row.push(values.arrival_date);
                                row.push(values.assigned_to);
                                row.push(values.updated_by);
                                row.push(values.agent_status);
                                row.push(values.agent_status_date);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var reattempt_shipments = 0;
            var dbf_shipments = 0;
            var shipment_remarks = {};
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'RCP Agent Productivity Shipment Wise',
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
                    url: '{{ route('admin.return.rcp_agent_cn.list') }}',
                    data: function (d) {
                        d.agent = $('#search_agent').val();
                        d.from_date = $('#search_form input[name="from_date_formatted"]').val();
                        d.to_date = $('#search_form input[name="to_date_formatted"]').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number', name: 's.tracking_number', class: 'align-middle tracking_number'},
                    {data: 'shipper_name', orderable: false, name: 'u.name', class: 'align-middle shipper_name'}, 
                    {data: 'origin', name: 'c.name', class: 'align-middle origin'},
                    {data: 'consignee_city', name: 'consignee_city.name', class: 'align-middle consignee_city'},
                    {data: 'consignee_name', orderable: false, name: 's.consignee_name', class: 'align-middle consignee_name'},
                    {data: 'consignee_phone_number', orderable: false, name: 's.consignee_phone_number_1', class: 'align-middle consignee_phone_number'},
                    {data: 'consignee_address', orderable: false, name: 's.consignee_address', class: 'align-middle consignee_address'},
                    {data: 'collection_amount', orderable: false, name: 's.amount', class: 'align-middle collection_amount'},
                    {data: 'current_status', orderable: false, name: 'ss.name', class: 'align-middle current_status'},
                    {data: 'current_status_date', orderable: false, name: 'sj.updated_at', class: 'align-middle current_status_date'},
                    {data: 'reason', orderable: false, name: 'ssr.name', class: 'align-middle reason'},
                    {data: 'call_findings', orderable: false, name: 'call_findings', class: 'align-middle call_findings'},
                    {data: 'arrival_date', orderable: false, name: 'sjj.updated_at', class: 'align-middle arrival_date'}, 
                    {data: 'assigned_to', orderable: false, name: 'a.name', class: 'align-middle assigned_to'}, 
                    {data: 'updated_by', orderable: false, name: 'updated_by', class: 'align-middle updated_by'},
                    {data: 'agent_status', orderable: false, name: 'agent_status', class: 'align-middle agent_status'},
                    {data: 'agent_status_date', orderable: false, name: 'ras.updated_at', class: 'align-middle agent_status_date'}, 

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    if(index == 0){
                    // console.log(data);
                    // on_hold_for_sc = data.on_hold_for_sc;
                    }
                    else{
                    // on_hold_for_sc += data.on_hold_for_sc;

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

                        if ($(header).is('.serial_number') || $(header).is('.productivity')  || $(header).is('.un_assigned')) {
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
                    table.draw(true);
                }
            });
        });
    </script>
@endsection
