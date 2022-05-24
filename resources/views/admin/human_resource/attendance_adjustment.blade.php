@extends('admin.layout.master')

@section('title', 'Employee Attendance Adjustments')

@section('content')
    <h1>Employee Attendance Adjustments</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 ">
                                    <form id="search_form" class="form-inline mb-1 justify-content-center" novalidate="novalidate">
                                        <div class="col-6 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_admin" id="search_admin" class="form-control select2">
                                                    @foreach($admins as $admin)
                                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-6 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_rider" id="search_rider" class="form-control select2">
                                                    @foreach($riders as $rider)
                                                        <option value="{{$rider->id}}">{{$rider->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_trax_id" id="search_trax_id" class="form-control select2">
                                                    @foreach($trax_ids as $trax_id)
                                                        <option value="{{$trax_id}}">{{$trax_id}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_cnic" id="search_cnic" class="form-control select2">
                                                    @foreach($cnics as $cnic)
                                                        <option value="{{$cnic}}">{{$cnic}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
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

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Adjustment ID</th>
                                    <th class="border-primary border-darken-1">Employee ID</th>
                                    <th class="border-primary border-darken-1">Employee Name</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Employee Type</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Availed Adjustment</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Reason</th>
                                    <th class="border-primary border-darken-1">Reject Reason</th>
                                    <th class="border-primary border-darken-1">Date</th>
                                    <th class="border-primary border-darken-1">Request Date</th>
                                    <th class="border-primary border-darken-1">Updated By</th>
                                    <th class="border-primary border-darken-1">Updated At</th>
                                    <th class="border-primary border-darken-1"></th>
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

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>



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
            $('#search_trax_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Employee ID',
                width:'100%',
                allowClear:true
            });
            $('#search_cnic').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search CNIC',
                width:'100%',
                allowClear:true
            });

            jQuery.fn.DataTable.Api.register('buttons.exportData()', function (options) {
                if (this.context.length) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.human_resource.adjustment.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Adjustment ID');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Designation');
                            head.push('Department');
                            head.push('Employee Type');
                            head.push('CNIC');
                            head.push('Availed Adjustment');
                            head.push('Status');
                            head.push('Reason');
                            head.push('Reject Reason');
                            head.push('Date');
                            head.push('Requested Date');
                            head.push('Updated By');
                            head.push('Updated At');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.adjustment_id);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.employee_type);
                                row.push(values.cnic);
                                row.push(values.adjustment_count);
                                row.push(values.status);
                                row.push(values.applied_reason);
                                row.push(values.reject_reason);
                                row.push(values.date);
                                row.push(values.requested);
                                row.push(values.updated_by);
                                row.push(values.updated);
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
                        title: 'Employee Attendance Adjustments',
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
                    url: '{{ route('admin.human_resource.adjustment.list') }}',
                    data: function (d) {

                        d.search_admin = $('#search_admin').val();
                        d.search_rider = $('#search_rider').val();
                        d.search_trax_id = $('#search_trax_id').val();
                        d.search_cnic = $('#search_cnic').val();
                    }
                },
                order: [[1, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    {data: 'adjustment_id', name: 'employee_attendance_adjustments.id', class: 'align-middle adjustment_id'},
                    {data: 'trax_id', name: 'a.trax_id', class: 'align-middle trax_id', searchable: false},
                    {data: 'name', name: 'a.name', class: 'align-middle name', searchable: false},
                    {data: 'designation', name: 'a.designation', class: 'align-middle designation'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {data: 'employee_type', name: 'employee_attendance_adjustments.employee_type_id', class: 'align-middle employee_type'},
                    {data: 'cnic', name: 'a.cnic', class: 'align-middle cnic', searchable: false},
                    {data: 'adjustment_count', name: 'ls.name', class: 'align-middle adjustment_count', orderable: false, searchable: false},
                    {data: 'status', name: 'ls.id', class: 'align-middle status'},
                    {data: 'applied_reason', name: 'employee_attendance_adjustments.applied_reason', class: 'align-middle applied_reason', orderable: false},
                    {data: 'reject_reason', name: 'employee_attendance_adjustments.rejected_reason', class: 'align-middle reject_reason', orderable: false},
                    {data: 'date', name: 'employee_attendance_adjustments.date', class: 'align-middle date'},
                    {data: 'requested', name: 'employee_leaves.created_at', class: 'align-middle requested'},
                    {data: 'updated_by', name: 'u.name', class: 'align-middle updated_by'},
                    {data: 'updated', name: 'employee_leaves.updated_at', class: 'align-middle updated'},
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var status_filter = '<select name="status_filter" id="status_filter" class="select2 form-control"></select>';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var employee_type = '<select name="employee_type" id="employee_type" class="select2 form-control">' +
                        '<option value="1">Staff</option>' +
                        '<option value="2">Rider</option>' +
                        '</select>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.days') || $(header).is('.leave_count') || $(header).is('.name') || $(header).is('.trax_id') || $(header).is('.cnic')) {
                            $(td).appendTo($(search));
                        }
                        else if ($(header).is('.employee_type')) {
                            $(employee_type).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else if ($(header).is('.status')) {
                            $(status_filter).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function () {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    $('#employee_type').prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Employee Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data = $.map({!! $leave_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#status_filter').prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
                table.draw(true);
            });
        });
    </script>

@endsection