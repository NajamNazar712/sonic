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

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr class="bg-primary white">
                                    <th class="border-primary border-darken-1">S No.</th>
                                    <th class="border-primary border-darken-1">Trax ID</th>
                                    <th class="border-primary border-darken-1">Name</th>
                                    <th class="border-primary border-darken-1">Designation</th>
                                    <th class="border-primary border-darken-1">Department</th>
                                    <th class="border-primary border-darken-1">Employee Type</th>
                                    <th class="border-primary border-darken-1">CNIC</th>
                                    <th class="border-primary border-darken-1">Availed Leaves</th>
                                    <th class="border-primary border-darken-1">Status</th>
                                    <th class="border-primary border-darken-1">Leave From</th>
                                    <th class="border-primary border-darken-1">Leave TO</th>
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
    <div class="modal fade text-left" id="addDepartmentModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="addDepartmentModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Add Department</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.department.add')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="addDepartmentForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Department Name*" data-rule-required="true" data-msg-required="Name is required">
                        </div>
                        <div class="form-group">
                            <textarea name="description" class="form-control" id="description" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="add" class="btn btn-primary add" value="Add">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="editLeaveModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="editLeaveModal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel8">Edit Leave</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form action="{{route('admin.human_resource.leave.edit')}}" class="form-horizontal mb-1 justify-content-center" method="POST" id="editLeaveForm" novalidate="novalidate">
                        {{csrf_field()}}
                        <input type="hidden" name="leave_id" id="leave_id" value="">
                        <div class="form-group">
                            <input type="text" name="name" id="edit_name" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <input type="text" name="trax_id" id="edit_trax_id" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <input type="text" name="designation" id="edit_designation" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <input type="text" name="department" id="edit_department" class="form-control" readonly>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <input type="text" name="from" id="edit_from" class="form-control" data-rule-required="true" data-msg-required="From Date is required">
                            </div>
                            <div class="form-group">
                                <input type="text" name="to" id="edit_to" class="form-control" data-rule-required="true" data-msg-required="To Date is required">
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea name="reason" class="form-control" id="edit_reason" placeholder="Reason" readonly></textarea>
                        </div>
                        <div class="form-group ml-1">
                            <button type="submit" name="edit" class="btn btn-primary" value="edit">Edit & Approve</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"
            type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/textarea/autosize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}"
            type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
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
                            head.push('Trax ID');
                            head.push('Name');
                            head.push('Designation');
                            head.push('Department');
                            head.push('Employee Type');
                            head.push('CNIC');
                            head.push('Availed Leaves');
                            head.push('Status');
                            head.push('Leave From');
                            head.push('Leave To');
                            head.push('Requested Date');
                            head.push('Updated By');
                            head.push('Updated At');

                            $.each(result.data, function (index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.designation);
                                row.push(values.department);
                                row.push(values.employee_type);
                                row.push(values.cnic);
                                row.push(values.leave_count);
                                row.push(values.status);
                                row.push(values.from);
                                row.push(values.to);
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
                ajax: '{{ route('admin.human_resource.leave.list') }}',
                order: [[11, 'desc']],
                rowId: 'id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) { return''; }
                    },
                    {data: 'trax_id', name: 'a.trax_id', class: 'align-middle trax_id'},
                    {data: 'name', name: 'a.name', class: 'align-middle name'},
                    {data: 'designation', name: 'a.designation', class: 'align-middle designation'},
                    {data: 'department', name: 'ad.name', class: 'align-middle department'},
                    {data: 'employee_type', name: 'employee_leaves.employee_type_id', class: 'align-middle employee_type'},
                    {data: 'cnic', name: 'a.cnic', class: 'align-middle cnic'},
                    {data: 'leave_count', name: 'ls.name', class: 'align-middle leave_count', orderable: false, searchable: false},
                    {data: 'status', name: 'ls.name', class: 'align-middle status'},
                    {data: 'from', name: 'employee_leaves.from', class: 'align-middle from'},
                    {data: 'to', name: 'employee_leaves.to', class: 'align-middle to'},
                    {data: 'requested', name: 'employee_leaves.created_at', class: 'align-middle requested'},
                    {data: 'updated_by', name: 'u.name', class: 'align-middle updated_by'},
                    {data: 'updated', name: 'employee_leaves.updated_at', class: 'align-middle updated'},
                    {data: 'action', name: 'action', class: 'align-middle text-center action', orderable: false, searchable: false}
                ],
                rowCallback: function (row, data, index) {
                    var info = table.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function () {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    this.api().columns().every(function (column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') || $(header).is('.action')) {
                            $(td).appendTo($(search));
                        }else {
                            var current = $(input).appendTo($(search)).on('change', function () {
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
            $('#addDepartmentModal').on('hide.bs.modal', function () {
                $('#name').val('');
                $('#description').val('');
            });

            $('body').on('click', '.edit', function (e) {
                var id = $(this).data('target-id');
                var name = table.row($(this).parents('tr')).data().name;
                var trax_id = table.row($(this).parents('tr')).data().trax_id;
                var designation = table.row($(this).parents('tr')).data().designation;
                var department = table.row($(this).parents('tr')).data().department;
                var from = table.row($(this).parents('tr')).data().from;
                var to = table.row($(this).parents('tr')).data().to;
                var reason = table.row($(this).parents('tr')).data().applied_reason;
                $('#leave_id').val(id);
                $('#edit_name').val(name);
                $('#edit_trax_id').val(trax_id);
                $('#edit_department').val(department);
                $('#edit_designation').val(designation);
                $('#edit_from').val(from);
                $('#edit_to').val(to);
                $('#edit_reason').val(reason);
                // $('#edit_head_list').val(head_id).trigger('change');
                $('#editDepartmentModal').modal('show');
            });

            $("#addDepartmentForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Department is being added!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
            $("#editDepartmentForm").validate({
                errorClass: "danger",
                errorPlacement: function (error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function (form) {
                    swal({
                        title: 'Please Wait!',
                        text: 'Department is being Updated!',
                        icon: 'info',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    form.submit();
                }
            });
        });
    </script>

@endsection