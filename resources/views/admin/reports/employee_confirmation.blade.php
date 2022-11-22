@extends('admin.layout.master')

@section('title', 'Employee Confirmation Report')

@section('content')
    <h1 class="mb-1">
        Employee Confirmation Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div class="row mb-2 justify-content-center">


                    
                    <div class="col-5">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From" >
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To" >
                        </div>
                    </div>
                   
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Confirmation ID</th>
                        <th class="border-primary border-darken-1">Trax ID</th>
                        <th class="border-primary border-darken-1">Name</th>
                        <th class="border-primary border-darken-1">Designation</th>
                        <th class="border-primary border-darken-1">Department</th>
                        <th class="border-primary border-darken-1">HUB</th>
                        <th class="border-primary border-darken-1">Zone</th>
                        <th class="border-primary border-darken-1">Initiated Date</th>
                        <th class="border-primary border-darken-1">Line Manager Approval Date</th>
                        <th class="border-primary border-darken-1">HOD Approval Date</th>
                        <th class="border-primary border-darken-1">Action Completion Date</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

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
        
        
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
           

            var today = '{{ Carbon\Carbon::today() }}';
            var next_month = '{{ Carbon\Carbon::today()->addMonths(1) }}';
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
                    to_date.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    to_date.pickadate('picker').set('select', new Date(current.toDate()),{muted:true});
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                max : new Date(next_month),
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
               
            });



            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();

                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.employee_confirmation.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Confirmation ID');
                            head.push('Trax ID');
                            head.push('Name');
                            head.push('Designation');
                            head.push('Department');
                            head.push('HUB');
                            head.push('Zone');
                            head.push('Initiated Date');
                            head.push('Line Manager Approval Date');
                            head.push('HOD Approval Date');
                            head.push('Action Completion Date');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.id);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.employee_hub);
                                row.push(values.zone);
                                row.push(values.created_at);
                                row.push(values.approve_by_lm_at);
                                row.push(values.approve_by_hod_at);
                                row.push(values.approve_by_hr_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });


                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: false  , scrollY: false,
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Employee Confirmation Report',
                        className: 'btn btn-primary excel',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.employee_confirmation.list') }}',
                    data: function (d) {
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                rowId: 'id',
                order: [[7, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'id', name: 'employee_confirmation.id', class: 'align-middle id', searchable: false},
                    {data: 'trax_id', name: 'a.trax_id', class: 'align-middle trax_id', searchable: false},
                    {data: 'name', name: 'a.name', class: 'align-middle name'},
                    {data: 'designation', name: 'ed.name', class: 'align-middle designation'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {data: 'employee_hub', name: 'employee_hub', class: 'align-middle employee_hub'},
                    {data: 'zone', name: 'ez.name', class: 'align-middle zone'},
                    {data: 'created_at', name: 'employee_confirmation.created_at', class: 'align-middle created_at'},
                    {data: 'approve_by_lm_at', name: 'employee_confirmation.approve_by_lm_at', class: 'align-middle approve_by_lm_at'},
                    {data: 'approve_by_hod_at', name: 'employee_confirmation.approve_by_hod_at', class: 'align-middle approve_by_hod_at'},
                    {data: 'approve_by_hr_at', name: 'employee_confirmation.approve_by_hr_at', class: 'align-middle approve_by_hr_at'},

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