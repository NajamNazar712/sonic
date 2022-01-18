@extends('admin.layout.master')

@section('title', 'Employee Attendance')

@section('content')
    <h1>Employee Attendance</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <form id="attendance_upload_form" class="form-horizontal" method="POST" action="{{ route('admin.attendance.excel') }}" novalidate="novalidate" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row align-items-center justify-content-center">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="file" name="attendance" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group text-left">
                                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                        </div>
                                    </div>

                                    <div class="col ml-auto">
                                        <div class="form-group text-right">
                                            <a href="#" class="btn btn-primary generate_pdf"><i class="la la-download"></i> Generate PDF</a>
                                            <a href="{{ asset('file/Employee Attendance Template.xlsx') }}?v=14_09_2021" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 ">
                                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_admin" id="search_admin" class="form-control select2">
                                                    @foreach($admins as $admin)
                                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_rider" id="search_rider" class="form-control select2">
                                                    @foreach($riders as $rider)
                                                        <option value="{{$rider->id}}">{{$rider->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_city" id="search_city" class="form-control select2">
                                                    @foreach($cities as $city)
                                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_trax_id" id="search_trax_id" class="form-control select2">
                                                    @foreach($trax_ids as $trax_id)
                                                        <option value="{{$trax_id}}">{{$trax_id}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_department" id="search_department" class="form-control select2">
                                                    @foreach($departments as $department)
                                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        @if(session('role_id') == 1)
                                        <div class="col-4 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_cnic" id="search_cnic" class="form-control select2">
                                                    @foreach($cnics as $cnic)
                                                        <option value="{{$cnic}}">{{$cnic}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        @endif
                                        <div class="col-5 mt-1">
                                            <div class="form-group input-group ">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="search_date_from"
                                                       class="form-control pickadate bg-primary border-primary white rounded-right"
                                                       id="search_date_from" placeholder="Attandance Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                                            </div>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <div class="form-group input-group">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o"></span>
                                            </span>
                                                </div>
                                                <input type="text" name="search_date_to"
                                                       class="form-control pickadate bg-primary border-primary white rounded-right"
                                                       id="search_date_to" placeholder="Attandance Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                                            </div>
                                        </div>

                                        <div class="col-2 mt-1">
                                            <div class="form-group">
                                                <button type="button" id="search_filter_btn"
                                                        class="btn btn-outline-info btn-min-width"><i class="la la-search"></i>
                                                    Search
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="modal fade text-left" id="generateAttendancePdf" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="generateAttendancePdf"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="generate_pdf_heading">Select Details<span></span></h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body col-12" id="generatePdfDiv">
                                            <form id="generate_pdf_form" class="form-horizontal" method="POST" action="{{ route('admin.attendance.print') }}" novalidate="novalidate">
                                                {{ csrf_field() }}

                                                <div class="col mt-1">
                                                    <fieldset class="form-group">
                                                        <select name="pdf_trax_id" id="pdf_trax_id" class="form-control select2" data-rule-required="true" data-msg-required="Select Employee ID">
                                                            @foreach($trax_ids as $trax_id)
                                                                <option value="{{$trax_id}}">{{$trax_id}}</option>
                                                            @endforeach
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col mt-1">
                                                    <div class="form-group input-group ">
                                                        <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o"></span>
                                                </span>
                                                        </div>
                                                        <input type="text" name="pdf_date_from"
                                                               class="form-control pickadate bg-primary border-primary white rounded-right" data-rule-required="true" data-msg-required="This Field is required"
                                                               id="pdf_date_from" placeholder="Attandance Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                                                    </div>
                                                </div>
                                                <div class="col mt-1">
                                                    <div class="form-group input-group">
                                                        <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o"></span>
                                                </span>
                                                        </div>
                                                        <input type="text" name="pdf_date_to"
                                                               class="form-control pickadate bg-primary border-primary white rounded-right" data-rule-required="true" data-msg-required="This Field is required"
                                                               id="pdf_date_to" placeholder="Attandance Date (To)" data-value="{{ Carbon\Carbon::today() }}">
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <fieldset class="form-actions center">
                                                        <button type="submit" class="btn btn-primary">
                                                            Download
                                                        </button>
                                                    </fieldset>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Employee ID</th>
                                    <th class="border-primary border-darken-1">Employee Name</th>
                                    <th class="border-primary border-darken-1">Employee Shift</th>
                                    <th class="border-primary border-darken-1">Employee CNIC</th>
                                    <th class="border-primary border-darken-1">Hub</th>
                                    <th class="border-primary border-darken-1">Employee Type</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Date</th>
                                    <th class="border-primary border-darken-1">Day</th>
                                    <th class="border-primary border-darken-1">Clock-in Datetime</th>
                                    <th class="border-primary border-darken-1">Clock-in Radius</th>
                                    <th class="border-primary border-darken-1">Clock-in Location</th>
                                    <th class="border-primary border-darken-1">Clock-out Datetime</th>
                                    <th class="border-primary border-darken-1">Clock-out Radius</th>
                                    <th class="border-primary border-darken-1">Clock-out Location</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/simple-line-icons/style.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_admin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Staff',
                width:'100%',
                allowClear:true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Rider',
                width:'100%',
                allowClear:true
            });
            $('#search_city').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search City',
                width:'100%',
                allowClear:true
            });
            $('#search_department').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Department',
                width:'100%',
                allowClear:true
            });
            $('#search_trax_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Employee ID',
                width:'100%',
                allowClear:true
            });
            $('#pdf_trax_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Employee ID',
                width:'100%',
                allowClear:true,
                dropdownParent: $("#generate_pdf_form")
            });
            $('#search_cnic').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search CNIC',
                width:'100%',
                allowClear:true
            });
            var search_date_from = $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#generate_pdf_form').validate({
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
                        text: 'Pdf is being downloading',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            var search_date_to = $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            var pdf_date_from = $('#generate_pdf_form #pdf_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#generate_pdf_form #pdf_date_to').pickadate('picker').set('min', $('#generate_pdf_form #pdf_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            var pdf_date_to = $('#generate_pdf_form #pdf_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#generate_pdf_form #pdf_date_from').pickadate('picker').set('max', $('#generate_pdf_form #pdf_date_to').pickadate('picker').get('select'));
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
                        url: '{{ route('admin.attendance.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Shift');
                            head.push('Employee CNIC');
                            head.push('Hub');
                            head.push('Employee Type');
                            head.push('Designation');
                            head.push('Department');
                            head.push('Date');
                            head.push('Day');
                            head.push('Clock-in Time');
                            head.push('Clock-in Radius');
                            head.push('Clock-out Time');
                            head.push('Clock-out Radius');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.shift);
                                row.push(values.cnic);
                                row.push(values.city_name);
                                row.push(values.employee_type);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.attendance_date);
                                row.push(values.attendance_day);
                                row.push(values.clock_in);
                                row.push(values.clock_in_status);
                                row.push(values.clock_out);
                                row.push(values.clock_out_status);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Employee Attendance',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                    'reset'],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                autoWidth: false,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,

                ajax: {
                    url: '{{ route('admin.attendance.list') }}',
                    data: function (d) {

                        d.search_admin = $('#search_admin').val();
                        d.search_rider = $('#search_rider').val();
                        d.search_city = $('#search_city').val();
                        d.search_department = $('#search_department').val();
                        d.search_trax_id = $('#search_trax_id').val();
                        d.search_cnic = $('#search_cnic').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();

                    }
                },
                order: [[9, 'desc']],
                rowId: 'id',
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        name: 'serial_number',
                        class: 'align-middle serial_number',
                        targets: 0,
                        render: function (data, type, row) {
                            return '';
                        }
                    },
                    {data: 'trax_id', name: 'a.trax_id', class: 'align-middle trax_id'},
                    {data: 'name', name: 'a.name', class: 'align-middle name'},
                    {data: 'shift', name: 'aes.name', class: 'align-middle shift'},
                    {data: 'cnic', name: 'a.cnic', class: 'align-middle cnic'},
                    {data: 'city_name', name: 'city_name', class: 'align-middle city_name'},
                    {data: 'employee_type', name: 'c.id', class: 'align-middle employee_type'},
                    {data: 'designation', name: 'a.designation', class: 'align-middle designation'},
                    {data: 'department', name: 'ad.id', class: 'align-middle department'},
                    {data: 'attendance_date', name: 'employee_attendances.attendance_date', class: 'align-middle attendance_date'},
                    {data: 'attendance_day', name: 'employee_attendances.attendance_date', class: 'align-middle attendance_day'},
                    {data: 'clock_in', name: 'employee_attendances.clock_in', class: 'align-middle clock_in'},
                    {data: 'clock_in_status', name: 'employee_attendances.clock_in_location', class: 'align-middle clock_in_status'},
                    {data: 'clock_in_location', name: '', class: 'align-middle clock_in_location', sortable: false},
                    {data: 'clock_out', name: 'employee_attendances.clock_out', class: 'align-middle clock_out'},
                    {data: 'clock_out_status', name: 'employee_attendances.clock_out_location', class: 'align-middle clock_out_status'},
                    {data: 'clock_out_location', name: '', class: 'align-middle clock_out_location', sortable: false},
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    this.api().table().columns.adjust();
                }

            });

            $('#search_filter_btn').on('click',function () {
                table.draw(true);
            });

            $('.datatable tbody').on('click', 'tr td.select-checkbox', function () {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                } else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.sms').enable();
                } else {
                    table.button('.sms').disable();
                }
            });
            $('body').on('click','.generate_pdf',function(){
                $('#generateAttendancePdf').modal('show');
            });
        });
    </script>

@endsection