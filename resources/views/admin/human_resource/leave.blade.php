@extends('admin.layout.master')

@section('title', 'Employee Leaves')

@section('content')
    <h1>Employee Leaves</h1>

    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @include('admin.inc.messages')

                            <div class="row mb-2 justify-content-center">
                                <div class="col-12 ">
                                    <form id="search_form" class="form-inline mb-1 justify-content-center"
                                          novalidate="novalidate">
                                        <div class="col-6 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_admin" id="search_admin"
                                                        class="form-control select2">
                                                    @foreach($admins as $admin)
                                                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-6 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_rider" id="search_rider"
                                                        class="form-control select2">
                                                    @foreach($riders as $rider)
                                                        <option value="{{$rider->id}}">{{$rider->name}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_trax_id" id="search_trax_id"
                                                        class="form-control select2">
                                                    @foreach($trax_ids as $trax_id)
                                                        <option value="{{$trax_id}}">{{$trax_id}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-5 mt-1">
                                            <fieldset class="form-group">
                                                <select name="search_cnic" id="search_cnic"
                                                        class="form-control select2">
                                                    @foreach($cnics as $cnic)
                                                        <option value="{{$cnic}}">{{$cnic}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-2 mt-1">
                                            <div class="form-group">
                                                <button type="button" id="search_filter_btn"
                                                        class="btn btn-outline-info btn-min-width"><i
                                                            class="la la-search"></i>
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
                                    <th class="border-primary border-darken-1">Leave ID</th>
                                    <th class="border-primary border-darken-1">Employee ID</th>
                                    <th class="border-primary border-darken-1">Employee Name</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Employee Type</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Availed Leaves</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Leave Purpose</th>
                                    <th class="border-primary border-darken-1">Leave Type</th>
                                    <th class="border-primary border-darken-1">Reject Reason</th>
                                    <th class="border-primary border-darken-1">Leave From</th>
                                    <th class="border-primary border-darken-1">Leave TO</th>
                                    <th class="border-primary border-darken-1">Days</th>
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
    <div class="modal fade" id="leave_request" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="leave_request" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_mapping_title">Add Leave Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="track_form"  action="{{route('admin.human_resource.leave.leave_request')}}"
                          method="post" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
                        @csrf
                        <input type="text" name="admin_id" value="{{auth()->id()}}" hidden>
                        <div class="row mb-2 justify-content-center">
                            <div class="col-6 mt-1">

                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                    </div>
                                    <input type="text" name="requested_from_date"
                                           class="form-control bg-primary border-primary white rounded-right"
                                           id="requested_from_date" placeholder="From" aria-required="true" data-rule-required="true" data-msg-required="Date is required">
                                    <label id="requested_from_date-error" class="danger w-100" for="requested_from_date"></label>
                                </div>

                            </div>
                            <div class="col-6 mt-1">

                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                    </div>
                                    <input type="text" name="requested_to_date"
                                           class="form-control bg-primary border-primary white rounded-right required"
                                           id="requested_to_date" placeholder="Till" aria-required="true" data-rule-required="true" data-msg-required="Date is required">
                                    <label id="requested_to_date-error" class="danger w-100" for="requested_to_date"></label>
                                </div>

                            </div>
                            <div class="col-6">

                                <textarea class="form-control" placeholder="Reasons of Leaves"
                                       name="leave_request_reason"
                                       id="leave_request_reason" data-rule-required="true" data-msg-required="Reason is required"></textarea>
                                <label id="leave_request_reason-error" class="danger w-100" for="leave_request_reason"></label>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select name="leave_type" id="leave_type" class="form-control" data-rule-required="true" data-msg-required="Leave Type is required">
                                        @foreach ($leave_types as $leave_type)
                                            <option value="{{$leave_type->id}}">{{$leave_type->name}}</option>
                                        @endforeach
                                    </select>
                                    <label id="leave_type-error" class="danger w-100" for="leave_type"></label>

                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit"
                                        class="btn btn-outline-primary btn-min-width mt-1">
                                             Submit
                                </button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade text-left" id="editLeaveModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="editLeaveModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Edit Leave</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.leave.edit')}}"
                          class="form-horizontal mb-1 justify-content-center" method="POST" id="editLeaveForm"
                          novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="leave_id" id="leave_id" value="">
                        <div class="row mb-2 justify-content-center">
                           
                            <div class="col-6 mt-1">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                    </div>
                                    <input type="text" name="edit_from"
                                           class="form-control bg-primary border-primary white rounded-right"
                                           id="edit_from" placeholder="From" aria-required="true" data-rule-required="true" data-msg-required="Date is required">
                                    <label id="edit_from-error" class="danger w-100" for="edit_from"></label>
                                </div>
                            </div>

                            <div class="col-6 mt-1">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o small-calender-icon"></span>
                                                    </span>
                                    </div>
                                    <input type="text" name="edit_to"
                                           class="form-control bg-primary border-primary white rounded-right required"
                                           id="edit_to" placeholder="Till" aria-required="true" data-rule-required="true" data-msg-required="Date is required">
                                    <label id="edit_to-error" class="danger w-100" for="edit_to"></label>
                                </div>
                            </div>
                            <div class="col-6">
                                <textarea class="form-control" placeholder="Reasons of Leaves"
                                       name="reason"
                                       id="edit_reason" data-rule-required="true" data-msg-required="Reason is required"></textarea>
                                <label id="edit_reason-error" class="danger w-100" for="edit_reason"></label>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select name="edit_leave_type" id="edit_leave_type" class="form-control" data-rule-required="true" data-msg-required="Leave Type is required">
                                        @foreach ($leave_types as $leave_type)
                                            <option value="{{$leave_type->id}}">{{$leave_type->name}}</option>
                                        @endforeach
                                    </select>
                                    <label id="edit_leave_type-error" class="danger w-100" for="edit_leave_type"></label>

                                </div>
                            </div>
                          

                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary btn-min-width" value="edit">Edit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="rejectLeaveModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="rejectLeaveModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Reject Leave</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.leave.reject')}}"
                          class="form-horizontal mb-1 justify-content-center" method="POST" id="rejectLeaveForm"
                          novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="leave_id" id="reject_leave_id" value="">
                        <div class="form-group">
                            <textarea name="reason" class="form-control" id="reject_reason" placeholder="Reason"
                                      rows="6" maxlength="255"></textarea>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="reject" class="btn btn-primary" value="reject">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="approveLeaveModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="approveLeaveModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Approve Leave</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.leave.approve')}}"
                          class="form-horizontal mb-1 justify-content-center" method="POST" id="approveLeaveForm"
                          novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="leave_id" id="approve_leave_id" value="">
                        <input type="hidden" name="line_manager" id="line_manager_approve_leave_id" value="0">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="hod_approveLeaveModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="hod_approveLeaveModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Approve Leave</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.leave.hod_approve')}}"
                          class="form-horizontal mb-1 justify-content-center" method="POST" id="hod_approveLeaveForm"
                          novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="leave_id" id="hod_approve_leave_id" value="">
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

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

    {{--    todo date filter field--}}
    <script>
        var booking_from_date = $('#requested_from_date').pickadate({
            // max: '{{ Carbon\Carbon::today()->month(8)->endOfMonth() }}',
            min: '{{ Carbon\Carbon::now()}}',
            format: 'yyyy-mm-dd',
            firstDay: 1,
            clear: '',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            onSet: function(context) {
                if (context.select) {
                    $('#requested_to_date').pickadate('picker').set('min', $('#requested_from_date').pickadate('picker').get('select'));
                }
            }
        });

        var booking_to_date = $('#requested_to_date').pickadate({
            firstDay: 1,
            clear: '',
            // max: '{{ Carbon\Carbon::today()->month(8)->endOfMonth() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd 23:59:59',
        });
    </script>
    {{--    todo date filter field end--}}

    <script type="text/javascript">
        $(document).ready(function () {
            $('#search_admin').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Staff',
                width: '100%',
                allowClear: true
            });
            $('#search_rider').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Rider',
                width: '100%',
                allowClear: true
            });
            $('#search_trax_id').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search Employee ID',
                width: '100%',
                allowClear: true
            });
            $('#search_cnic').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Search CNIC',
                width: '100%',
                allowClear: true
            });
            $('#leave_type').select2({
                placeholder: 'Select Leave Type',
                width: '100%',
                allowClear: true
            });
            $('#edit_leave_type').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Select Leave Type',
                width: '100%',
                allowClear: true
            });
            
            
            var from_date = $('#editLeaveForm #edit_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                min: '{{ Carbon\Carbon::now()}}',

                onSet: function (context) {
                    if (context.select) {
                        $('#editLeaveForm #edit_to').pickadate('picker').set('min', $('#editLeaveForm #edit_from').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#editLeaveForm #edit_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function (context) {
                    if (context.select) {
                        $('#editLeaveForm #edit_to').pickadate('picker').set('min', $('#editLeaveForm #edit_from').pickadate('picker').get('select'));
                        $('#editLeaveForm #edit_from').pickadate('picker').set('max', $('#editLeaveForm #edit_to').pickadate('picker').get('select'));
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
                        url: '{{ route('admin.human_resource.leave.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Leave ID');
                            head.push('Employee ID');
                            head.push('Employee Name');
                            head.push('Designation');
                            head.push('Department');
                            head.push('Employee Type');
                            head.push('CNIC');
                            head.push('Availed Leaves');
                            head.push('Status');
                            head.push('Leave Purpose');
                            head.push('Leave Type');
                            head.push('Reject Reason');
                            head.push('Leave From');
                            head.push('Leave To');
                            head.push('Days');
                            head.push('Requested Date');
                            head.push('Updated By');
                            head.push('Updated At');re

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.leave_id);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.employee_type);
                                row.push(values.cnic);
                                row.push(values.leave_count);
                                row.push(values.status);
                                row.push(values.applied_reason);
                                row.push(values.leave_type);
                                row.push(values.reject_reason);
                                row.push(values.from);
                                row.push(values.to);
                                row.push(values.days);
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
                        text: 'Add Leave Request',
                        className: 'btn btn-primary leave_request_btn',
                        enabled: true,
                        action: function (e, dt, node, config) {
                            $('#leave_request').modal('show');
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'Employee Leaves',
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
                    url: '{{ route('admin.human_resource.leave.list') }}',
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
                    {data: 'leave_id', name: 'employee_leaves.id', class: 'align-middle leave_id'},
                    {data: 'trax_id', name: 'e.trax_id', class: 'align-middle trax_id', searchable: false},
                    {data: 'name', name: 'e.name', class: 'align-middle name', searchable: false},
                    {data: 'designation', name: 'ed.name', class: 'align-middle designation'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {
                        data: 'employee_type',
                        name: 'employee_leaves.employee_type_id',
                        class: 'align-middle employee_type'
                    },
                    {data: 'cnic', name: 'a.cnic', class: 'align-middle cnic', searchable: false},
                    {
                        data: 'leave_count',
                        name: 'ls.name',
                        class: 'align-middle leave_count',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'status', name: 'ls.id', class: 'align-middle status'},
                    {
                        data: 'applied_reason',
                        name: 'employee_leaves.applied_reason',
                        class: 'align-middle applied_reason',
                        orderable: false
                    },
                    {
                        data: 'leave_type',
                        name: 'lt.id',
                        class: 'align-middle leave_type',
                        orderable: false
                    },
                    
                    {
                        data: 'reject_reason',
                        name: 'employee_leaves.rejected_reason',
                        class: 'align-middle reject_reason',
                        orderable: false
                    },
                    {data: 'from', name: 'employee_leaves.from', class: 'align-middle from'},
                    {data: 'to', name: 'employee_leaves.to', class: 'align-middle to'},
                    {data: 'days', name: '', class: 'align-middle days', orderable: false, searchable: false},
                    {data: 'requested', name: 'employee_leaves.created_at', class: 'align-middle requested'},
                    {data: 'updated_by', name: 'u.name', class: 'align-middle updated_by'},
                    {data: 'updated', name: 'employee_leaves.updated_at', class: 'align-middle updated'},
                    {
                        data: 'action',
                        name: 'action',
                        class: 'align-middle text-center action',
                        orderable: false,
                        searchable: false
                    }
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
                    var leave_type_filter = '<select name="leave_type_filter" id="leave_type_filter" class="select2 form-control"></select>';
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
                        } else if ($(header).is('.employee_type')) {
                            $(employee_type).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.status')) {
                            $(status_filter).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else if ($(header).is('.leave_type')) {
                            $(leave_type_filter).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        } else {
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
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

                    var data = $.map({!! $leave_statuses !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $('#status_filter').prepend('<option value="" selected></option>').select2({
                        data: data,
                        placeholder: "Select Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data_leave_type = $.map({!! $leave_types !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });
                    $('#leave_type_filter').prepend('<option value="" selected></option>').select2({
                        data: data_leave_type,
                        placeholder: "Select Leave Type",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });


                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click', function () {
                table.draw(true);
            });

            $('body').on('click', '.edit', function (e) {
                var leave_type_id = $(this).attr('rel');
                var id = $(this).data('target-id');
                var from = table.row($(this).parents('tr')).data().from;
                var to = table.row($(this).parents('tr')).data().to;
                var reason = table.row($(this).parents('tr')).data().applied_reason;
                from_date.pickadate('picker').set('select', new Date(from));
                to_date.pickadate('picker').set('select', new Date(to));
                from_date.pickadate('picker').set('max', $('#editLeaveForm #edit_to').pickadate('picker').get('select'));
                $('#leave_id').val(id);
                $('#edit_reason').val(reason);
                $('#edit_leave_type').val(leave_type_id).trigger('change');
                
                $('#editLeaveModal').modal('show');
            });

            $('body').on('click', '.reject', function (e) {
                var id = $(this).data('target-id');
                $('#reject_leave_id').val(id);
                $('#rejectLeaveModal').modal('show');
            });

            $('#rejectLeaveModal').on('hide.bs.modal', function () {
                $('#reject_leave_id').val('');
                $('#reject_reason').val('');
            });

            


            $("#rejectLeaveForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Leave is being reject!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });

            $("#editLeaveForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Leave is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $('body').on('click', '.hod_approve', function (e) {
                var id = $(this).data('target-id');
                $('#hod_approveLeaveForm #hod_approve_leave_id').val(id);
                atext = "Select Yes to Approve Request!";
                swal({
                    title: 'Are You Sure?',
                    text: atext,
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $('#hod_approveLeaveForm').submit();
                    }
                });

            });

            $('body').on('click', '.approve', function (e) {
                var id = $(this).data('target-id');
                $('#approveLeaveForm #approve_leave_id').val(id);
                atext = "Select Yes to Approve Request!";
                swal({
                    title: 'Are You Sure?',
                    text: atext,
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $('#approveLeaveForm').submit();
                    }
                });

            });

            $('body').on('click', '.approve_by_line_manager', function (e) {
                var id = $(this).data('target-id');
                $('#approveLeaveForm #approve_leave_id').val(id);
                $('#approveLeaveForm #line_manager_approve_leave_id').val(1);
                atext = "Select Yes to Approve Request!";
                swal({
                    title: 'Are You Sure?',
                    text: atext,
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes',
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true
                }).then(function (confirm) {
                    if (confirm) {
                        $('#approveLeaveForm').submit();
                    }
                });

            });

            //todo : for calling buttons id
            $("#leave_request_btn").click(function () {
                $("#leave_request").modal('show');
            });

            $('#leave_request').on('hide.bs.modal', function () {
                $('#leave_request_reason').val('');
                $('#leave_type').val('').trigger('change');
                $('#requested_from_date').val('');
                $('#requested_to_date').val('');
                

            });
            $('#editLeaveModal').on('hide.bs.modal', function () {
                $('#edit_reason').val('');
                $('#edit_leave_type').val('').trigger('change');
                $('#edit_from').val('');
                $('#edit_to').val('');
                

            });
            
            //todo : for calling buttons id end

            //todo : leave request form submission
            $("#track_form").validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('form'));
                },
                submitHandler: function(form) {
                    form.submit()
                }
            });
            // $("#track_form").submit(function (e) {
            //     $(this).validate();
            //     e.preventDefault(); // prevent actual form
            //     // var requested_from_date = $('#requested_from_date').val();
            //     // var requested_to_date = $('#requested_to_date').val();
            //     // if(requested_from_date && requested_to_date) {
            //         var form = $(this);
            //         var url = form.attr('action'); //get submit url [replace url here if desired]
            //         $.ajax({
            //             type: "POST",
            //             url: url,
            //             data: form.serialize(), // serializes form input
            //         }).done(function (data) {
            //             // console.log(data);
            //             $('#leave_request').modal('hide');
            //
            //             if (data.status == 3) {
            //                 toastr.error(data.message, 'Denied!', {
            //                     positionClass: 'toast-top-center',
            //                     containerId: 'toast-top-center'
            //                 });
            //             } else if (data.status == 2) {
            //                 toastr.success(data.success, 'Success!', {
            //                     positionClass: 'toast-top-center',
            //                     containerId: 'toast-top-center'
            //                 });
            //             } else if (data.status == 1) {
            //                 toastr.error(data.error, 'Error!', {
            //                     positionClass: 'toast-top-center',
            //                     containerId: 'toast-top-center'
            //                 });
            //             } else if (data.status == 0) {
            //                 toastr.error(data.error, 'Error!', {
            //                     positionClass: 'toast-top-center',
            //                     containerId: 'toast-top-center'
            //                 });
            //             }
            //             $("#track_form")[0].reset();
            //             table.draw();
            //         });
            //     // }else{
            //     //     alert('Fill the dates');
            //     //
            //     // }
            // });
            //todo : leave request form submission end
        });
    </script>

@endsection