@extends('admin.layout.master')

@section('title', 'Employee Payslips')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Employee Payslips
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="payslip_upload_form" class="form-horizontal" method="POST" action="{{ route('admin.human_resource.payslip.excel') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="file" name="payslip" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group input-group ml-1">

                                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                                </span>
                                            </div>
                                            <input type="text" name="payslip_month" class="form-control bg-primary border-primary white rounded-right" id="payslip_month" placeholder="Payslip Month" data-rule-required="true" data-msg-required="Payslip Month is required">

                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>

                                    <div class="col ml-auto">
                                        <div class="form-group text-right">
                                            <a href="{{ asset('file/Employee Payslip Template.xlsx') }}?v=14_09_2021" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <form id="search_form" class="form-inline mb-1 row" novalidate="novalidate">

                                <div class="col-3">
                                    <div class="form-group input-group ml-1">

                                        <div class="input-group-prepend">
                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                                </span>
                                        </div>
                                        <input type="text" name="search_payslip_month" class="form-control bg-primary border-primary white rounded-right" id="search_payslip_month" placeholder="Search Payslip Month" data-rule-required="true" data-msg-required="Payslip Month is required">

                                    </div>
                                </div>

                                <div class="form-group col-2 mt-2">
                                    <button id="datatable_filter_btn" type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search
                                    </button>
                                </div>
                            </form>
                            <table class="table table-stripped table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1"></th>
                                        <th class="border-primary border-darken-1">Payroll Month</th>
                                        <th class="border-primary border-darken-1">Emp. ID.</th>
                                        <th class="border-primary border-darken-1">Name</th>
                                        <th class="border-primary border-darken-1">Designation</th>
                                        <th class="border-primary border-darken-1">Department</th>
                                        <th class="border-primary border-darken-1">Hub</th>
                                        <th class="border-primary border-darken-1">Zone</th>
                                        <th class="border-primary border-darken-1">Date of Joining</th>
                                        <th class="border-primary border-darken-1">CNIC</th>
                                        <th class="border-primary border-darken-1">Total Deduction</th>
                                        <th class="border-primary border-darken-1">Total Salary</th>
                                        <th class="border-primary border-darken-1">Net Salary</th>
                                        <th class="border-primary border-darken-1">IBAN / Account No.</th>
                                        <th class="border-primary border-darken-1">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
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
        #payslip_month_table , #search_payslip_month_table {
            display:none;
        }

    </style>
@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var max = '{{ Carbon\Carbon::now() }}';

            var payslip_month = $('#payslip_upload_form #payslip_month').pickadate({
                firstDay: 1,
                disable:[true,1],
                clear: '',
                today:'Select Current Month',
                max: max,
                format:'mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#payslip_month_root').css('top','40px');
                    $('#payslip_month_root button.picker__button--today').removeAttr('disabled');
                },
                onSet: function(context) {

                    var from_month = $('#payslip_month_root .picker__select--month').val();
                    var from_year = $('#payslip_month_root .picker__select--year').val();
                    var payslip_month_selected = new Date(from_year,from_month, 1);

                    payslip_month.pickadate('picker').set('select', payslip_month_selected,{muted:true});

                }

            });

            var search_payslip_month = $('#search_form #search_payslip_month').pickadate({
                firstDay: 1,
                disable:[true,1],
                clear: '',
                today:'Select Current Month',
                max: max,
                format:'mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#search_payslip_month_root').css('top','40px');
                    $('#search_payslip_month_root button.picker__button--today').removeAttr('disabled');
                },
                onSet: function(context) {

                    var from_month = $('#search_payslip_month_root .picker__select--month').val();
                    var from_year = $('#search_payslip_month_root .picker__select--year').val();
                    var payslip_month_selected = new Date(from_year,from_month, 1);

                    search_payslip_month.pickadate('picker').set('select', payslip_month_selected,{muted:true});

                }

            });

            $('#payslip_upload_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                normalizer: function(value) {
                    return $.trim(value);
                },
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button[type=submit]').attr('disabled', 'disabled');

                    swal({
                        title: 'Please Wait!',
                        text: 'Payslips are being updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
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
                        url: '{{ route('admin.human_resource.payslip.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Payroll Month');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Designation');
                            head.push('Department');
                            head.push('Hub');
                            head.push('Zone');
                            head.push('Date of Joining');
                            head.push('CNIC');
                            head.push('Total Deduction');
                            head.push('Total Salary');
                            head.push('Net Salary');
                            head.push('IBAN');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.payroll_month);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.hub);
                                row.push(values.zone);
                                row.push(values.joining_date);
                                row.push(values.cnic);
                                row.push(values.total_deduction);
                                row.push(values.total_salary);
                                row.push(values.net_salary);
                                row.push(values.iban);

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
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Employee Payslips',
                        className:'btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },

                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                rowId: 'id',
                order: [[1, 'desc']],
                ajax: {
                    url: '{{ route('admin.human_resource.payslip.list') }}',
                    data: function (d) {
                        d.search_payslip_month = $('input[name="search_payslip_month_formatted"]').val();
                    }
                },
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'payroll_month', name: 'employee_payslips.payroll_month', class: 'align-middle payroll_month'},
                    {data: 'trax_id', name: 'employee_payslips.trax_id', class: 'align-middle trax_id'},
                    {data: 'name', name: 'employee_payslips.name', class: 'align-middle name'},
                    {data: 'designation', name: 'employee_payslips.designation', class: 'align-middle designation'},
                    {data: 'department', name: 'employee_payslips.department', class: 'align-middle department'},
                    {data: 'hub', name: 'employee_payslips.hub', class: 'align-middle hub'},
                    {data: 'zone', name: 'employee_payslips.zone', class: 'align-middle zone'},
                    {data: 'joining_date', name: 'employee_payslips.joining_date', class: 'align-middle joining_date'},
                    {data: 'cnic', name: 'employee_payslips.cnic', class: 'align-middle cnic'},
                    {data: 'total_deduction', name: 'employee_payslips.total_deduction', class: 'align-middle total_deduction'},
                    {data: 'total_salary', name: 'employee_payslips.total_salary', class: 'align-middle total_salary'},
                    {data: 'net_salary', name: 'employee_payslips.net_salary', class: 'align-middle net_salary'},
                    {data: 'iban', name: 'employee_payslips.iban', class: 'align-middle iban'},
                    {data: 'action', name: 'action', class: 'align-middle action', orderable: false, searchable: false},
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

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
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
            })
            var route = '{!! url('admin') !!}';
             $('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
                var payslip_id = parseInt($(this).parents('tr').attr('id'));

                if ($(this).hasClass('print')) {
                    if(payslip_id){
                        $.ajax({
                            url: '{!! route('admin.human_resource.payslip.print') !!}',
                            method: 'POST',
                            data: {
                                'payslip_id': payslip_id,
                                '_token': '{{ csrf_token() }}'
                            }
                        }).done(function (data) {
                            if(data.status == 0){
                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                            }else{

                                toastr.success('Payslip downloaded successfully', 'Success!', {
                                    positionClass: 'toast-bottom-center',
                                    containerId: 'toast-bottom-center'
                                });

                                var tab = window.open('', '_blank');
                                tab.document.write('<iframe src="'+ data.image +'" width="100%" height="100%"></iframe>');
                                tab.document.close();
                                tab.focus();
                            }
                        });
                    }
                }

                if ($(this).hasClass('download')) {
                    if(payslip_id){
                        var route = '{!! route('admin.human_resource.payslip.download', ':id') !!}';
                        route = route.replace(':id', payslip_id);
                        window.open(route,'_black');
                    }
                }
            });

            $('#search_form').bind('submit',function (e) {
                e.preventDefault();

                table.draw();


            });

        });
    </script>
@endsection